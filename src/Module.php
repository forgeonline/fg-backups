<?php
/**
 * Zend Backend (http://forge.co.nz/)
 *
 * Module
 *
 * PHP version 5
 *
 * @category Module
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
namespace FgBackups;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
/**
 * Module Class
 *
 * @category Module
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ConsoleUsageProviderInterface   // <- our module implement this feature and provides console usage info
{
    /**
    * Get Module configuration
    *
    * @return array
    */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }


    public function getAutoloaderConfig()
    {
    }
    /**
     * This method is defined in ConsoleBannerProviderInterface
     */
    public function getConsoleUsage(Console $console)
    {
        return array(
            // Describe available commands
            'savegooglebackup' => 'Save backups to google drive'
        );
    }
}
