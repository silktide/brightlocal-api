<?php

namespace Silktide\BrightLocalApi\Data;

/**
 * Class DataLoader
 *
 * @package Silktide\BrightLocalApi\Data
 */
class DataLoader
{
    /**
     * @param $file
     * @return array
     */
    public function loadJsonDataFromFile($file)
    {
        $data = file_get_contents($file);
        return json_decode($data, true);
    }

    /**
     * @return array
     */
    public function loadDirectoryData()
    {
        return $this->loadJsonDataFromFile(dirname(__FILE__).'/../../data/directories.json');
    }
}