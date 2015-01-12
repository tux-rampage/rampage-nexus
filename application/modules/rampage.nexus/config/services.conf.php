<?php
/**
 * LICENSE: $license_text$
 *
 * @author    Axel Helmert <ah@luka.de>
 * @copyright Copyright (c) 2014 LUKA netconsult GmbH (www.luka.de)
 * @license   $license$
 */

namespace rampage\nexus;

use Zend\Navigation\Service\DefaultNavigationFactory;

return [
    'factories' => [
        'DeploymentConfig' => services\DeploymentConfigFactory::class,
        'navigation' => DefaultNavigationFactory::class,
    ],
    'aliases' => [
    ]
];
