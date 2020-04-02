# FIIT BP Canaries - Backend

This repository holds source code for the back-end portion with full API documentation of the leak-detection system. 

## Authors

- Dominik Dancs - dodancs@moow.info, https://dodancs.moow.info/

## Requirements

### Platform & services

#### For development
- Windows Server or any Unix/Linux operating system
- SQL Server - preferably MariaDB
- PHP 7.3
- Visual Studio Code
- git
- PHP composer
- Windows subsystem for Linux (on Windows) with Ubuntu distribution installed

#### For production

- Windows Server or any Unix/Linux operating system
- SQL Server - preferably MariaDB
- PHP
- NginX / Apache

### How to run

Before running the API a few things need to be taken care of:

Create a MySQL user and database:
```bash
~$ mysql -u root -p
mysql> CREATE USER 'user'@'host' IDENTIFIED BY 'password';
mysql> GRANT ALL PRIVILEGES ON `database` . * TO 'user'@'host';
mysql> FLUSH PRIVILEGES;
mysql> CREATE DATABASE `database`;
mysql> quit;
```

Clone and navigate to the project folder:
```bash
~$ git clone git@github.com:dodancs/FIIT-BP-Canaries-Backend.git
~$ cd /path/to/project/
```

Install required packages:
```bash
/path/to/project/$ composer update
```

Create an environment configuration file and setup connection to a database:
```bash
/path/to/project/$ cp .env.example .env
/path/to/project/$ vi .env
```

Setup connection to MySQL (use credentials from the previous step) in `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=host
DB_PORT=port
DB_DATABASE=database
DB_USERNAME=user
DB_PASSWORD=password
```

Generate APP key and JWT secret:
```bash
/path/to/project/$ php artisan tinker
>>> echo \Illuminate\Support\Str::random(32);
>>> echo \Illuminate\Support\Str::random(64);
>>> exit;
```
The two echo commands will generate a 32 char APP key and 64 char JWT_SECRET key. Set these in the `.env` file.
```ini
APP_KEY=MVmGWBQwJejrmOPCAyboh6DECGcXpADB
JWT_SECRET=EKXJXXDWKFBYXTR3Gc7w6Mii3atLHFrOzr3CvegkowXrE0zRQUBck4DlYkPp3yn8
```

You can also change any other relevant settings in the `.env` file.

Create database structure:
```bash
/path/to/project/$ php artisan migrate
```

Add an administrator user:
```bash
/path/to/project/$ php artisan tinker
>>> use App\Models\User;
>>> use Illuminate\Support\Facades\Hash;
>>> $user = new User(['username' => 'admin', 'password' => Hash::make('supersecret')])
>>> $user->permissions = ['admin']
>>> $user->save();
>>> exit;
```

#### Running using Visual Studio Code

- Start VSCode and open folder with the project files.
- _Note: The project contains pre-defined tasks to launch the API server on both Windows and Unix/Linux operating systems._
- To start the task open the command palette with `ctrl + shift + p` and type in `Run task`, press enter to select the command.
- Now select the task that matches your operating system: `Launch app: Linux` or `Launch app: Windows`
- The built-in terminal will open to show you the output of the web server

#### Running without Visual Studio Code

You may use the PHP internal web server to host the API:
```bash
~$ cd /path/to/project
/path/to/project/$ php -S localhost:8000 -t public
```

Otherwise, you can run Nginx or Apache discrete web servers.

Then just specify the root of the site to be in `/path/to/project/public` and allow the site access to `/path/to/project/` (the parent directory).

## API Documentation

- [Markdown](API.md)
- [PDF](API.pdf)
- [DOCX](API.docx)

##  Data models

- [Data models](data-model.md)
- [PNG](data-model.png)
- [UXF](data-model.uxf)
