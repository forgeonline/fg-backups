<?php
/**
 * Zend Backend (http://forge.co.nz/)
 *
 * Service
 *
 * PHP version 5
 *
 * @category Module
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
namespace FgBackups\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Hydrator\ClassMethods;
use FgBackups\Mapper\DbGoogleuploadsMapper;
use FgBackups\Model\Googleuploads;

/**
 * DbGoogleuploadsMapperFactory Class
 *
 * @category Service
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class DbGoogleuploadsMapperFactory implements FactoryInterface
{
    /**
     * Factory for zend-servicemanager v3.
     *
     * @param ContainerInterface $container
     * @param string $name
     * @param null|array $options
     * @return Logger
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
	) {
		return new DbGoogleuploadsMapper(
			$container->get('Zend\Db\Adapter\Adapter'),
			new ClassMethods(false),
			new Googleuploads()
		);
	}
	
    /**
     * Factory for zend-servicemanager v2.
     *
     * Proxies to `__invoke()`.
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function createService(
		ServiceLocatorInterface $serviceLocator
	) {
        return $this(
			$serviceLocator,
			FgBackups\Service\DbGoogleuploadsMapperFactory
		);
    }
}