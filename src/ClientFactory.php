<?php

namespace Silktide\BrightLocalApi;
use GuzzleHttp\Client as Guzzle;

/**
 * Class BatchFactory
 *
 * Factory for convenience if not using DI
 *
 * @package Silktide\BrightLocalApi
 */
class ClientFactory
{
    /**
     * Create an API client and its dependencies
     *
     * @return Client
     * @throws \Exception
     */
    public function createClient($apiKey, $apiSecret)
    {
        $api = new Api(new Guzzle(), $apiKey, $apiSecret);
        $batchFactory = new BatchFactory($api);
        return new Client($api, $batchFactory);
    }
}