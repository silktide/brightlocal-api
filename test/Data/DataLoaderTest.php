<?php

namespace Silktide\BrightLocalApi\Test\Data;

use PHPUnit_Framework_TestCase;
use Silktide\BrightLocalApi\Data\DataLoader;

/**
 * Class DataLoaderTest
 * Test the DataLoader
 *
 * @package Silktide\BrightLocalApi\Test
 */
class DataLoaderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var DataLoader
     */
    protected $dataLoader;

    /**
     * Set up DataLoader
     */
    public function setup()
    {
        $this->dataLoader = new DataLoader();
    }

    /**
     * Test loading of data
     */
    public function testLoadData()
    {
        $data = $this->dataLoader->loadDirectoryData();

        $this->assertTrue(is_array($data), "Data loaded from file should be an array.");
        $this->assertGreaterThan(0, count($data), "Data should contain at least one item.");
    }
}