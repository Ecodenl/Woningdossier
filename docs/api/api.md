#Hoomdossier API

#### Register a user

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
  "phone_number": null,
  "allow_access": 1
}
```

**Parameter description**  
``email=[required|string]``    
 
``first_name=[required|string]``
  
``last_name=[required|string]``
  
``extra.contact_id=[required]``
   
``postal_code=[required|eg; 1234AB, 1234 AB]``
  
``number=[required]``
  
``house_number_extension=[optional|eg; A, a, B2]``
  
``street=[required|string]``
  
``city=[required|string]``
  
``phone_number=[optional]``
  
``allow_access=[required| Must be either "yes", "on", 1, or true.]``

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
  


