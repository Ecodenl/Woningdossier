#Hoomdossier API

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

  

## API Test call  
Has no functionality besides testing the API authorization.  
**Url:**  /api/v1/  
**Method:** `GET`  

**Success Response:**
  * **Code:** 200  
    
**Error Response:**
  * **Code:** 401 UNAUTHORIZED <br />
    **Content:** `{ error : "Unauthenticated." }`
    
  * **Code:** 403 Forbidden <br />
    **Content:** `{ error : "Unauthorized for current cooperation." }`


## Register a user

**Url:**  /api/v1/register  
**Method:** `POST`  
**Body parameters**
```
{
  "email": "demo@example.org",
  "first_name": "Demo",
  "extra": {
      "contact_id": "2489728934"
  },
  "last_name": "User",
  "postal_code": "1234AB",
  "number": "10",
  "house_number_extension": "A",
  "street": "Teststreet",
  "city": "Nonexistantcity",
  "phone_number": null
}
```

**Parameter description**  
``email=[required|string]``    
 
``first_name=[required|string]``
  
``last_name=[required|string]``
  
``extra.contact_id=[optional]``
   
``postal_code=[required|eg; 1234AB, 1234 AB]``
  
``number=[required]``
  
``house_number_extension=[optional|eg; A, a, B2]``
  
``street=[required|string]``
  
``city=[required|string]``
  
``phone_number=[optional]``

**Success Response:**
  * **Code:** 201  
    **Content:** `{ account_id: 512, user_id: 532 }`
    
**Error Response:**
  * **Code:** 401 UNAUTHORIZED <br />
    **Content:** `{ error : "Unauthenticated." }`
    
  * **Code:** 403 Forbidden <br />
    **Content:** `{ error : "Unauthorized for current cooperation." }`
    
  * **Code:** 422 UNPROCESSABLE ENTRY <br />
    **Content:** `{ error : "Email Invalid" }`

