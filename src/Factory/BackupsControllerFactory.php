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
use FgBackups\Controller\BackupsController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Hydrator\ClassMethods;
use FgCore\Model\Configuration;

/**
 * BackupControllerFactory Class
 *
 * @category Factory
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class BackupsControllerFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null)
    {
        return new BackupsController(
			$container,
			new ClassMethods(false),
			new Configuration()
		);
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
		return $this($serviceLocator, 'FgBackups\Factory\BackupsControllerFactory');
     }
}