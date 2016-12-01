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

/**
 * CustomersController Class
 *
 * @category Controller
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class BackupController extends AdminController
{
	/*
	* @var Zend\ServiceManager\ServiceLocatorInterface
	*/
	protected $services;
	/*
	* @var Interop\Container\ContainerInterface
	*/
	protected $container;
	
	protected $authService;
	/*
	* @var FgCore\Manager\ConfigurationManager
	*/
	protected $configurationManager;
	
	public function __construct(
		ContainerInterface $container
	) {
		$this->container = $container;
	}
	
	protected function adminSetup()
	{
		$this->getAdminHeader();
		$this->getAdminSidebarmenu();
		$this->getAdminFooter();
	}
	
	public function indexAction()
	{
		$this->adminSetup();
	}
}
