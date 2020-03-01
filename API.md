# Canaries API

## Endpoints

- v1 - API version 1
- fake - Fake API as a placeholder

## Routes

- [Auth](#route-auth)
- [Domains](#route-domains)
- [Monitored sites](#route-sites)
- [Canary nodes](#route-canaries)
- [Mail](#route-mail)

### <a name="route-auth"></a>Auth

#### /{endpoint}/auth/login : POST
- description: Log in and get access token
- request:
  - parameters:
    - body:
      ```json
      {
        "username": "user", 
        "password": "secret123"
      }
      ```
- response:
  - http_code: 200
  - parameters:
    - body:
      ```json
      {
        "token": "JWT_ACCESSTOKEN", 
        "token_type": "bearer", 
        "expires": 3600,
        "uuid": "uuidstring", 
        "permissions": ["admin"],
        "canaries": []
      }
      ```
	
- response:
  - http_code: 400
  - parameters:
    - http_headers: 
		  - `X-RateLimit-Limit: 10`
			- `X-RateLimit-Remaining: 7`
		- body: 
      ```json
      {
        "code": 0,
        "message": "Bad request",
        "details": "Invalid credentials..."
	    }
      ```
	
- response:
  - http_code: 429
  - parameters:
    - http_headers: 
		  - `X-RateLimit-Limit: 10`
			- `X-RateLimit-Remaining: 0`
			- `X-RateLimit-Reset: 1582480798`
      - `Retry-After: 60`
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Rate limit exceeded", 
        "retry": 60
      }
      ```

-----------

#### /{endpoint}/auth/logout : GET
- decription: Log user out
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
- response:
  - http_code: 200

-----------

#### /{endpoint}/auth/users : GET
- decription: Get all users
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - request (with limit - optional): `limit=10`
    - request (with offset - optional): `offset=100`
    - request (with limit & offset): `limit=5&offset=50`
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "count": 10, 
        "total": 2314,
        "offset": 100,
        "users": [
          { 
            "uuid": "uuidstring1", 
            "username": "jozkomrkvicka", 
            "permissions": ["admin", "worker"],
            "canaries": [],
            "created_at": "2020-02-19 08:46:28",
            "updated_at": "2020-02-19 08:46:28"
          }, 
          { 
            "uuid": "uuidstring2", 
            "username": "peter",
            "permissions": ["worker"],
            "canaries": ["uuidstring1", "uuidstring2"],
            "created_at": "2020-02-19 11:33:01",
            "updated_at": "2020-02-19 11:33:01"
          }
        ]
      }
      ```
- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```
	
- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2, 
        "message": "Invalid range"
      }
      ```

-----------

#### /{endpoint}/auth/users/{uuid} : GET
- decription: Get a particular user information
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - __{uuid}__: Public user identification string
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "uuid": "uuidstring",
        "username": "jozkomrkvicka",
        "permissions": ["admin", "worker"], 
        "canaries": ["uuidstring1", "uuidstring2"],
        "created_at": "2020-02-19 08:46:28",
        "updated_at": "2020-02-19 08:46:28"
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```
	
- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2,
        "message": "Bad request",
        "details": "..."
      }
      ```

-----------

#### /{endpoint}/auth/users : POST
- decription: Create a new user
- request
  - parameters:
    - http_headers: 
      
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - body: 
      ```json
      {
        "users": [
          {
            "username": "peter",
            "password": "heslo",
            "permissions": ["worker"],
            "canaries": ["uuidstring1", "uuidstring2"]
          }
        ]
      }
	    ```
	
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "users": [
          {
            "username": "peter",
            "permissions": ["worker"],
            "canaries": ["uuidstring1", "uuidstring2"],
            "uuid": "uuidstring",
            "created_at": "2020-02-19 08:46:28",
            "updated_at": "2020-02-19 08:46:28"
          }
        ]
      }
	    ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2,
        "message": "Bad request",
        "details": "..."
      }
      ```

-----------

#### /{endpoint}/auth/users/{uuid} : PUT
- decription: Update a particular user
- request
  - parameters:
    - http_headers: 
      
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - __{uuid}__: Public user identification string
    - body: 
      ```json
      {
        "username": "peter1",
        "password": "noveheslo",
        "permissions": {"admin": true, "worker": false}
      }
      ```
- response:
  - http_code: 200
  
- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2,
        "message": "Bad request",
        "details": "..."
      }
      ```

-----------

#### /{endpoint}/auth/users/{uuid} : DELETE
- decription: Delete a particular user
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - __{uuid}__: Public user identification string
- response:
  
- http_code: 200
  
- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```

-----------

#### /{endpoint}/auth/refresh_token : GET
- decription: Get new access token
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "token": "JWT_ACCESSTOKEN",
        "token_type": "bearer",
        "expires": 3600
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```

-----------
-----------

### <a name="route-domains"></a>Domains

#### /{endpoint}/domains : GET
- description: Prints all available canary domains
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - request (with limit - optional): `limit=10`
    - request (with offset - optional): `offset=100`
    - request (with limit & offset): `limit=5&offset=50`
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "count": 10,
        "total": 455,
        "offset": 100,
        "domains": [
          {
            "uuid": "uuidstring1", 
            "domain": "domainname.tld",
            "created_at": "2020-02-19 08:46:28",
            "updated_at": "2020-02-19 08:46:28"
          },
          {
            "uuid": "uuidstring2", 
            "domain": "another.tld",
            "created_at": "2020-02-19 08:46:28",
            "updated_at": "2020-02-19 08:46:28"
           }
        ]
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```
	
- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2, 
        "message": "Invalid range"
      }
      ```

-----------

#### /{endpoint}/domains : POST
- description: Add new domain(s)
- request:
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - body: 
      ```json
      {
        "domains": [
          "domena.sk"
        ]
      }
      ```
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "domains": [
          {
            "uuid": "uuidstring",
            "domain": "domena.sk",
            "created_at": "2020-02-19 08:46:28",
            "updated_at": "2020-02-19 08:46:28"
          }
        ]
	    }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
		    "code": 2,
        "message": "Bad request",
        "details": "Domains already exist..."
      }
      ```

-----------

#### /{endpoint}/domains/{uuid} : DELETE
- decription: Delete a domain
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - __{uuid}__: Canary domain uuid
- response:
  - http_code: 200

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```

-----------
-----------

### <a name="route-sites"></a>Monitored sites

#### /{endpoint}/sites : GET
- description: Prints all monitored sites
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - request (with limit - optional): `limit=10`
    - request (with offset - optional): `offset=100`
    - request (with limit & offset): `limit=5&offset=50`
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "count": 10,
        "total": 234,
        "offset": 100,
        "sites": [
          {
            "uuid": "uuidstring1", 
            "site": "facebook.com",
            "created_at": "2020-02-19 08:46:28",
            "updated_at": "2020-02-19 08:46:28"
          },
          {
            "uuid": "uuidstring2", 
            "site": "azet.sk",
            "created_at": "2020-02-19 08:46:28",
            "updated_at": "2020-02-19 08:46:28"
          }
        ]
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body:
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```
	
- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2, 
        "message": "Invalid range"
      }
      ```

-----------

#### /{endpoint}/sites : POST
- description: Add new site(s)
- request:
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - body: 
      ```json
      {
        "sites": [
          "bazos.sk"
        ]
      }
      ```
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "sites": [
          {
            "uuid": "uuidstring",
            "site": "bazos.sk",
            "created_at": "2020-02-19 08:46:28",
            "updated_at": "2020-02-19 08:46:28"
          }
        ]
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body:  
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```
	
- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2,
        "message": "Bad request",
        "details": "Sites already exist..."
      }
      ```

-----------

#### /{endpoint}/sites/{uuid} : DELETE
- decription: Delete a site
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - __{uuid}__: Monitored site uuid
- response:
  - http_code: 200

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```

-----------
-----------

### <a name="route-canaries"></a>Canary nodes

#### /{endpoint}/canaries : GET
- description: Prints all canary nodes available to the user
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - request (with limit - optional): `limit=10`
    - request (with offset - optional): `offset=100`
    - request (with limit & offset): `limit=5&offset=50`
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "count": 10,
        "total": 11456,
        "offset": 100,
        "canaries": [
          {
            "uuid": "uuidstring",
            "domain": "uuidstring",
            "site": "uuidstring",
            "assignee": "uuidstring",
            "testing": false,
            "data": {
              "username": "milan.paradajka",
              "password": "hesielko123",
              "name": "Milan",
              "surname": "Paradajka",
              "phone": "+412 123 456 789"
            },
            "created_at": "2020-02-19 08:46:28",
            "updated_at": "2020-02-19 08:46:28"
          }
        ]
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```
	
- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2, 
        "message": "Invalid range"
      }
      ```

-----------

#### /{endpoint}/canaries/{uuid} : GET
- description: Prints information about a particular canary node
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - __{uuid}__: Canary node uuid
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "uuid": "uuidstring",
        "domain": "uuidstring",
        "site": "uuidstring",
        "assignee": "uuidstring",
        "testing": false,
        "data": {
          "username": "milan.paradajka",
          "password": "hesielko123",
          "name": "Milan",
          "surname": "Paradajka",
          "phone": "+412 123 456 789"
        },
        "created_at": "2020-02-19 08:46:28",
        "updated_at": "2020-02-19 08:46:28"
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2,
        "message": "Bad request",
        "details": "Canary does not exist..."
      }
      ```

-----------

#### /{endpoint}/canaries/{uuid}/{parameter} : GET
- description: Gets or generates fake information for the canary node registration process
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - __{uuid}__: Canary node uuid
    - __{parameter}__: Parameter name
      - __username__
      - __password__
      - __email__
      - __site__
      - __testing__
      - firstname
      - lastname
      - birthday
      - sex
      - address
      - phone
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "parameter": "value"
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2,
        "message": "Bad request",
        "details": "..."
      }
      ```

-----------

#### /{endpoint}/canaries/{uuid}/{parameter}/new : GET
- description: Re-generates fake information
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - __{uuid}__: Canary node uuid
    - __{parameter}__: Parameter name
      - firstname
      - lastname
      - birthday
      - sex
      - address
      - phone
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "parameter": "value"
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2,
        "message": "Bad request",
        "details": "..."
      }
      ```

-----------

#### /{endpoint}/canaries : POST
- description: Create new canary nodes
- request:
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - body: 
      ```json
      {
        "domain": "uuidstring",
        "site": "uuidstring",
        "testing": false,
        "count": 10
		  }
      ```
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "canaries": [
          {
            "uuid": "uuidstring", 
            "domain": "uuidstring", 
            "site": "uuidstring", 
            "assignee": "uuidstring", 
            "testing": false, 
            "data": { 
              "username": "milan.paradajka", 
              "password": "hesielko123", 
            },
            "created_at": "2020-02-19 08:46:28",
            "updated_at": "2020-02-19 08:46:28"
          }
        ]
	    }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2,
        "message": "Bad request",
        "details": "..."
      }
      ```

-----------

#### /{endpoint}/canaries/{uuid} : DELETE
- decription: Delete a canary node
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - __{uuid}__: Canary node uuid
- response:
  - http_code: 200

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```

-----------
-----------

### <a name="route-mail"></a>Mail

#### /{endpoint}/mail/{uuid} : GET
- description: Prints all e-mails received by a particular canary account
- request
  - parameters:
    - http_headers: 
      - `Authentication: "bearer JWT_ACCESSTOKEN"`
    - __{uuid}__: Canary node uuid
    - request (with limit - optional): `limit=10`
    - request (with offset - optional): `offset=100`
    - request (with limit & offset): `limit=5&offset=50`
- response:
  - http_code: 200
  - parameters:
    - body: 
      ```json
      {
        "count": 3,
        "total": 3,
        "offset": 0,
        "emails": [
          {
            "uuid": "uuidstring",
            "from": "sender@domain.tld",
            "subject": "message subject",
            "body": "raw body",
            "created_at": "2020-02-19 08:46:28",
            "updated_at": "2020-02-19 08:46:28"
          }
        ]
      }
      ```

- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 0, 
        "message": "Token not provided"
      }
      ```

- response:
  - http_code: 401
  - parameters:
    - body: 
      ```json
      {
        "code": 1, 
        "message": "Unauthorized",
        "details": "..."
      }
      ```
	
- response:
  - http_code: 400
  - parameters:
    - body: 
      ```json
      {
        "code": 2, 
        "message": "Invalid range"
      }
      ```