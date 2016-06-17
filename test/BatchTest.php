<?php

namespace Silktide\BrightLocalApi\Test;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Silktide\BrightLocalApi\Api;
use Silktide\BrightLocalApi\Batch;

/**
 * Class BatchTest
 * Test the BrightLocalApi Batch
 *
 * @package Silktide\BrightLocalApi\Test
 */
class BatchTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Api|PHPUnit_Framework_MockObject_MockObject
     */
    protected $api;

    /**
     * @var Batch
     */
    protected $batch;

    /**
     * @var string
     */
    protected $demoBatchId = "12345";

    protected function setup()
    {
        $this->api = $this->getMockBuilder('Silktide\BrightLocalApi\Api')->disableOriginalConstructor()->getMock();
        $this->batch = new Batch($this->api);
    }

    public function testCreateBatch()
    {


        $this->api
            ->expects($this->atLeastOnce())
            ->method('post')
            ->with('/v4/batch', [
                'stop-on-job-error' => false
            ])
            ->willReturn([
                'success' => true,
                'batch-id' => $this->demoBatchId
            ]);

        $this->batch->create();

        $this->assertEquals($this->demoBatchId, $this->batch->getBatchId(), "Batch ID not returned as expected on creation.");

    }

    public function testCommitBatch()
    {
        $this->api
            ->expects($this->atLeastOnce())
            ->method('put')
            ->with('/v4/batch', [
                'batch-id' => $this->demoBatchId
            ])
            ->willReturn([
                'success' => true
            ]);

        $this->batch->setBatchId($this->demoBatchId);
        $this->assertFalse($this->batch->hasBeenCommitted());
        $this->batch->commit();
        $this->assertTrue($this->batch->hasBeenCommitted());
    }

    public function testDeleteBatch()
    {
        $this->api
            ->expects($this->atLeastOnce())
            ->method('delete')
            ->with('/v4/batch', [
                'batch-id' => $this->demoBatchId
            ])
            ->willReturn([
                'success' => true
            ]);

        $this->batch->setBatchId($this->demoBatchId);
        $this->batch->delete();
    }

    public function testPollForResults()
    {
        $sampleNotReady = '{
          "success": true,
          "status": "Running"
        }';

        $sampleResult = '{
          "success": true,
          "status": "Finished"
        }';

        $sampleNotReadyAsData = json_decode($sampleNotReady, true);
        $sampleResultAsData = json_decode($sampleResult, true);

        $this->api
            ->expects($this->exactly(2))
            ->method('get')
            ->with('/v4/batch', [
                'batch-id' => $this->demoBatchId
            ])
            ->willReturnOnConsecutiveCalls($sampleNotReadyAsData, $sampleResultAsData);

        $this->batch->setBatchId($this->demoBatchId);
        $result = $this->batch->pollForResults();

        $this->assertEquals($sampleResultAsData, $result, "Result data not returned correctly.");
    }


    public function testGetResultsFromBatch()
    {
        $sampleResult = '{
          "success": true,
          "status": "Finished",
          "results": {
            "LdFetchProfileUrl": [
              {
                "results": [
                  {
                    "url": "https://plus.google.com/117512971192208385977/about?hl=en&rfmt=s"
                  }
                ],
                "status": "Completed",
                "job-id": 318
              }
            ],
            "LdFetchProfileDetails": [
              {
                "results": [
                  {
                    "business_name": "Hub Plumbing & Mechanical",
                    "street_address": null,
                    "postcode": null,
                    "region": null,
                    "locality": null,
                    "address": "Greenwich Village New York, NY",
                    "contact_telephone": "+1 917-634-8888",
                    "description_present": true,
                    "num_photos": 2,
                    "star_rating": "4.7",
                    "num_reviews": 10,
                    "claimed": true,
                    "website_url": "http://www.hubplumbingnyc.com/",
                    "cid": "117512971192208385977",
                    "categories": "Plumber",
                    "check_in": null
                  }
                ],
                "status": "Completed",
                "job-id": 318
              }
            ]
          },
          "statuses": {
            "Completed": 2
          }
        }';

        $sampleResultAsData = json_decode($sampleResult, true);

        $this->api
            ->expects($this->atLeastOnce())
            ->method('get')
            ->with('/v4/batch', [
                'batch-id' => $this->demoBatchId
            ])
            ->willReturn($sampleResultAsData);

        $this->batch->setBatchId($this->demoBatchId);
        $result = $this->batch->getResults();

        $this->assertEquals($sampleResultAsData, $result, "Result data not returned correctly.");
    }
}