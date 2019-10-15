# APILDS API
A - Active
P - Personal
I - Information
L - Leak
D - Detection
S - System
či?

## Endpoints

### Auth

#### /auth : GET
- description: Prints auth module version
- response:
  - http_code: 200
  - parameters:
    - body: ```{'version': [1,0,0]}```

-----------

#### /auth/login : POST
- description: Get access token
- request:
  - parameters:
    - body: ```{'username': 'janko', 'password': 'secret'}```
- response:
  - http_code: 200
  - parameters:
    - body: ```{'token': 'accesstoken', 'uuid': 'uuidstring', 'expire': '14-10-2019-23-59-59' }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'User does not exist'}```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 1, 'message': 'Bad password'}```

-----------

#### /auth/logout : GET
- decription: Log user out
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
- response:
  - http_code: 200

-----------

#### /auth/users : GET
- decription: Get all users
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
- response:
  - http_code: 200
  - parameters:
    - body: ```{ 'users': [ { 'uuid': 'uuidstring', 'admin': true, 'worker': false } ] }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

-----------

#### /auth/users/{uuid} : GET
- decription: Get a particular user information
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - __uuid__: Public user identification string
- response:
  - http_code: 200
  - parameters:
    - body: ```{ 'user': { 'uuid': 'uuidstring', 'admin': true, 'worker': false } }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

-----------

#### /auth/users : POST
- decription: Create a new user
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - body: ```{'username': 'peter', 'password': 'heslo'}```
- response:
  - http_code: 200
  - parameters:
    - body: ```{ 'users': [ { 'uuid': 'uuidstring', 'admin': true, 'worker': false } ] }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 1, 'message': 'User already exists'}```

-----------

#### /auth/users/{uuid} : PUT
- decription: Update a particular user
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - __uuid__: Public user identification string
    - body: ```{'username': 'peter', 'password': 'heslo', 'worker': true}```
- response:
  - http_code: 200
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 1, 'message': 'User does not exist'}```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 2, 'message': 'Bad parameters'}```

-----------

#### /auth/users/{uuid} : DELETE
- decription: Delete a particular user
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - __uuid__: Public user identification string
- response:
  - http_code: 200
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 1, 'message': 'Cannot delete self'}```

-----------

#### /auth/tokens : GET
- decription: Get all access tokens
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
- response:
  - http_code: 200
  - parameters:
    - body: ```{ 'tokens': [ { 'uuid': 'uuidstring', 'token': 'accesstoken', 'permanent': false, 'expire': '14-10-2019-23-59-59' } ] }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

-----------

#### /auth/tokens : PUT
- decription: Update access token expiration - returns a new token
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
- response:
  - http_code: 200
  - parameters:
    - body: ```{ 'token': { 'uuid': 'uuidstring', 'token': 'accesstoken', 'permanent': false, 'expire': '15-10-2019-23-59-59' } }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Token expired'}```

-----------

#### /auth/tokens/{token} : DELETE
- decription: Delete access token
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - __token__: Access token
- response:
  - http_code: 200
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

-----------
-----------

### Domains

#### /domains : GET
- description: Prints all available canary domains
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
- response:
  - http_code: 200
  - parameters:
    - body: ```{ 'domains': [ 'domainname.tld', 'another.tld' ] }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

-----------

#### /domains : POST
- description: Add new domain
- request:
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - body: ```{ 'domains': [ 'domena.sk' ] }```
- response:
  - http_code: 200
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

-----------

#### /domains/{domain} : DELETE
- decription: Delete a domain
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - __domain__: Canary domain name
- response:
  - http_code: 200
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

-----------
-----------

### Monitored sites

#### /sites : GET
- description: Prints all monitored sites
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
- response:
  - http_code: 200
  - parameters:
    - body: ```{ 'domains': [ 'facebook.com', 'azet.sk' ] }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

-----------

#### /sites : POST
- description: Add new site
- request:
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - body: ```{ 'sites': [ 'bazos.sk' ] }```
- response:
  - http_code: 200
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

-----------

#### /sites/{site} : DELETE
- decription: Delete a site
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - __site__: Monitored site
- response:
  - http_code: 200
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

-----------
-----------

### Canary nodes

#### /canaries : GET
- description: Prints all canary nodes available to the user
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
- response:
  - http_code: 200
  - parameters:
    - body: ```{ 'canaries': [ { 'id': 'd43uyy7f2y9', 'domain': 'domainname.tld', 'site': 'facebook.com', 'username': 'milan.paradajka', 'password': 'hesielko123', 'worker': 'uuidstring', 'testing': false, 'other': {} } ] }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

-----------

#### /canaries/{id} : GET
- description: Prints information about a particular canary node
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - __id__: Canary node ID
- response:
  - http_code: 200
  - parameters:
    - body: ```{ 'id': '3ry2o877r94', 'domain': 'other.tld', 'site': 'fortuna.sk', 'username': 'petra.kosicka', 'password': 'lubimferka', 'worker': 'uuidstring', 'testing': false, 'other': {} }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 1, 'message': 'Node does not exist'}```

-----------

#### /canaries/{id}/{parameter} : GET
- description: Generates and prints fake information for the canary node registration process
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - __id__: Canary node ID
    - __parameter__: Parameter name
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
    - body: ```{ 'parameter': 'value' }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 1, 'message': 'Node does not exist'}```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 2, 'message': 'Unknown parameter'}```

-----------

#### /canaries : POST
- description: Add new canary nodes
- request:
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - body: ```{ 'domain': 'domainname.tld', 'site': 'facebook.com', 'testing': false, 'cout': 10 }```
- response:
  - http_code: 200
  - parameters:
    - body: ```{ 'canaries': [ { 'id': 'd43uyy7f2y9', 'username': 'milan.paradajka', 'password': 'hesielko123', 'worker': 'uuidstring', 'other': {} } ] }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

-----------

#### /canaries/{id} : PUT
- description: Edit canary node
- request:
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - body: ```{ 'username': 'karol', 'password': 'lepsieheslo', 'testing': true, 'other': { 'address': 'Bratislava' } }```
    - __id__: Canary node ID
- response:
  - http_code: 200
  - parameters:
    - body: ```{ 'canaries': [ { 'id': 'd43uyy7f2y9', 'username': 'milan.paradajka', 'password': 'hesielko123', 'worker': 'uuidstring' } ] }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 1, 'message': 'Unknown paramter'}```

-----------

#### /canaries/{id} : DELETE
- decription: Delete a canary node
- request
  - parameters:
    - http_headers: 
      - ```X-Access-Token: 'accesstoken'```
    - __id__: Canary node ID
- response:
  - http_code: 200
- response:
  - http_code: 400
  - parameters:
    - body: ```{'code': 0, 'message': 'Unauthorized'}```

