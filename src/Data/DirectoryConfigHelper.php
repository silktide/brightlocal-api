<?php

namespace Silktide\BrightLocalApi\Data;

use Exception;

/**
 * Class DirectoryConfigHelper
 * 
 * @package Silktide\BrightLocalApi\Data
 */
class DirectoryConfigHelper
{

    /**
     * @var DataLoader
     */
    protected $dataLoader;

    /**
     * @var array
     */
    protected $data;

    /**
     * DirectoryConfigHelper constructor.
     *
     * @param DataLoader $dataLoader
     */
    public function __construct(DataLoader $dataLoader)
    {
        $this->dataLoader = $dataLoader;
    }

    /**
     * Get the directory data
     *
     * @return array
     */
    protected function getDirectoryData()
    {
        if (!isset($this->data)) {
            $this->data = $this->dataLoader->loadDirectoryData();
        }

        return $this->data;
    }

    /**
     * Get the directories appropriate for given country
     *
     * @param string $country
     * @return array
     */
    public function getDirectoriesForCountry($country)
    {
        $directories = [];
        foreach ($this->getDirectoryData() as $directory => $data) {
            if (in_array($country, $data['countries'])) {
                $directories[] = $directory;
            }
        }
        return $directories;
    }

    /**
     * Get friendly label for a directory
     *
     * @param $directory
     * @return mixed
     * @throws Exception
     */
    public function getLabelForDirectory($directory)
    {
        $data = $this->getDirectoryData();

        if (!isset($data[$directory])) {
            throw new Exception("Directory ".$directory." not found in directory data.");
        }

        // Fallback to directory name if no label set
        if (!isset($data[$directory]['label'])) {
            return $directory;
        }

        return $data[$directory]['label'];

    }

}