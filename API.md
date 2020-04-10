# Canaries API

## Endpoints

-   v1 - API version 1

## Routes

-   [Auth](#route-auth)
    -   [Login](#route-auth-login)
    -   [Logout](#route-auth-logout)
    -   [Get all users](#route-auth-getUsers)
    -   [Get a particular user](#route-auth-getUser)
    -   [Add new users](#route-auth-addUsers)
    -   [Update a particular user](#route-auth-updateUser)
    -   [Delete a particular user](#route-auth-deleteUser)
-   [Domains](#route-domains)
    -   [Get all domains](#route-domains-get)
    -   [Add new domains](#route-domains-add)
    -   [Delete a particular domain](#route-domains-delete)
-   [Monitored sites](#route-sites)
    -   [Get all sites](#route-sites-get)
    -   [Add new sites](#route-sites-add)
    -   [Delete a particular site](#route-sites-delete)
-   [Canary nodes](#route-canaries)
    -   [Get all canary nodes](#route-canaries-get)
    -   [Generate new fake data for canary node](#route-canaries-gen)
    -   [Re-generate fake data for canary node](#route-canaries-genNew)
    -   [Delete fake data for canary node](#route-canaries-genDel)
    -   [Create new canary nodes](#route-canaries-add)
    -   [Update canary node](#route-canaries-update)
    -   [Delete a particular canary node](#route-canaries-delete)
-   [Mail](#route-mail)
    -   [Get all mail for a particular canary node](#route-mail-get)

### <a name="route-auth"></a>Auth

-   [Login](#route-auth-login)
-   [Logout](#route-auth-logout)
-   [Get all users](#route-auth-getUsers)
-   [Get a particular user](#route-auth-getUser)
-   [Add new users](#route-auth-addUsers)
-   [Update a particular user](#route-auth-updateUser)
-   [Delete a particular user](#route-auth-deleteUser)

#### <a name="route-auth-login"></a>/{endpoint}/auth/login : POST

-   description: Log in and get access token
-   request:
    -   parameters:
        -   body:
            ```json
            {
                "username": "user",
                "password": "secret123"
            }
            ```

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
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

-   response:
    -   http_code: 400
    -   parameters:
        -   http_headers:
            -   `X-RateLimit-Limit: 10`
            -   `X-RateLimit-Remaining: 7`
        -   body:
            ```json
            {
                "code": 0,
                "message": "Bad request",
                "details": "Invalid credentials..."
            }
            ```

*   response:
    -   http_code: 429
    -   parameters:
        -   http_headers:
            -   `X-RateLimit-Limit: 10`
            -   `X-RateLimit-Remaining: 0`
            -   `X-RateLimit-Reset: 1582480798`
            -   `Retry-After: 60`
        -   body:
            ```json
            {
                "code": 1,
                "message": "Rate limit exceeded",
                "retry": 60
            }
            ```

---

#### <a name="route-auth-logout"></a>/{endpoint}/auth/logout : GET

-   decription: Log user out
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`

*   response:
    -   http_code: 200

---

#### <a name="route-auth-getUsers"></a>/{endpoint}/auth/users : GET

-   decription: Get all users
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   request (with limit - optional): `limit=10`
        -   request (with offset - optional): `offset=100`
        -   request (with limit & offset): `limit=5&offset=50`

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
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
                        "updated_at": "2020-02-19 08:46:28",
                        "updated_by": null
                    },
                    {
                        "uuid": "uuidstring2",
                        "username": "peter",
                        "permissions": ["worker"],
                        "canaries": ["uuidstring1", "uuidstring2"],
                        "created_at": "2020-02-19 11:33:01",
                        "updated_at": "2020-02-19 11:33:01",
                        "updated_by": "adminuuidstring"
                    }
                ]
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Invalid range"
            }
            ```

---

#### <a name="route-auth-getUser"></a>/{endpoint}/auth/users/{uuid} : GET

-   decription: Get a particular user information
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   **{uuid}**: Public user identification string

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
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

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Bad request",
                "details": "..."
            }
            ```

---

#### <a name="route-auth-addUsers"></a>/{endpoint}/auth/users : POST

-   decription: Create a new user
-   request
    -   parameters:
        -   http_headers:
            
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
            
        - permissions: User permissions - can be: `admin`, `worker` or `expert`
        
        -   body:
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

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
            ```json
            {
                "users": [
                    {
                        "username": "peter",
                        "permissions": ["worker"],
                        "canaries": ["uuidstring1", "uuidstring2"],
                        "uuid": "uuidstring",
                        "created_at": "2020-02-19 08:46:28",
                        "updated_at": "2020-02-19 08:46:28",
                        "updated_by": "adminuuidstring"
                    }
                ]
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Bad request",
                "details": "..."
            }
            ```

---

#### <a name="route-auth-updateUser"></a>/{endpoint}/auth/users/{uuid} : PUT

-   decription: Update a particular user
-   request
    -   parameters:
        -   http_headers:
            
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
            
        - **{uuid}**: Public user identification string
        
        - permissions: User permissions - can be: `admin`, `worker` or `expert`
        
        -   body:
            ```json
            {
                "username": "peter1",
                "password": "noveheslo",
                "permissions": ["admin"],
                "canaries": ["uuidstring"]
            }
            ```

*   response:
    -   http_code: 200

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Bad request",
                "details": "..."
            }
            ```

---

#### <a name="route-auth-deleteUser"></a>/{endpoint}/auth/users/{uuid} : DELETE

-   decription: Delete a particular user
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   **{uuid}**: Public user identification string

*   response:
    -   http_code: 200

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

---

#### <a name="route-auth-refreshToken"></a>/{endpoint}/auth/refresh_token : GET

-   decription: Get new access token
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
            ```json
            {
                "token": "JWT_ACCESSTOKEN",
                "token_type": "bearer",
                "expires": 3600
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

---

---

### <a name="route-domains"></a>Domains

-   [Get all domains](#route-domains-get)
-   [Add new domains](#route-domains-add)
-   [Delete a particular domain](#route-domains-delete)

#### <a name="route-domains-get"></a>/{endpoint}/domains : GET

-   description: Prints all available canary domains
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   request (with limit - optional): `limit=10`
        -   request (with offset - optional): `offset=100`
        -   request (with limit & offset): `limit=5&offset=50`

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
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

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Invalid range"
            }
            ```

---

#### <a name="route-domains-add"></a>/{endpoint}/domains : POST

-   description: Add new domain(s)
-   request:
    -   parameters:
        -   http_headers:
            
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   body:
            ```json
            {
                "domains": ["domena.sk"]
            }
            ```

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
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

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Bad request",
                "details": "Domains already exist..."
            }
            ```

---

#### <a name="route-domains-delete"></a>/{endpoint}/domains/{uuid} : DELETE

-   decription: Delete a domain
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   **{uuid}**: Canary domain uuid

*   response:
    -   http_code: 200

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

---

---

### <a name="route-sites"></a>Monitored sites

-   [Get all sites](#route-sites-get)
-   [Add new sites](#route-sites-add)
-   [Delete a particular site](#route-sites-delete)

#### <a name="route-sites-get"></a>/{endpoint}/sites : GET

-   description: Prints all monitored sites
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   request (with limit - optional): `limit=10`
        -   request (with offset - optional): `offset=100`
        -   request (with limit & offset): `limit=5&offset=50`

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
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

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Invalid range"
            }
            ```

---

#### <a name="route-sites-add"></a>/{endpoint}/sites : POST

-   description: Add new site(s)
-   request:
    -   parameters:
        -   http_headers:
            
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   body:
            ```json
            {
                "sites": ["bazos.sk"]
            }
            ```

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
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

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Bad request",
                "details": "Sites already exist..."
            }
            ```

---

#### <a name="route-sites-delete"></a>/{endpoint}/sites/{uuid} : DELETE

-   decription: Delete a site
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   **{uuid}**: Monitored site uuid

*   response:
    -   http_code: 200

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

---

---

### <a name="route-canaries"></a>Canary nodes

-   [Get all canary nodes or canary details](#route-canaries-get)
-   [Generate new fake data for canary node](#route-canaries-gen)
-   [Re-generate fake data for canary node](#route-canaries-genNew)
-   [Delete fake data for canary node](#route-canaries-genDel)
-   [Create new canary nodes](#route-canaries-add)
-   [Update canary node](#route-canaries-update)
-   [Delete a particular canary node](#route-canaries-delete)

#### <a name="route-canaries-get"></a>/{endpoint}/canaries : GET

-   description: Prints all canary nodes available to the user. If uuid or email parameters are specified, then only detail of that canary will be returned.
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   request (with limit - optional): `limit=10`
        -   request (with offset - optional): `offset=100`
        -   request (with limit & offset): `limit=5&offset=50`
        -   request (with uuid): `uuid=uuidstring`
        -   request (with email): `email=milan.paradajka@domena.tld`

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
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
                        "setup": false,
                        "email": "milan.paradajka@domena.tld",
                        "password": "hesielko123",
                        "data": {
                            "username": "milan.paradajka",
                            "firstname": "Milan",
                            "lastname": "Paradajka",
                            "phone": "+412 123 456 789"
                        },
                        "created_at": "2020-02-19 08:46:28",
                        "updated_at": "2020-02-19 08:46:28",
                        "updated_by": "adminuuidstring"
                    }
                ]
            }
            ```

-   response:
    -   http_code: 200
    -   parameters:
        -   body:
            ```json
            {
                "uuid": "uuidstring",
                "domain": "uuidstring",
                "site": "uuidstring",
                "assignee": "uuidstring",
                "testing": false,
                "setup": true,
                "email": "milan.paradajka@domena.tld",
                "password": "hesielko123",
                "data": {
                    "username": "milan.paradajka",
                    "firstname": "Milan",
                    "lastname": "Paradajka",
                    "phone": "+412 123 456 789"
                },
                "created_at": "2020-02-19 08:46:28",
                "updated_at": "2020-02-19 08:46:28",
                "updated_by": "adminuuidstring"
            }
            ```

*   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

-   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

*   response:

    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Invalid range"
            }
            ```

*   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 3,
                "message": "Bad request",
                "details": "Canary does not exist..."
            }
            ```

---

#### <a name="route-canaries-gen"></a>/{endpoint}/canaries/{uuid}/{parameter} : GET

-   description: Generates fake information for the canary node registration process
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   **{uuid}**: Canary node uuid
        -   **{parameter}**: Parameter name
            -   username
            -   firstname
            -   lastname
            -   birthday
            -   sex
            -   address
            -   phone

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
            ```json
            {
                "parameter": "value"
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Bad request",
                "details": "..."
            }
            ```

---

#### <a name="route-canaries-genNew"></a>/{endpoint}/canaries/{uuid}/{parameter} : POST

-   description: Re-generates fake information
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   **{uuid}**: Canary node uuid
        -   **{parameter}**: Parameter name
            -   username
            -   firstname
            -   lastname
            -   birthday
            -   sex
            -   address
            -   phone

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
            ```json
            {
                "parameter": "value"
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Bad request",
                "details": "..."
            }
            ```

---

#### <a name="route-canaries-genDel"></a>/{endpoint}/canaries/{uuid}/{parameter} : DELETE

-   description: Deletes fake information
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   **{uuid}**: Canary node uuid
        -   **{parameter}**: Parameter name
            -   username
            -   firstname
            -   lastname
            -   birthday
            -   sex
            -   address
            -   phone

*   response:
    -   http_code: 200

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

---

#### <a name="route-canaries-add"></a>/{endpoint}/canaries : POST

-   description: Create new canary nodes
-   request:
    -   parameters:
        -   http_headers:
            
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   password strength (optional - default `random`):
            -   `dictionary` - random password from the top 1 million
            -   `simple` - word + word(p=0.7) + digit + digit(p=0.5) + digit(p=0.1)
            -   `random` - 8 random characters from `[a-zA-Z0-9]`
            -   `strong` - 13 - 18 totally random characters
        -   body:
            ```json
            {
                "domain": "uuidstring",
                "site": "uuidstring",
                "testing": false,
                "count": 10,
                "password_strength": "simple"
            }
            ```

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
            ```json
            {
                "canaries": [
                    {
                        "uuid": "uuidstring",
                        "domain": "uuidstring",
                        "site": "uuidstring",
                        "assignee": "uuidstring",
                        "testing": false,
                        "setup": false,
                        "email": "milan.paradajka@domena.tld",
                        "password": "hesielko123",
                        "data": {
                            "username": "milan.paradajka"
                        },
                        "created_at": "2020-02-19 08:46:28",
                        "updated_at": "2020-02-19 08:46:28",
                        "updated_by": "adminuuidstring"
                    }
                ]
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Bad request",
                "details": "..."
            }
            ```

---

#### <a name="route-canaries-update"></a>/{endpoint}/canaries/{uuid} : PUT

-   description: Update canary information. Changing of every, but setup, parameter needs admin permission. Setup parameter can be updated by the assignee.
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   **{uuid}**: Canary node uuid
            -   site
            -   assignee
            -   testing
            -   setup

*   response:
    -   http_code: 200

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Bad request",
                "details": "..."
            }
            ```

---

#### <a name="route-canaries-delete"></a>/{endpoint}/canaries/{uuid} : DELETE

-   decription: Delete a canary node
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   **{uuid}**: Canary node uuid

*   response:
    -   http_code: 200

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

---

---

### <a name="route-mail"></a>Mail

-   [Get all mail for a particular canary node](#route-mail-get)

#### <a name="route-mail-get"></a>/{endpoint}/mail/{uuid} : GET

-   description: Prints all e-mails received by a particular canary account
-   request
    -   parameters:
        -   http_headers:
            -   `Authentication: "bearer JWT_ACCESSTOKEN"`
        -   **{uuid}**: Canary node uuid

*   response:
    -   http_code: 200
    -   parameters:
        -   body:
            ```json
            {
                "emails": [
                    {
                        "uuid": "uuidstring",
                        "canary": "uuidstring",
                        "received_on": "2020-02-19 08:45:00",
                        "from": "sender@domain.tld",
                        "subject": "message subject",
                        "body": "raw body",
                        "created_at": "2020-02-19 08:46:28",
                        "updated_at": "2020-02-19 08:46:28"
                    }
                ]
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 0,
                "message": "Token not provided"
            }
            ```

*   response:
    -   http_code: 401
    -   parameters:
        -   body:
            ```json
            {
                "code": 1,
                "message": "Unauthorized",
                "details": "..."
            }
            ```

-   response:
    -   http_code: 400
    -   parameters:
        -   body:
            ```json
            {
                "code": 2,
                "message": "Invalid range"
            }
            ```
