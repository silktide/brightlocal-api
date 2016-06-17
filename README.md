# brightlocal-api
Wrapper for BrightLocal API.  Work in progress.

Methods implemented:

* Batch create
* Batch commit
* Batch get results
* Fetch profile details by business data

## Create a client

### Using dependency injection
*If you're not sure what this is, skip to creating a client using the client factory.*



### Using the client factory

This library comes with a convenient factory for those not using DI.  Just use the following to create an API client:

~~~~
$clientFactory = new \Silktide\BrightLocalApi\ClientFactory();
$client = $clientFactory->createClient([YOUR API KEY], [YOUR API SECRET]);	
~~~~


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
As it may take time for BrightLocal to complete the batch, a convenience method will poll for results until they are available.  By default, the results will be checked every 5 seconds for up to 20 attempts (this can be overriden by passing values to this method).  An exception will be thrown if the batch has not completed before the maximum number of attempts have been made.
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

## General

### Error handling
In the event of an issue or bad result from the API, an exception will be thrown.  You should expect exceptions to be thrown in your application and handle them gracefully.