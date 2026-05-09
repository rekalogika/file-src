<?php

declare(strict_types=1);

/*
 * This file is part of rekalogika/file-src package.
 *
 * (c) Priyadi Iman Nurcahyo <https://rekalogika.dev>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

use Composer\InstalledVersions;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $orm = [
        'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
        'controller_resolver' => [
            'auto_mapping' => false,
        ],
        'mappings' => [
            'App' => [
                'is_bundle' => false,
                'type' => 'attribute',
                'dir' => '%kernel.project_dir%/src/App/Entity',
                'prefix' => 'Rekalogika\\File\\Tests\\App\\Entity',
                'alias' => 'App',
            ],
        ],
    ];

    // 2.x-only options: superseded by always-on behavior in 3.x (proxy
    // generation replaced by native lazy objects; lazy ghost & where-declared
    // reporting are mandatory). On 2.x they're opt-in and required to keep
    // CI's max[direct]=0 deprecation check green.
    if (version_compare(InstalledVersions::getVersion('doctrine/doctrine-bundle') ?? '0', '3.0.0', '<')) {
        $orm['auto_generate_proxy_classes'] = true;
        $orm['enable_lazy_ghost_objects'] = true;
        $orm['report_fields_where_declared'] = true;
    }

    $container->extension('doctrine', [
        'dbal' => [
            'driver' => 'pdo_sqlite',
            'memory' => true,
            'charset' => 'UTF8',
        ],
        'orm' => $orm,
    ]);
};
