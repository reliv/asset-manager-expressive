<?php

namespace Reliv\AssetManagerExpressive\Service;

use Assetic\Asset\AssetInterface;
use AssetManager\Exception;
use AssetManager\Resolver\ResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class AssetManager extends \AssetManager\Service\AssetManager
{
    /**
     * @param ResolverInterface $resolver
     * @param array             $config
     */
    public function __construct(
        ResolverInterface $resolver,
        array $config
    ) {
        parent::__construct(
            $resolver,
            $config
        );
    }

    /**
     * Check if the request resolves to an asset.
     *
     * @param    ServerRequestInterface $request
     *
     * @return   boolean
     */
    public function resolvesToAssetPsr(ServerRequestInterface $request)
    {
        if (null === $this->asset) {
            $this->asset = $this->resolvePsr($request);
        }

        return (bool)$this->asset;
    }

    /**
     * Set the asset on the response, including headers and content.
     *
     * @param    ResponseInterface $response
     *
     * @return   ResponseInterface
     * @throws   Exception\RuntimeException
     */
    public function setAssetOnResponsePsr(ResponseInterface $response)
    {
        if (!$this->asset instanceof AssetInterface) {
            throw new Exception\RuntimeException(
                'Unable to set asset on response. Request has not been resolved to an asset.'
            );
        }

        // @todo: Create Asset wrapper for mimetypes
        if (empty($this->asset->mimetype)) {
            throw new Exception\RuntimeException('Expected property "mimetype" on asset.');
        }

        $this->getAssetFilterManager()->setFilters($this->path, $this->asset);

        $this->asset = $this->getAssetCacheManager()->setCache($this->path, $this->asset);
        $mimeType = $this->asset->mimetype;
        $assetContents = $this->asset->dump();

        // @codeCoverageIgnoreStart
        if (function_exists('mb_strlen')) {
            $contentLength = mb_strlen($assetContents, '8bit');
        } else {
            $contentLength = strlen($assetContents);
        }
        // @codeCoverageIgnoreEnd

        if (!empty($this->config['clear_output_buffer']) && $this->config['clear_output_buffer']) {
            // Only clean the output buffer if it's turned on and something
            // has been buffered.
            if (ob_get_length() > 0) {
                ob_clean();
            }
        }

        /** @var ResponseInterface $response */
        $response = $response->withAddedHeader(
            'Content-Transfer-Encoding',
            'binary'
        )->withAddedHeader(
            'Content-Type',
            $mimeType
        )->withAddedHeader(
            'Content-Length',
            $contentLength
        );

        $body = $response->getBody();
        $body->rewind();
        $body->write($assetContents);

        $response = $response->withBody($body);

        $this->assetSetOnResponse = true;

        return $response;
    }

    /**
     * Resolve the request to a file.
     *
     * @param ServerRequestInterface $request
     *
     * @return mixed false when not found, AssetInterface when resolved.
     */
    protected function resolvePsr(ServerRequestInterface $request)
    {
        /* @var $uri \Zend\Uri\UriInterface */
        $uri = $request->getUri();
        //$fullPath   = $uri->getPath();
        //$path       = substr($fullPath, strlen($request->getBasePath()) + 1);
        $path = $uri->getPath();
        $this->path = $path;
        $asset = $this->getResolver()->resolve($path);

        if (!$asset instanceof AssetInterface) {
            return false;
        }

        return $asset;
    }
}
