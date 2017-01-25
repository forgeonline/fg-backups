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

use Zend\Mvc\Controller\AbstractActionController;
use Interop\Container\ContainerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Console\Request as ConsoleRequest;
use Zend\Hydrator\HydratorInterface;
use FgCore\Interfaces\ConfigurationInterface;
use FgCore\Model\zbeMessage;
use Zend\Session\Container;
use FgBackups\Model\Googleuploads;

/**
 * CustomersController Class
 *
 * @category Controller
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class CronController extends AbstractActionController
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
	
	/*
	* @var FgCore\Service\ConfigurationFactory
	*/
	protected $dbconfig;
	
	protected $config;
	
	protected $noofBackups = 7;
	
	protected $configuration;
	
	protected $google;
	
	protected $datetoday;	

	public function __construct(
		ContainerInterface $container,
		HydratorInterface $hydrator,
		ConfigurationInterface $configurationModel
	) {
		$this->container = $container;
		$this->hydrator  = $hydrator;
		$this->configurationModel = $configurationModel;
		$this->dbconfig = $this->container->get('FgCore\Service\ConfigurationFactory');
		$this->google = $this->container->get('FgBackups\Service\GoogleApiFactory');
		$this->config = $this->dbconfig->getConfiguration();	
		$this->setNoofBackups($this->config['noofbackups']);
	}

	
	public function savegooglebackupAction()
	{
		$request = $this->getRequest();
		$bashdeleteArray = array();
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException(
                'You can only use this action from a console!'
            );
        }
		
		// Get savebackup enabled vhost data
		$vhostdb = $this->container->get('FgVirtualhost\Service\VhostsDBFactory');
		$vhosts = $vhostdb->getEnabledVhosts();
		$config = $this->dbconfig->getConfiguration();	
		$backupdirectory = (isset($this->config['backupdirectory']))?$this->config['backupdirectory']:'backups';
		$root = $this->config['virtualhostpath'];
		$googleModelStatus = $this->google->checkGoogleDriveConnection();

		
		//die("CD");		
		ini_set('memory_limit','5G');
		
		
		foreach($vhosts as $key => $value){
			
			$insertedFile = NULL;
			if (! empty($value["backupkey"])) {
				$googleuploads = $this->container->get('FgBackups\Service\GoogleuploadsService');
				$path = $root . $value['name'] . DIRECTORY_SEPARATOR . $backupdirectory  ;

				foreach (new \DirectoryIterator($root . $value['name'] . DIRECTORY_SEPARATOR . $backupdirectory) as $fileInfo) {
					if($fileInfo->isDot()) continue;
					$pathinfo = pathinfo($fileInfo->getFilename());
					if($pathinfo['extension']=='gz' || $pathinfo['extension']=='sql'){
						//check if this is uploaded file
						$uploads = $googleuploads->findByName($fileInfo->getFilename(),false,$value['id']);

						if (!$uploads) {
							//if file size is over 1 GB send to administrator
							if($fileInfo->getSize()>(1024*1000)*1024) {
								$message = $value['name'] . ' Host exceed the max site value (1GB)';
								$message .= ' Please action';
								mail(
									$this->config['serveradminemail'],
									'Max file size reached',
									$message
								);
							}
							$insertedFile = $this->google->insertFile(
								$value,
								$root . $value['name'] . 
								DIRECTORY_SEPARATOR . 
								$backupdirectory . DIRECTORY_SEPARATOR 
								. $fileInfo->getFilename(),
								$fileInfo,
								$fileInfo->getSize()
							);
							if($insertedFile){
								$tmp = array(
									'backup' => $fileInfo->getFilename(),
									'vhost' => $value['id'],
									'size' => $fileInfo->getSize(),
									'fileid' => $insertedFile->id,
								);
								$googleUploadsModel = new Googleuploads();
								$savevhost = $this->hydrator->hydrate($tmp, $googleUploadsModel);
								$googleuploads->save($savevhost);
							}
						} else {
							
							$filedeletestatus = $this->filetoDelete( $fileInfo->getFilename() );
							if ( $filedeletestatus && file_exists($root.$value['name'].DIRECTORY_SEPARATOR.$backupdirectory.DIRECTORY_SEPARATOR.$fileInfo->getFilename()) ) {
								array_push( 
									$bashdeleteArray,
									$root.$value['name'].DIRECTORY_SEPARATOR.$backupdirectory.DIRECTORY_SEPARATOR.$fileInfo->getFilename() 
								);
							}
						}
					}
				}
			}
		}
		
		$bashconfig = $this->container->get('configuration');
		if (!empty($bashdeleteArray) ) {
			if(file_exists(getcwd() .DIRECTORY_SEPARATOR.$bashconfig['config_virtualhost']["bash_delete_files"]) ) {
				unlink(getcwd() .DIRECTORY_SEPARATOR.$bashconfig['config_virtualhost']["bash_delete_files"]);
			}
			$fp = fopen( getcwd() .DIRECTORY_SEPARATOR.$bashconfig['config_virtualhost']["bash_delete_files"], 'w');
			foreach($bashdeleteArray as $value ) {
				fwrite($fp, $value ."\n");
			}
			fclose($fp);
		}
		return "Cron run success";
	}
	

	protected function cronvertTotimestemp($filename)
	{
		$fileextvalidate = pathinfo($filename);
		if($fileextvalidate["extension"]=='sql'){
			preg_match('/([a-zA-Z0-9_-]+)([0-9]{4}-[0-9]{2}-[0-9]{2})(\.sql)/', $filename, $matches, PREG_OFFSET_CAPTURE);
		} else {
			preg_match('/([a-z_-]+)(.*)(\.tar\.gz)/', $filename, $matches, PREG_OFFSET_CAPTURE);
		}
		return $matches[2][0];
	}
	
	protected function setNoofBackups($backups)
	{
		$this->noofBackups = $backups;
	}
	
	protected function getNoofBackups()
	{
		return $this->noofBackups;
	}
	
	protected function filetoDelete($filename){
		$filenamedate = $this->cronvertTotimestemp($filename);
		$backups = $this->getNoofBackups();

		$this->datetoday = (!empty($this->datetoday))?$this->datetoday:new \DateTime();
		$filedate = new \DateTime($filenamedate);
		$interval = $this->datetoday->diff($filedate);
		
		if( $interval->format("%a")>$backups ) {
			return true;
		}
		
		return false;
	}

}
