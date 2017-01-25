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
			Controller\BackupsController::class => 'FgBackups\Factory\BackupsControllerFactory',
			Controller\CronController::class => 'FgBackups\Factory\CronControllerFactory',
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
				'may_terminate' => false,
				'child_routes' => [
					'save' => [
						'type' => Literal::class,
						'options' => [
							'route' => '/save',
							'defaults' => [
								'action' => 'save',
							]
						],
					],
					'configuration' => [
						'type' => Literal::class,
						'options' => [
							'route' => '/configuration',
							'defaults' => [
								'action' => 'configuration',
							]
						],
					],
					'configurationsave' => [
						'type' => Literal::class,
						'options' => [
							'route' => '/configurationsave',
							'defaults' => [
								'action' => 'configurationsave',
							]
						],
					],
					'hosts' => [
						'type' => Literal::class,
						'options' => [
							'route' => '/hosts',
							'defaults' => [
								'action' => 'hosts',
							]
						],
					],
					'vhostsave' => [
						'type' => Literal::class,
						'options' => [
							'route' => '/vhostsave',
							'defaults' => [
								'action' => 'vhostsave',
							]
						],
					],
					'googleauth' => [
						'type' => Literal::class,
						'options' => [
							'route' => '/googleauth',
							'defaults' => [
								'action' => 'googleauth',
							]
						],
					],
					'validate' => [
						'type' => Literal::class,
						'options' => [
							'route' => '/validate',
							'defaults' => [
								'action' => 'validate',
							]
						],
						'may_terminate' => true,
						'child_routes' => [
							'refresh' => [
								'type' => Literal::class,
								'options' => [
									'route' => '/refresh',
									'defaults' => [
										'action' => 'refresh',
									]
								],
							],
							'savetoken' => [
								'type' => Literal::class,
								'options' => [
									'route' => '/savetoken',
									'defaults' => [
										'action' => 'savetoken',
									]
								],
							],
						],
					],
					'database' => [
						'type' => Literal::class,
						'options' => [
							'route' => '/database',
							'defaults' => [
								'action' => 'database',
							]
						],
					],
					'databasesave' => [
						'type' => Literal::class,
						'options' => [
							'route' => '/databasesave',
							'defaults' => [
								'action' => 'databasesave',
							]
						],
					],
				],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
			'FgBackups\Mapper\DbGoogleuploadsMapper' => 'FgBackups\Service\DbGoogleuploadsMapperFactory',
			'FgBackups\Mapper\DbBakupdatabaseMapper' => 'FgBackups\Service\DbBakupdatabaseMapperFactory',
			'FgBackups\Service\GoogleuploadsService' => Service\GoogleuploadsService::class,
			'FgBackups\Service\BackupdatabaseService' => Service\BackupdatabaseService::class,
            'FgBackups\Service\GoogleApiFactory' => Service\GoogleApiFactory::class,
          ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'template_map' => [
            'fg-backups/backups/index' 	=> __DIR__ . '/../view/pages/index.phtml',
			'fg-backups/backups/hosts'	=> __DIR__ . '/../view/pages/hosts.phtml',
			'fg-backups/backups/database'	=> __DIR__ . '/../view/pages/database.phtml',
			'fg-backups/backups/configuration'	=> __DIR__ . '/../view/pages/configuration.phtml',
			'fg-backups/backups/refresh'	=> __DIR__ . '/../view/pages/validate_refresh.phtml',
            ],
    ],
    'console' => [
        'router' => [
            'routes' => [
                'savegooglebackup' => [
                    'options' => [
                        'route'    => 'savegooglebackup',
                        'defaults' => [
                            'controller' => Controller\CronController::class,
                            'action'     => 'savegooglebackup'
                        ]
                    ],
                ],
            ],
        ],
     ],
];
