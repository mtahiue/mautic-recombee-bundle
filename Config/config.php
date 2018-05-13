<?php

return [
    'name'        => 'Recombee',
    'description' => 'Enable integration with Recombee  - personalize content using Recommender as a Service',
    'author'      => 'kuzmany.biz',
    'version'     => '0.9.0',
    'services'    => [
        'events'       => [
            'mautic.recombee.pagebundle.subscriber'  => [
                'class'     => MauticPlugin\MauticRecombeeBundle\EventListener\PageSubscriber::class,
                'arguments' => [
                    'mautic.recombee.helper',
                    'mautic.recombee.service.replacer',
                    'mautic.recombee.service.api.commands',
                ],
            ],
            'mautic.recombee.leadbundle.subscriber'  => [
                'class'     => MauticPlugin\MauticRecombeeBundle\EventListener\LeadSubscriber::class,
                'arguments' => [
                    'mautic.recombee.helper',
                    'mautic.recombee.service.api.commands',
                ],
            ],
            'mautic.recombee.emailbundle.subscriber' => [
                'class'     => MauticPlugin\MauticRecombeeBundle\EventListener\EmailSubscriber::class,
                'arguments' => [
                    'mautic.recombee.helper',
                    'mautic.recombee.service.replacer',
                ],
            ],
        ],
        'models'       => [
            'mautic.recombee.model.recombee' => [
                'class' => MauticPlugin\MauticRecombeeBundle\Model\RecombeeModel::class,
            ],
        ],
        'forms'        => [
            'mautic.form.type.recombee'         => [
                'class'     => MauticPlugin\MauticRecombeeBundle\Form\Type\RecombeeType::class,
                'alias'     => 'recombee',
                'arguments' => [
                    'mautic.security',
                    'router',
                    'translator',
                ],
            ],
            'mautic.form.type.recombee.example' => [
                'class' => MauticPlugin\MauticRecombeeBundle\Form\Type\RecombeeExampleType::class,
                'alias' => 'recombee_example',
            ],

            'mautic.form.type.recombee.types'             => [
                'class' => MauticPlugin\MauticRecombeeBundle\Form\Type\RecombeeTypesType::class,
                'alias' => 'recombee_types',
            ],
            'mautic.form.type.recombee.recombee_template' => [
                'class' => MauticPlugin\MauticRecombeeBundle\Form\Type\RecombeeTemplateType::class,
                'alias' => 'recombee_template',
            ],
        ],
        'other'        => [
            'mautic.recombee.helper'                              => [
                'class'     => MauticPlugin\MauticRecombeeBundle\Helper\RecombeeHelper::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'mautic.recombee.model.recombee',
                    'translator',
                    'mautic.security',
                ],
            ],
            'mautic.recombee.api.recombee'                        => [
                'class'     => MauticPlugin\MauticRecombeeBundle\Api\RecombeeApi::class,
                'arguments' => [
                    'mautic.page.model.trackable',
                    'mautic.helper.integration',
                    'monolog.logger.mautic',
                ],
            ],
            'mautic.recombee.service.api.commands' => [
                'class'     => MauticPlugin\MauticRecombeeBundle\Api\Service\ApiCommands::class,
                'arguments' => [
                    'mautic.recombee.api.recombee',
                    'monolog.logger.mautic',
                    'translator'
                ],
            ],
            'mautic.recombee.service.token'                       => [
                'class' => MauticPlugin\MauticRecombeeBundle\Service\RecombeeToken::class,
            ],
            'mautic.recombee.service.token.finder'                => [
                'class'     => MauticPlugin\MauticRecombeeBundle\Service\RecombeeTokenFinder::class,
                'arguments' => [
                    'mautic.recombee.service.token',
                ],
            ],
            'mautic.recombee.service.replacer'                    => [
                'class'     => MauticPlugin\MauticRecombeeBundle\Service\RecombeeTokenReplacer::class,
                'arguments' => [
                    'mautic.recombee.service.token',
                    'mautic.recombee.service.token.finder',
                    'mautic.recombee.service.token.generator',
                ],
            ],
            'mautic.recombee.service.token.generator'             => [
                'class'     => MauticPlugin\MauticRecombeeBundle\Service\RecombeeGenerator::class,
                'arguments' => [
                    'mautic.recombee.model.recombee',
                    'mautic.recombee.api.recombee',
                    'mautic.tracker.contact',
                    'mautic.lead.model.lead',
                    'twig',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.recombee' => [
                'class'     => \MauticPlugin\MauticRecombeeBundle\Integration\RecombeeIntegration::class,
                'arguments' => [
                    'mautic.recombee.helper',
                ],
            ],
        ],
    ],
    'routes'      => [
        'main'   => [
            'mautic_recombee_index'  => [
                'path'       => '/recombee/{page}',
                'controller' => 'MauticRecombeeBundle:Recombee:index',
            ],
            'mautic_recombee_action' => [
                'path'       => '/recombee/{objectAction}/{objectId}',
                'controller' => 'MauticRecombeeBundle:Recombee:execute',
            ],
        ],
        'public' => [
            'mautic_recombee_webhook' => [
                'path'       => '/recombee/hook',
                'controller' => 'MauticRecombeeBundle:Webhook:process',
            ],
        ],
        'api'    => [
            'mautic_recombee_api' => [
                'path'       => '/recombee/{component}/{user}/{action}/{item}',
                'controller' => 'MauticRecombeeBundle:Api\RecombeeApi:process',
                'method'     => 'POST',
            ],
        ],
    ],
    'menu'        => [
        'main' => [
            'items' => [
                'mautic.plugin.recombee' => [
                    'route'    => 'mautic_recombee_index',
                    'access'   => ['recombee:recombee:viewown', 'recombee:recombee:viewother'],
                    'checks'   => [
                        'integration' => [
                            'Recombee' => [
                                'enabled' => true,
                            ],
                        ],
                    ],
                    'parent'   => 'mautic.core.components',
                    'priority' => 100,
                ],
            ],
        ],
    ],
    'parameters'  => [],
];