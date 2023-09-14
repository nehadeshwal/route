HTTP API, PHP HTTP client and webservice framework
==================================================
HTTP API is a PHP HTTP client that makes it easy to send HTTP requests and
trivial to integrate with web services.

- Manages things like persistent connections, represents query strings as
  collections, simplifies sending streaming POST requests with fields

- Can send both synchronous and asynchronous requests using the same interface
  without requiring a dependency on a specific event loop.

### Installing via Composer

```bash
"repositories": [
    {
        "type": "vcs",
        "url": "http://git.tnq.co.in/common/http-api.git"
    },
],
"require": {
    "php": ">=5.4.0",
    "tnq/httpapi": "latest tag"
},
```

### Make a Request

You can send requests with HttpApi using a Tnq\HttpApi\HttpRequest object.

```php
use Tnq\HttpApi\HttpRequest;

$httpApi = new HttpRequest();
$httpApi->setUrl("URL"); /* Request url to process */
$request = $httpApi->build();
$response = $request->execute();
```
### Overwrite default params before sending http reqeust

##### Set Reqeust Method

By default Reqeust method for HTTP API in GET method, to overwrite the default method type for the request as follows

```php
$httpApi->setMethod("GET"); //"POST", "GET", "PUT", "DELETE"
```
##### Set Authentication

To access the Authentication based URL need to sent authentication method as TRUE by default authentication method is open for every reqeust

```php
$httpApi->setAuth(true);
$httpApi->setAuthUsername("username");
$httpApi->setAuthPassword("password");
$httpApi->setAuthType("authentication type"); //"Basice", "Digest"
```
##### Set Headers

While sending the request we can add Header detail with the http reqeust

```php
$httpApi->setHeaders(
    [
        'X-Foo-Header' => 'value',
        'content-type' => 'application/json'
    ]
);
```
##### Set Retry

If the reqeust get faild there is a option to retry the reqeust, by default retry is set to false and there is option to set number of retry to be call.

```php
$httpApi->setRetry(true);
$httpApi->setRetryCount(5);
```

We can specify type of error to be retry using status codes

```php
$httpApi->setTypeOfErrorToRetry([500]);
```
##### Set Delay

we can set delay between the reqeust, by default delay set to 1

```php
$httpApi->setDelay(1000); /*milli secound*/
```

##### Set Data

You can send data as parameters for both GET and POST by following method

```php
$httpApi->setData("array of parameters");
```

If you want to send data as raw data you can send by following method

```php
$httpApi->setDataAsBody(true);
$httpApi->setData("Raw body content");
```

### Using Responses

In the previous examples, we retrieved a ```$response``` variable. This value is actually a Tnq\HttpApi\ResponsetHandler object and contains lots of helpful information.

##### Status code

To get the status code of the request

```php
$response->getStatusCode(); /*200 response*/
```

##### Body Content

The body of a response can be retrieved and cast to a string.

```php
$response->getBodyContent(); /*Body Content*/
```

##### JSON Responses

You can more easily work with JSON responses using the getJsonContent() method of a response.

```php
$response->getJsonContent(); /*Returns JSON formate body contents as array*/
```
##### XML Responses

You can use a responseâ€™s getXmlContent() method to more easily work with responses that contain XML data.

```php
$response->getXmlContent(); /*Returns XML formate body contents as array*/
```
##### Header Details

You can get the whole header details as an array using getHeaders().

```php
$response->getHeaders(); /*Returns header details as array*/
```
To get a specific formate of header details by following method

###### As String

```php
$response->getHeaderAsString('content-type');
/*return header details as string formate OUTPUT:application/json */
```
###### As Array

```php
$response->getHeaderAsArray('content-type');
#return header details as string formate OUTPUT:["0"=> "application/json"]
```

If you want to get detail other then mentioned here you can directly get it from $response object

### Exception handling

You will get two kind of exception from HTTPAPI reqeust

###### Request exception

Throws exception while is there any details given from the user is not valid, you can overwrite the exception using ``` use Tnq\HttpApi\Exception\RequestException;```

###### Response exception

Throws exception while executing http reqeust, you can overwrite the exception using
``` use Tnq\HttpApi\Exception\ResponseException;```
