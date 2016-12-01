<?php
/**
 * Zend Backend (http://forge.co.nz/)
 *
 * CustomersControllerFactory
 *
 * PHP version 5
 *
 * @category Module
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
namespace FgBackups\Factory;

use Interop\Container\ContainerInterface;
use FgBackups\Controller\BackupController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * BackupControllerFactory Class
 *
 * @category Factory
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class BackupControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null)
    {
        return new BackupController($container);
    }
	
	/**
	* Create service
	*
	* @param ServiceLocatorInterface $serviceLocator
	*
	* @return mixed
	*/
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		return $this($serviceLocator, 'FgBackups\Factory\BackupControllerFactory');
     }
}