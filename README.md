# brightlocal-api
Wrapper for BrightLocal API.  Work in progress.

[![Build Status](https://travis-ci.org/silktide/brightlocal-api.svg?branch=master)](https://travis-ci.org/silktide/brightlocal-api)
[![Code Climate](https://codeclimate.com/github/silktide/brightlocal-api/badges/gpa.svg)](https://codeclimate.com/github/silktide/brightlocal-api)
[![Test Coverage](https://codeclimate.com/github/silktide/brightlocal-api/badges/coverage.svg)](https://codeclimate.com/github/silktide/brightlocal-api/coverage)

Methods implemented:

* Batch create
* Batch commit
* Batch get results
* Fetch profile details by business data

## Create a client

### Using the client factory

This library comes with a convenient factory for those not using DI.  Just use the following to create an API client:

~~~~
$clientFactory = new \Silktide\BrightLocalApi\ClientFactory();
$client = $clientFactory->createClient([YOUR API KEY], [YOUR API SECRET]);	
~~~~

### Using dependency injection

*If you don't know what this is, ignore this part.*

If you're using [syringe](https://github.com/silktide/syringe) , a config is included for convenience.  If you want to wire it up in your own DI container, take a look inside the ClientFactory class to see how the Client and its dependencies are created.


## Batches

### Create a batch
~~~~
$batch = $client->createNewBatch();
~~~~

### Commit batch for processing
~~~~
$batch->commit();
~~~~

### Get results
Gets the batch results.  Note - due to the way BrightLocal takes time to gather results, you may prefer to poll for results (see below).
~~~~
$results = $batch->getResults();
~~~~

### Poll for results
As it may take time for BrightLocal to complete the batch, a convenience method will poll for results until they are available.  This hangs the execution of your script until the BrightLocal results are ready.  By default, the results will be checked every 5 seconds for up to 20 attempts (this can be overriden by passing values to this method).  An exception will be thrown if the batch has not completed before the maximum number of attempts have been made.
~~~~
$results = $batch->pollForResults();
~~~~

## Local directories

### Fetch profile details by business data
~~~~
$client->fetchProfileDetailsByBusinessData($batch, [
    'business-names' => 'Silktide',
    'country' => 'GBR',
    'city' => 'Derby',
    'postcode' => 'DE248HR',
    'local-directory' => 'google'
]);
~~~~

The directory should be one listed in [BrightLocal's appendices](http://apidocs.brightlocal.com/#appendix).
Country should be [3-letter ISO](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-3)

## Directory config helper
To help with talking to the BrightLocal API, a helper is included which can help with your integration.

### Find out which directories can be used
This function returns an array of directory IDs which BrightLocal supports in a given country.
~~~~
$helper = Silktide\BrightLocalApi\Data\DirectoryConfigHelper(new Silktide\BrightLocalApi\Data\DataLoader());
$helper->getDirectoriesForCountry('GBR');
~~~~

### Get the "friendly name" of a directory
BrightLocal's API contains a reference to all the directories they support, but all the names are lowercase with punctuation removed (e.g. 2findlocal, canadaspace).  This function returns a "friendly" name for your given directory.
~~~~
$helper = Silktide\BrightLocalApi\Data\DirectoryConfigHelper(new Silktide\BrightLocalApi\Data\DataLoader());
$helper->getLabelForDirectory('192'); // returns '192.com'
~~~~

## Complete examples

### Fetch profile details for a business

~~~~
// Create client (using factory)
$clientFactory = new \Silktide\BrightLocalApi\ClientFactory();
$client = $clientFactory->createClient([YOUR API KEY], [YOUR API SECRET]);	

// Directories to fetch
$directories = [
    'google',
    'facebook',
    'yell',
    'scoot'
];

//Create batch
$batch = $client->createNewBatch();

// Add directory checks for each local directory
foreach ($directories as $directory) {
    $client->fetchProfileDetailsByBusinessData($batch, [
        'business-names' => 'Silktide\nSilktide Ltd',
        'country' => 'GBR',
        'city' => 'Derby',
        'postcode' => 'DE248HR',
        'local-directory' => $directory
    ]);
}

// Commit the batch for processing
$batch->commit();

// Poll for results
$results = $batch->pollForResults();
~~~~

## General

### Error handling
In the event of an issue or bad result from the API, an exception will be thrown.  You should expect exceptions to be thrown in your application and handle them gracefully.