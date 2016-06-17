<?php

namespace Silktide\BrightLocalApi;

class Client
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * @var BatchFactory
     */
    protected $batchFactory;

    /**
     * Client constructor.
     *
     * @param Api $api API client
     * @param BatchFactory $batchFactory
     */
    public function __construct(Api $api, BatchFactory $batchFactory)
    {
        $this->api = $api;
        $this->batchFactory = $batchFactory;
    }

    /**
     * Create a new batch
     *
     * @param bool $stopJobOnError Should the batch be stopped on error?
     * @return Batch
     */
    public function createNewBatch($stopJobOnError = false)
    {
        return $this->batchFactory->createBatch($stopJobOnError);
    }

    /**
     * Fetch profile details by business data
     *
     * @param Batch $batch The batch to add this to
     * @param string[] $parameters The parameters for request
     * @throws \Exception
     */
    public function fetchProfileDetailsByBusinessData(Batch $batch, $parameters)
    {
        if ($batch->hasBeenCommitted()) {
            throw new \Exception("Attempting to add task to a batch which has already been comitted!");
        }

        $parameters['batch-id'] = $batch->getBatchId();

        $this->api->post('/v4/ld/fetch-profile-details-by-business-data', $parameters);
    }
}