<?php

namespace Silktide\BrightLocalApi;

/**
 * Class BatchFactory
 *
 * Creates Batches
 *
 * @package Silktide\BrightLocalApi
 */
class BatchFactory
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * BatchFactory constructor
     *
     * @param $api
     */
    public function __construct($api)
    {
        $this->api = $api;
    }

    /**
     * Create a batch
     *
     * @param bool $stopJobOnError Should the batch be stopped on error?
     * @return Batch
     * @throws \Exception
     */
    public function createBatch($stopJobOnError = false)
    {
        $batch = new Batch($this->api);
        $batch->create($stopJobOnError);
        return $batch;
    }
}