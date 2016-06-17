<?php

namespace Silktide\BrightLocalApi;

class Batch
{
    const WAIT_SECONDS = 5;
    const MAX_TRIES = 20;

    /**
     * @var Api
     */
    protected $api;

    /**
     * @var string
     */
    protected $batchId;

    /**
     * @var bool
     */
    protected $isCommitted = false;

    /**
     * Batch constructor.
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Set batch ID
     *
     * @param string $batchId The new batch ID
     */
    public function setBatchId($batchId)
    {
        $this->batchId = $batchId;
    }

    /**
     * Get batch ID
     *
     * @return string The batch ID
     * @throws \Exception
     */
    public function getBatchId()
    {
        if (!isset($this->batchId)) {
            throw new \Exception("No batch ID has been set.  Did you forget to create the batch first?");
        }

        return $this->batchId;
    }

    /**
     * Create batch
     *
     * @param bool $stopJobOnError Should the batch be halted on error?
     * @throws \Exception
     */
    public function create($stopJobOnError = false)
    {
        $response = $this->api->post('/v4/batch', [
            'stop-on-job-error' => (int) $stopJobOnError
        ]);

        if (!isset($response['success']) || !$response['success']) {
            throw new \Exception("Response was not successful.");
        }

        if (!isset($response['batch-id'])) {
            throw new \Exception("No batch ID found in response.");
        }

        $this->setBatchId($response['batch-id']);
    }

    /**
     * Commit batch
     *
     * @throws \Exception
     */
    public function commit()
    {
        $response = $this->api->put('/v4/batch', [
            'batch-id' => $this->getBatchId()
        ]);

        if (!isset($response['success']) || !$response['success']) {
            throw new \Exception("Response was not successful.");
        }

        $this->isCommitted = true;
    }

    /**
     * Delete batch
     *
     * @throws \Exception
     */
    public function delete()
    {
        $response = $this->api->delete('/v4/batch', [
            'batch-id' => $this->getBatchId()
        ]);

        if (!isset($response['success']) || !$response['success']) {
            throw new \Exception("Response was not successful.");
        }
    }

    /**
     * Poll for test results
     *
     * @param int $waitSeconds The amount of seconds to wait between tries
     * @param int $maxTries Maximum number of tries to make before throwing exception
     * @return array
     * @throws \Exception
     */
    public function pollForResults($waitSeconds = self::WAIT_SECONDS, $maxTries = self::MAX_TRIES)
    {
        for ($i = 0; $i < $maxTries; $i++) {
            $results = $this->getResults();

            if ($results['status'] == 'Finished' || $results['status'] == 'Stopped') {
                return $results;
            }

            if ($results['status'] == 'Created') {
                throw new \Exception("Batch not yet committed");
            }

            sleep($waitSeconds);
        }

        throw new \Exception("Timed out polling for results after ".$maxTries." attempts");
    }

    /**
     * Get batch results
     *
     * @return array
     * @throws \Exception
     */
    public function getResults()
    {
        $response = $this->api->get('/v4/batch', [
            'batch-id' => $this->getBatchId()
        ]);

        if (!isset($response['success']) || !$response['success']) {
            throw new \Exception("Response was not successful.");
        }

        return $response;
    }

    /**
     * Has this batch been committed?
     *
     * @return bool
     */
    public function hasBeenCommitted()
    {
        return $this->isCommitted;
    }
}