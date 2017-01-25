<?php
/**
 * Zend Backend (http://forge.co.nz/)
 *
 * CustomersController
 *
 * PHP version 5
 *
 * @category Module
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
namespace FgBackups\Controller;

use FgHome\Controller\AdminController;
use Zend\Authentication\AuthenticationService;
use Interop\Container\ContainerInterface;
use Zend\Mvc\MvcEvent;
use Zend\view\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;
use Zend\Hydrator\HydratorInterface;
use FgCore\Interfaces\ConfigurationInterface;
use FgCore\Model\zbeMessage;
use Zend\Session\Container;
use FgVirtualhost\Model\Vhosts;
use FgBackups\Model\Backupdatabase;

/**
 * CustomersController Class
 *
 * @category Controller
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class BackupsController extends AdminController
{
	/*
	* @var Interop\Container\ContainerInterface
	*/
	protected $container;
	
	/**
	* @var \Zend\Stdlib\Hydrator\HydratorInterface
	*/
	protected $hydrator;
	
	/*
	* @var FgCore\Manager\ConfigurationManager
	*/
	protected $configurationManager;
	
	protected $vhosts;
	
	protected $dbconfig;
	
	public function __construct(
		ContainerInterface $container,
		HydratorInterface $hydrator,
		ConfigurationInterface $configurationModel
	) {
		$this->container = $container;
		$this->hydrator  = $hydrator;
		$this->configurationModel = $configurationModel;
	}
	
	public function indexAction()
	{
		$this->getAdminLayout();
		$layout = $this->layout();
		$layout->setTemplate('dashboard/index/layout');
	}
	
	/*
	* These action and view details are taken from 
	* FgVirtualhost model
	*/
	public function hostsAction()
	{
		$this->getAdminLayout();
		$this->getVhostData();
		$vhosts = $this->vhosts->getAllHosts();
		$this->getConfiguration();
        $configuration = $this->dbconfig->getConfiguration();
		$googleApi = $this->container->get('FgBackups\Service\GoogleApiFactory');
		$dbhostservice = $this->container->get('FgVirtualhost\Service\VhostsDBFactory');
		$view = new ViewModel(
            array(
				'vhosts' => $vhosts,
				'configuration' => $configuration,
				'googleapi' => $googleApi,
				'dbhostservice' => $dbhostservice
			)
        );
		return $view;
	}
	
	/*
	* These action and view details are taken from 
	* FgVirtualhost model
	*/
	public function databaseAction()
	{
		$this->getAdminLayout();
		$this->getVhostData();
		$vhosts = $this->vhosts->getAllHosts();
		$this->getConfiguration();
        $configuration = $this->dbconfig->getConfiguration();
		$googleApi = $this->container->get('FgBackups\Service\GoogleApiFactory');
		$dbhostservice = $this->container->get('FgVirtualhost\Service\VhostsDBFactory');
		$dbbackupService = $this->container->get('FgBackups\Service\BackupdatabaseService');
		$view = new ViewModel(
            array(
				'vhosts' => $vhosts,
				'configuration' => $configuration,
				'googleapi' => $googleApi,
				'dbhostservice' => $dbhostservice,
				'dbbackupservice' => $dbbackupService,
			)
        );
		return $view;
	}
	
	public function googleauthAction()
	{
		$message = new zbeMessage();
		$layout = $this->layout();
		$layout->setTemplate('login/process/layout');
		$code = trim( $this->getRequest()->getQuery()->code );
		if ($code) {
			$google = $this->container->get('FgBackups\Service\GoogleApiFactory');
			$this->getConfiguration();
			$configuration = $this->dbconfig->getConfiguration();	
			if ($configuration['googleapplicationname']) {
				$client = $google->updateAccessToken($code);
			}
		}
		return $this->redirect()->toRoute('backups/hosts');
	}
	
	public function vhostsaveAction()
	{
		$error = false;
		$layout = $this->layout();
		$layout->setTemplate('login/process/layout');

		$action = $this->getRequest()->getPost()->action;
		$vhost = $this->getRequest()->getPost()->vhost;
		$vhostData = $this->getRequest()->getPost()->hostingdata;
		$vhostId = $this->getRequest()->getPost()->vhostid;
		
		$this->getConfiguration();
		$configuration = $this->dbconfig->getConfiguration();	
		
		$message = new zbeMessage();
		if (! $action) {
			$error = true;
			$message->setError('Please select items and action');
			return false;
		} 
		if(empty($configuration["backupsgoogleparentdirectoryid"])) {
			$error = true;
			$message->setError('Please add Google Parent directory from configruation, so we can create backup location.');
		}

		if(!$error){
			if ($action && $action=='Create Backup Location') {
				$google = $this->container->get('FgBackups\Service\GoogleApiFactory');
				if ($configuration['googleapplicationname']) {
					$client = $google->getClient();
					if ($client) {
						try{
							$googlearray = $google->createBackupLocation($vhost);
							$vhostdb = $this->container->get('FgVirtualhost\Service\VhostsDBFactory');
							foreach($googlearray as $key => $value){
								$tmp = array(
									'name' => $key,
									'rootsize' => 0,
									'backups' => 0,
									'savebackup' => 0,
									'backupkey' => $value,
									'source' => 'Google Drive'
								);
								$vhost = new Vhosts;
								$savevhost = $this->hydrator->hydrate($tmp, $vhost);
								$vhostdb->save($savevhost);
							}
							$message->setSuccess('Bakup locations were created on Google Drive');
							return $this->redirect()->toRoute('backups/hosts');
							return true;
						} catch(Exception $e) {
							throw new \Exception($e->getMessage());
						}
					}
					return $this->redirect()->toRoute('backups/validate');
				}
			}
			if ($action && $action=='Start/Stop Uploading Backups') {
				foreach( $vhost as $key => $value ) {
					if(!empty($vhostData[$value])) {
						$vhostdb = $this->container->get('FgVirtualhost\Service\VhostsDBFactory');
						$vhost = $vhostdb->getById($vhostId[$value]);
						$vhost->setSavebackup(1);
						$vhostdb->save($vhost);
						$message->setSuccess(
							sprintf("<div><b>%s</b> Google backup enabled.</div>", $value)
						);
					} else {
						$message->setError(
							sprintf("<div><b>%s</b> Doesn't have google drive location. Please create it first.</div>", $value),
							true
						);
					}
				}
			}
		}
		return $this->redirect()->toRoute('backups/hosts');
	}
	
	public function databasesaveAction()
	{
		$error = false;
		$layout = $this->layout();
		$layout->setTemplate('login/process/layout');

		$action = $this->getRequest()->getPost()->action;
		$databases = $this->getRequest()->getPost()->database;
		$vhost = $this->getRequest()->getPost()->vhost;

		$this->getConfiguration();
		$configuration = $this->dbconfig->getConfiguration();	
		$message = new zbeMessage();
		if (! $action) {
			$error = true;
			$message->setError('Please select action');
			return false;
		} 
		if(empty($configuration["backupsgoogleparentdirectoryid"])) {
			$error = true;
			$message->setError('Please add Google Parent directory from configruation, so we can create backup location.');
		}

		if(!$error){
			if ($action && $action=='Create Backup Database') {
				foreach( $databases as $key => $value ){
					if( empty($value) ) continue;
					
					$dbbackups = $this->container->get('FgBackups\Service\BackupdatabaseService');
					$data = $dbbackups->findByName($value);
					if(!$data){
						$tmp = array(
							'vhost' => $vhost[$key],
							'database' => $value
						);
						$backupdatabase = new Backupdatabase;
						$savedatabase = $this->hydrator->hydrate($tmp, $backupdatabase);
						$dbbackups->save($savedatabase);	
						$message->setSuccess(
							sprintf("<div><b>%s</b> Database is add to backup.</div>", $value)
						);
					}
				}
			}
		}
		return $this->redirect()->toRoute('backups/database');
	}
	
	public function configurationAction()
	{
		$this->getAdminLayout();
		$this->getConfiguration();
        $configuration = $this->dbconfig->getConfiguration();			
		$view = new ViewModel(
            array('configuration' => $configuration)
        );
		return $view;
	}
	
	public function configurationsaveAction()
	{
		$post = $this->getRequest()->getPost()->configuration;
		try {
			$this->getConfiguration();
			$prepsave = $this->dbconfig->configurationCleanup($post);
			foreach($prepsave as $value) {
				$saveconfig = $this->hydrator->hydrate($value, $this->configurationModel);
				//\Zend\Debug\Debug::dump($saveconfig);die();
				$this->dbconfig->save($saveconfig);
			}
			
			$message = new zbeMessage();
			$message->setSuccess('Configuration save success');
			return $this->redirect()->toRoute('backups/configuration');
		} catch(Exception $e) {
			throw new \Exception($e->getMessage());
		}
		$layout = $this->layout();
		$layout->setTemplate('login/process/layout');
	}

	public function validateAction()
	{
		$layout = $this->layout();
		$layout->setTemplate('login/process/layout');
		$message = new zbeMessage();
		$google = $this->container->get('FgBackups\Service\GoogleApiFactory');
		$this->getConfiguration();
        $configuration = $this->dbconfig->getConfiguration();	
		if ($configuration['googleapplicationname']) {
			$client = $google->getClient();
			if (!$client) {
				$session = new Container('authurl');
				$authUrl = $session->offsetGet('authUrl');
				$session->offsetUnset('authUrl');
				return $this->redirect()->toUrl($authUrl);
			} else {
				$message->setSuccess('Access token is already issued');
				return $this->redirect()->toRoute('backups/hosts');
			}
		} else {
			$message->setError('Please add google API Credentials');
			return $this->redirect()->toRoute('backups/configuration');
		}
	}
	

	public function refreshAction()
	{
		$this->getAdminLayout();
	}
	
	public function savetokenAction()
	{
		$post = $this->getRequest()->getPost()->configuration;
		try {
			$this->getConfiguration();
			$prepsave = $this->dbconfig->configurationCleanup($post);
			foreach($prepsave as $value) {
				$saveconfig = $this->hydrator->hydrate($value, $this->configurationModel);
				//\Zend\Debug\Debug::dump($saveconfig);die();
				$this->dbconfig->save($saveconfig);
			}
			
			$message = new zbeMessage();
			$message->setSuccess('Configuration save success');
			return $this->redirect()->toRoute('backups/configuration');
		} catch(Exception $e) {
			throw new \Exception($e->getMessage());
		}
		$layout = $this->layout();
		$layout->setTemplate('login/process/layout');
	}
	
	protected function getVhostData()
	{
		$this->vhosts = $this->container->get('VirtualhostFactory');
	}
	
	protected function getConfiguration(){
		$this->dbconfig = $this->container->get('FgCore\Service\ConfigurationFactory');
	}
}
