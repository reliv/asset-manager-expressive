<?php

namespace Reliv\AssetManagerExpressive\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reliv\AssetManagerExpressive\Service\AssetManager;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class AssetManagerMiddleware
{
    /**
     * @var \Reliv\AssetManagerExpressive\Service\AssetManager
     */
    protected $assetManager;

    /**
     * @param AssetManager $assetManager
     */
    public function __construct(
        AssetManager $assetManager
    ) {
        $this->assetManager = $assetManager;
    }

    /**
     * __invoke
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable|null          $next
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) {
        if (!$this->assetManager->resolvesToAssetPsr($request)) {
            return $next($request, $response);
        }

        return $this->assetManager->setAssetOnResponsePsr();
    }
}
