<?php
/**
 * Zend Backend (http://forge.co.nz/)
 *
 * Config
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

use Zend\Router\Http\Literal;

return [
    'controllers' => [
        'factories' => [
			Controller\BackupsController::class => 'FgBackups\Factory\BackupControllerFactory',
        ],
    ],
    'router' => [
        'routes' => [
            'backups' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/backups',
                    'defaults' => [
                        'controller' => Controller\BackupsController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'template_map' => [
            'fg-backups/backups/index' => __DIR__ . '/../view/pages/index.phtml',
            ],
    ],
];
