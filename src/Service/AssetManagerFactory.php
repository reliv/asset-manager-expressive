<?php

namespace Reliv\AssetManagerExpressive\Service;

use Psr\Container\ContainerInterface;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class AssetManagerFactory
{
    /**
     * @param ContainerInterface $serviceContainer
     *
     * @return AssetManager
     */
    public function __invoke(
        $serviceContainer
    ) {
        $config = $serviceContainer->get('Config');
        $assetManagerConfig = [];

        if (!empty($config['asset_manager'])) {
            $assetManagerConfig = $config['asset_manager'];
        }

        $assetManager = new AssetManager(
            $serviceContainer->get('AssetManager\Service\AggregateResolver'),
            $assetManagerConfig
        );

        $assetManager->setAssetFilterManager(
            $serviceContainer->get('AssetManager\Service\AssetFilterManager')
        );

        $assetManager->setAssetCacheManager(
            $serviceContainer->get('AssetManager\Service\AssetCacheManager')
        );

        return $assetManager;
    }
}
