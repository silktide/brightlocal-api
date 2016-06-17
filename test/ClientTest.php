<?php

namespace Silktide\BrightLocalApi\Test;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Silktide\BrightLocalApi\Api;
use Silktide\BrightLocalApi\BatchFactory;
use Silktide\BrightLocalApi\Client;

/**
 * Class ClientTest
 * Test the BrightLocalApi Client
 *
 * @package Silktide\BrightLocalApi\Test
 */
class ClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Api|PHPUnit_Framework_MockObject_MockObject
     */
    protected $api;

    /**
     * @var BatchFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $batchFactory;

    /**
     * @var Client
     */
    protected $client;

    protected function setup()
    {
        $this->api = $this->getMockBuilder('Silktide\BrightLocalApi\Api')->disableOriginalConstructor()->getMock();
        $this->batchFactory = $this->getMockBuilder('Silktide\BrightLocalApi\BatchFactory')->disableOriginalConstructor()->getMock();
        $this->client = new Client($this->api, $this->batchFactory);
    }

    public function testCreateBatch()
    {
        $mockBatch = $this->getMockBuilder('Silktide\BrightLocalApi\Batch')->disableOriginalConstructor()->getMock();

        $this->batchFactory
            ->expects($this->atLeastOnce())
            ->method('createBatch')
            ->with(false)
            ->willReturn($mockBatch);

        $resultBatch = $this->client->createNewBatch();

        $this->assertEquals($mockBatch, $resultBatch);
    }

    public function testFetchProfileDetailsByBusinessData()
    {
        $mockBatch = $this->getMockBuilder('Silktide\BrightLocalApi\Batch')->disableOriginalConstructor()->getMock();

        $data = [
            'business-names' => "A business",
            'country' => "GBR",
            'city' => 'Derby'
        ];

        $mockBatchId = "12345";

        $mockBatch
            ->expects($this->atLeastOnce())
            ->method('getBatchId')
            ->willReturn($mockBatchId);

        $this->api
            ->expects($this->atLeastOnce())
            ->method('post')
            ->with('/v4/ld/fetch-profile-details-by-business-data', array_merge($data, ['batch-id' => $mockBatchId]));

        $this->client->fetchProfileDetailsByBusinessData($mockBatch, $data);

    }

}