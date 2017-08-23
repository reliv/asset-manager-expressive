<?php

namespace Reliv\AssetManagerExpressive\Middleware;

use Psr\Container\ContainerInterface;
use Reliv\AssetManagerExpressive\Service\AssetManager;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class AssetManagerMiddlewareFactory
{
    /**
     * @param ContainerInterface $serviceContainer
     *
     * @return AssetManagerMiddleware
     */
    public function __invoke(
        $serviceContainer
    ) {
        return new AssetManagerMiddleware(
            $serviceContainer->get(
                AssetManager::class
            )
        );
    }
}
