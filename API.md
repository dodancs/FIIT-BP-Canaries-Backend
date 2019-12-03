# Canaries API
## Endpoints

### Auth

#### /auth/login : POST
- description: Log in and get access token
- request:
  - parameters:
    - username: `username`
    - password: `secret123`
- response:
  - http_code: 200
  - parameters:
    - body: ```{
		'token': 'JWT_ACCESSTOKEN', 
		'token_type': 'bearer', 
		'expires': 3600,
		'uuid': 'uuidstring', 
		'permissions': { admin:true, worker: false }
      }```
- response:
  - http_code: 404
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'User does not exist'
      }```
- response:
  - http_code: 403
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Bad password'
      }```
- response:
  - http_code: 429
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Login rate limit exceeded', 
		'retry': 3600
      }```

-----------

#### /auth/logout : GET
- decription: Log user out
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
- response:
  - http_code: 200

-----------

#### /auth/users : GET
- decription: Get all users
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - limit (optional): `10`
    - offset (optional): `100`
- response:
  - http_code: 200
  - parameters:
    - body: ```{
		'count': 10, 
		'total': 2314, 
		'users': [
				{ 
					'uuid': 'uuidstring1', 
					'username': 'jozkomrkvicka', 
					'permissions': { 'admin': true, 'worker': false } 
				}, 
				{ 
					'uuid': 'uuidstring2', 
					'username': 'peter',
					'permissions': { 'admin': false, 'worker': true } 
				},
				...
		]
      }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

-----------

#### /auth/users/{uuid} : GET
- decription: Get a particular user information
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - __{uuid}__: Public user identification string
- response:
  - http_code: 200
  - parameters:
    - body: ```{
		'uuid': 'uuidstring',
		'username': 'jozkomrkvicka',
		'permissions': { 'admin': false, 'worker': true, ... }, 
		'canaries': [ 'uuidstring1', 'uuidstring2', ... ]
      }```

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

-----------

#### /auth/users : POST
- decription: Create a new user
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - username: `peter`
    - password: `heslo`
    - permissions: `{ 'admin': true, 'worker': false }`
- response:
  - http_code: 200
  - parameters:
    - body: ```{
		'uuid': 'uuidstring',
		'username': 'peter',
		'permissions': { 'admin': true, 'worker': false, ... }
      }```

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

- response:
  - http_code: 409
  - parameters:
    - body: ```{
		'code': 3,
		'message': 'User already exists'
      }```

-----------

#### /auth/users/{uuid} : PUT
- decription: Update a particular user
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - __{uuid}__: Public user identification string
    - username (optional): `peter`
    - password (optional): `heslo`
    - permissions (optional): `{ 'worker': true }`
- response:
  - http_code: 200

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

- response:
  - http_code: 404
  - parameters:
    - body: ```{
		'code': 3,
		'message': 'User does not exist'
      }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 4,
		'message': 'Bad request'
      }```

-----------

#### /auth/users/{uuid} : DELETE
- decription: Delete a particular user
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - __{uuid}__: Public user identification string
- response:
  - http_code: 200

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2,
		'message': 'Token expired'
      }```

-----------

#### /auth/refresh_token : GET
- decription: Get new access token
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
- response:
  - http_code: 200
  - parameters:
    - body: ```{
		'token': 'JWT_ACCESSTOKEN',
		'token_type': 'bearer',
		'expires': 3600,
      }```

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

-----------
-----------

### Domains

#### /domains : GET
- description: Prints all available canary domains
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - limit (optional): `10`
    - offset (optional): `100`
- response:
  - http_code: 200
  - parameters:
    - body: ```{
		'count': 10,
		'total': 455,
		'domains': [
				{ 'uuid': 'uuidstring1', 'domain': 'domainname.tld' },
				{ 'uuid': 'uuidstring2', 'domain': 'another.tld' },
			...
		]
      }```

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

-----------

#### /domains : POST
- description: Add new domain
- request:
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - domain: `domena.sk`
- response:
  - http_code: 200
  - parameters:
    - body: ```{
		'uuid': 'uuidstring',
		'domain': 'domena.sk'
      }```

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 3,
		'message': 'Invalid domain'
      }```

- response:
  - http_code: 409
  - parameters:
    - body: ```{
		'code': 4,
		'message': 'Domain already exists'
      }```

-----------

#### /domains/{uuid} : DELETE
- decription: Delete a domain
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - __{uuid}__: Canary domain uuid
- response:
  - http_code: 200

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

-----------
-----------

### Monitored sites

#### /sites : GET
- description: Prints all monitored sites
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - limit (optional): `10`
    - offset (optional): `100`
- response:
  - http_code: 200
  - parameters:
    - body: ```{
		'count': 10,
		'total': 234,
		'sites': [
				{ 'uuid': 'uuidstring1', 'site': 'facebook.com' },
				{ 'uuid': 'uuidstring2', 'site': 'azet.sk' },
				...
		]
      }```

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

-----------

#### /sites : POST
- description: Add new site
- request:
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - site: `bazos.sk`
- response:
  - http_code: 200
  - parameters:
    - body: ```{
		'uuid': 'uuidstring',
		'site': 'bazos.sk'
      }```

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

-----------

#### /sites/{uuid} : DELETE
- decription: Delete a site
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - __{uuid}__: Monitored site uuid
- response:
  - http_code: 200

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

-----------
-----------

### Canary nodes

#### /canaries : GET
- description: Prints all canary nodes available to the user
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - limit (optional): `10`
    - offset (optional): `100`
- response:
  - http_code: 200
  - parameters:
    - body: ```{
		'count': 10,
		'total': 11456,
		'canaries': [
				{
					'uuid': 'uuidstring',
					'domain': 'uuidstring',
					'site': 'uuidstring',
					'assignee': 'uuidstring',
					'testing': false,
					'data': {
						'username': 'milan.paradajka',
						'password': 'hesielko123',
						'name': 'Milan',
						'surname': 'Paradajka',
						'phone': '+412 123 456 789'
					}
				},
				...
			]
      }```

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

-----------

#### /canaries/{uuid} : GET
- description: Prints information about a particular canary node
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - __{uuid}__: Canary node uuid
- response:
  - http_code: 200
  - parameters:
    - body: ```{
		'uuid': 'uuidstring',
		'domain': 'uuidstring',
		'site': 'uuidstring',
		'assignee': 'uuidstring',
		'testing': false,
		'data': {
				'username': 'milan.paradajka',
				'password': 'hesielko123',
				'name': 'Milan',
				'surname': 'Paradajka',
				'phone': '+412 123 456 789',
				...
		}
      } ```

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

- response:
  - http_code: 404
  - parameters:
    - body: ```{
		'code': 3,
		'message': 'Canary does not exist'
      }```

-----------

#### /canaries/{uuid}/{parameter} : GET
- description: Generates fake information for the canary node registration process
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
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
    - body: ```{
		'uuid': 'uuidstring',
		'domain': 'uuidstring',
		'site': 'uuidstring',
		'assignee': 'uuidstring',
		'testing': false,
		'data': {
				'username': 'milan.paradajka',
				'password': 'hesielko123',
				'name': 'Milan',
				'surname': 'Paradajka',
				'phone': '+412 123 456 789',
				'parameter': 'value',
				...
		}
      }```

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

- response:
  - http_code: 404
  - parameters:
    - body: ```{
		'code': 3,
		'message': 'Canary node does not exist'
      }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 4,
		'message': 'Unknown parameter'
      }```

-----------

#### /canaries : POST
- description: Create new canary nodes
- request:
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - domain: `uuidstring`
    - site: `uuidstring`
    - testing: `false`
    - cout: `10`
- response:
  - http_code: 200
  - parameters:
    - body: ```[
			{
				'uuid': 'uuidstring', 
				'domain': 'uuidstring', 
				'site': 'uuidstring', 
				'assignee': 'uuidstring', 
				'testing': false, 
				'data': { 
					'username': 'milan.paradajka', 
					'password': 'hesielko123', 
				} 
			},
			... 
      ] ```

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```
- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 3, 
		'message': 'Bad request'
      }```

-----------

#### /canaries/{uuid} : DELETE
- decription: Delete a canary node
- request
  - parameters:
    - http_headers: 
      - ```Authentication: 'bearer JWT_ACCESSTOKEN'```
    - __{uuid}__: Canary node uuid
- response:
  - http_code: 200

- response:
  - http_code: 400
  - parameters:
    - body: ```{
		'code': 0, 
		'message': 'Token not provided'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 1, 
		'message': 'Unauthorized'
      }```

- response:
  - http_code: 401
  - parameters:
    - body: ```{
		'code': 2, 
		'message': 'Token expired'
      }```

