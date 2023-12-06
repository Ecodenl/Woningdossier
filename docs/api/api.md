# Hoomdossier API
## API Token
An API token will be issued by Hoomdossier itself, contact https://hoom.nl/ for a token. 


### Authorization
Make sure to set the Bearer token in the Authorization header
```php
// Test the connection. If we get an HTTP 200 back, it's successful
$client = new \GuzzleHttp\Client();

$token = 'YOUR-API-TOKEN';

$headers = [
    'Authorization' => 'Bearer ' . $token,
    'Accept'        => 'application/json',
];

$response = $client->request(
    'GET', 
    "https://hoom.homedossier.nl/api/v1/", 
    compact('headers')
);
$content = $response->getBody()->getContents();
```

## The docs itself.
They can differ between production and test, be sure to check the right one. 
#### Production API docs
https://hoom.hoomdossier.nl/api/documentation
#### Test environment API Docs
https://test-hoom.homedossier.nl/api/documentation
