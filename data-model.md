# Canaries API data models

![Data models](data-model.png)

## TOC

- [Table `users`](#toc-users)
- [Table `domains`](#toc-domains)
- [Table `sites`](#toc-sites)
- [Table `canaries`](#toc-canaries)
- [Table `mail`](#toc-mail)

## <a name="toc-users"></a>Table `users` [[SQL migration]](/database/migrations/2019_10_15_162034_create_users_table.php)

> *<< PK >>* `id` : **bigint(20)** : PRIMARY KEY, NOT NULL
>
> `uuid` : **char(36)** : UNIQUE, NOT NULL, INDEX
>
> `username` : **varchar(255)** : UNIQUE
>
> `passowrd` : **varchar(255)**
>
> `permissions` : **longtext**
>
> *<< FK >>* `updated_by` : **char(36)**: NULL, REFERENCES users(uuid)
>
> `created_at` : **timestamp**
>
> `updated_at` : **timestamp**

Table `users` holds credentials for users that may access the API interface.


## <a name="toc-domains"></a>Table `domains` [[SQL migration]](/database/migrations/2020_03_01_110921_create_domains_table.php)

> *<< PK >>* `id` : **bigint(20)** : PRIMARY KEY, NOT NULL
>
> `uuid` : **char(36)** : UNIQUE, NOT NULL, INDEX
>
> `domain` : **varchar(255)** : UNIQUE
>
> `created_at` : **timestamp**
>
> `updated_at` : **timestamp**

Table `domains` holds domain names that are available for canary account creation.


## <a name="toc-sites"></a>Table `sites` [[SQL migration]](/database/migrations/2020_03_01_100932_create_sites_table.php)

> *<< PK >>* `id` : **bigint(20)** : PRIMARY KEY, NOT NULL
>
> `uuid` : **char(36)** : UNIQUE, NOT NULL, INDEX
>
> `site` : **varchar(255)** : UNIQUE
>
> `created_at` : **timestamp**
>
> `updated_at` : **timestamp**

Table `sites` holds monitored site URLs that are to be monitored.


## <a name="toc-canaries"></a>Table `canaries` [[SQL migration]](/database/migrations/2020_03_01_130803_create_canaries_table.php)

> *<< PK >>* `id` : **bigint(20)** : PRIMARY KEY, NOT NULL
>
> `uuid` : **char(36)** : UNIQUE, NOT NULL, INDEX
>
> *<< FK >>* `domain` : **char(36)**: NOT NULL, INDEX, REFERENCES domains(uuid)
> 
> *<< FK >>* `site` : **char(36)**: NOT NULL, INDEX, REFERENCES sites(uuid)
> 
> *<< FK >>* `assignee` : **char(36)**: NULL, INDEX, REFERENCES users(uuid)
>
> `testing` : **tinyint(1)**
>
> `setup` : **tinyint(1)**
>
> `email` : **varchar(255)**
>
> `passowrd` : **varchar(255)**
>
> `data` : **longtext**
>
> *<< FK >>* `updated_by` : **char(36)**: NULL, REFERENCES users(uuid)
>
> `created_at` : **timestamp**
>
> `updated_at` : **timestamp**

Table `canaries` holds all generated canary accounts and their credentials and additional data.


## <a name="toc-mail"></a>Table `mail` [[SQL migration]]()

> *<< PK >>* `id` : **bigint(20)** : PRIMARY KEY, NOT NULL
>
> `uuid` : **char(36)** : UNIQUE, NOT NULL, INDEX
> 
> *<< FK >>* `canary` : **char(36)**: NOT NULL, INDEX, REFERENCES canaries(uuid)
>
> `from` : **varchar(255)**
>
> `subject` : **varchar(255)**
>
> `body` : **longtext**
>
> `created_at` : **timestamp**
>
> `updated_at` : **timestamp**

Table `users` holds credentials for users that may access the API interface.