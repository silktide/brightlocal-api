<?php

namespace Silktide\BrightLocalApi\Test;

use PHPUnit_Framework_TestCase;
use Silktide\BrightLocalApi\Api;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;

/**
 * Class ApiTest
 * Test the BrightLocalApi API class
 *
 * @package Silktide\BrightLocalApi\Test
 */
class ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $demoKey = "demo-key";

    /**
     * @var string
     */
    protected $demoSecret = "demo-secret";

    /**
     * @var array
     */
    protected $attemptContainer = [];

    /**
     * @var MockHandler
     */
    protected $mockHandler;

    /**
     * @var Api
     */
    protected $api;

    /**
     * Helper to set up Guzzle with mock response queue
     *
     * @return Guzzle
     */
    protected function setupGuzzlePlugin()
    {
        // Set up Guzzle Mock Handler
        $this->mockHandler = new MockHandler();

        // This is a container for the dummy request / responses made to Guzzle
        $history = Middleware::history($this->attemptContainer);
        $stack = HandlerStack::create($this->mockHandler);

        // Add the history middleware to the handler stack.
        $stack->push($history);
        return new Guzzle(["handler" => $stack]);
    }

    /**
     * Setup API client
     */
    public function setup()
    {
        $this->api = new Api($this->setupGuzzlePlugin(), $this->demoKey, $this->demoSecret);
    }

    /**
     * Test put
     */
    public function testPut()
    {
        $responseData = ['success' => true];
        $this->mockHandler->append(
          new Response(200, [], json_encode($responseData))
        );

        // Make the call
        $endpoint = '/v4/blah';
        $params = [
            'some-param' => 'some-value'
        ];
        $actualResponse = $this->api->put($endpoint, $params);

        $this->checkResponse('PUT', $endpoint, $params, $responseData, $actualResponse);
    }

    /**
     * Test post
     */
    public function testPost()
    {
        $responseData = ['success' => true];
        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        // Make the call
        $endpoint = '/v4/blah';
        $params = [
            'some-param' => 'some-value'
        ];
        $actualResponse = $this->api->post($endpoint, $params);

        $this->checkResponse('POST', $endpoint, $params, $responseData, $actualResponse);
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        $responseData = ['success' => true];
        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        // Make the call
        $endpoint = '/v4/blah';
        $params = [
            'some-param' => 'some-value'
        ];
        $actualResponse = $this->api->delete($endpoint, $params);

        $this->checkResponse('DELETE', $endpoint, $params, $responseData, $actualResponse);
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $responseData = ['success' => true];
        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        // Make the call
        $endpoint = '/v4/blah';
        $params = [
            'some-param' => 'some-value'
        ];
        $actualResponse = $this->api->get($endpoint, $params);

        $this->checkResponse('GET', $endpoint, $params, $responseData, $actualResponse, true);
    }

    /**
     * Check response is as expected
     *
     * @param string $method The HTTP method
     * @param string $endpoint The BrightLocal API method / endpoint
     * @param string[] $params The params passed to the API
     * @param string[] $expectedResponseData Our expected response from API
     * @param string[] $actualResponseData The actual response from API
     * @param bool $dataPassedAsQuery If it's a GET, the params will be passed as query string rather than request body
     */
    protected function checkResponse($method, $endpoint, $params, $expectedResponseData, $actualResponseData, $dataPassedAsQuery = false)
    {
        $this->assertEquals(1, count($this->attemptContainer), "Expected 1 request, but ".count($this->attemptContainer)." were made");

        $attempt = $this->attemptContainer[0];

        /**
         * @var Request $request
         */
        $request = $attempt['request'];

        // If it's a get, then params are passed as query string
        if ($dataPassedAsQuery) {
            $props = $request->getUri()->getQuery();
        } else {
            $props = $request->getBody()->getContents();
        }

        parse_str($props, $bodyData);

        $this->assertEquals($method, $request->getMethod(), "Expected a PUT but made a ".$request->getMethod());
        $this->assertEquals('tools.brightlocal.com', $request->getUri()->getHost(), "Wrong hostname");
        $this->assertEquals('/seo-tools/api'.$endpoint, $request->getUri()->getPath(), "Wrong path");
        $this->assertEquals('https', $request->getUri()->getScheme(), "Wrong path");
        $this->assertEquals($this->demoKey, $bodyData['api-key'], "Wrong key or not passed");
        $this->assertGreaterThan(0, strlen($bodyData['sig']), "Signature empty or not passed");
        $this->assertTrue(is_numeric($bodyData['expires']), "Expiry should be set as integer");
        $this->assertGreaterThan(time(), $bodyData['expires'], "Expiry not in the future");

        foreach ($params as $key => $value) {
            $this->assertTrue(isset($bodyData[$key]), $key." not passed in request body");
            $this->assertEquals($value, $bodyData[$key], "Incorrect value for ".$key." in request body");
        }

        $this->assertEquals($expectedResponseData, $actualResponseData, "Response data did not match expected");
    }
}