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
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    // web-profiler-bundle 7.3 added .php route files; 8.0 removed the .xml
    // ones. Pick the format that exists in the installed version.
    $ext = version_compare(
        InstalledVersions::getVersion('symfony/web-profiler-bundle') ?? '0',
        '7.3.0',
        '>=',
    ) ? 'php' : 'xml';

    $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.' . $ext)
        ->prefix('/_wdt');

    $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.' . $ext)
        ->prefix('/_profiler');
};
