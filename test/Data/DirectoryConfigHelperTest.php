<?php

namespace Silktide\BrightLocalApi\Test\Data;

use Silktide\BrightLocalApi\Data\DirectoryConfigHelper;
use PHPUnit\Framework\TestCase;
/**
 * Class DirectoryConfigHelperTest
 * Test the DirectoryConfigHelper
 *
 * @package Silktide\BrightLocalApi\Test
 */
class DirectoryConfigHelperTest extends TestCase
{

    /**
     * @var DirectoryConfigHelper
     */
    protected $helper;

    /**
     * @var array
     */
    protected $mockDirectoryData = [
        'directory_one' => [
            'label' => 'Directory One',
            'countries' => [
                'GBR',
                'USA'
            ]
        ],
        'directory_two' => [
            'label' => 'Directory Two',
            'countries' => [
                'USA'
            ]
        ],
        'directory_three' => [
            'label' => 'Directory Three',
            'countries' => [
                'GBR',
                'CAN'
            ]
        ]
    ];

    /**
     * Set up helper
     */
    public function setup()
    {
        $mockDataLoader = $this->getMockBuilder('Silktide\BrightLocalApi\Data\DataLoader')->getMock();
        $mockDataLoader->expects($this->any())->method('loadDirectoryData')->willReturn($this->mockDirectoryData);
        $this->helper = new DirectoryConfigHelper($mockDataLoader);
    }

    /**
     * Test getting a test label
     */
    public function testGetLabelForDirectory()
    {
        foreach ($this->mockDirectoryData as $directoryId => $data) {
            $directorylabel = $this->helper->getLabelForDirectory($directoryId);
            $this->assertEquals($data['label'], $directorylabel);
        }
    }

    /**
     * Test getting a test label
     */
    public function testGetDirectoriesForCountry()
    {
        $country = 'USA';

        $shouldReturn = [];
        foreach ($this->mockDirectoryData as $directoryId => $data) {
            if (in_array($country, $data['countries'])) {
                $shouldReturn[] = $directoryId;
            }
        }

        $this->assertEquals($shouldReturn, $this->helper->getDirectoriesForCountry($country));
    }
}