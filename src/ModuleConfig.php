<?php

namespace Reliv\AssetManagerExpressive;

use Reliv\AssetManagerExpressive\Middleware\AssetManagerMiddleware;
use Reliv\AssetManagerExpressive\Middleware\AssetManagerMiddlewareFactory;
use Reliv\AssetManagerExpressive\Service\AssetManager;
use Reliv\AssetManagerExpressive\Service\AssetManagerFactory;

/**
 * Class ModuleConfig
 *
 * @author    James Jervis <jjervis@relivinc.com>
 * @copyright 2016 Reliv International
 * @license   License.txt
 * @link      https://github.com/reliv
 */
class ModuleConfig
{
    /**
     * __invoke
     *
     * @return array
     */
    public function __invoke()
    {
        $config = require(__DIR__ . '/../../../rwoverdijk/assetmanager/config/module.config.php');
        $dependencies = [
            AssetManagerMiddleware::class
            => AssetManagerMiddlewareFactory::class,

            AssetManager::class
            => AssetManagerFactory::class,
        ];

        $dependencies = array_merge_recursive(
            $config['service_manager'],
            $dependencies
        );

        return [
            'asset_manager' => $config['asset_manager'],
            'dependencies' => $dependencies,
        ];
    }
}
