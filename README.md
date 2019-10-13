# FIIT BP Canaries - Backend

This repository holds source codes for the back-end portion of the Leak-detection system



## Contains

- Python backend for e-mail canaries



## Requirements

### Platform & services

- Windows Server or any Unix/Linux operating system

- MySQL Server - preferably MariaDB
- Postfix SMTP server
- Dovecot IMAP server



### Installing Python 3

Visit the [following URL]( https://www.python.org/downloads/ ) where you can see links for installation files for different operating systems.

Also do not forget to install PIP (Python module manager):

```powershell
$> wget https://bootstrap.pypa.io/get-pip.py
$> python get-pip.py
```

Verify python and pip installation:

```powershell
$> python
Python 3.7.0 (v3.7.0:1bf9cc5093, Jun 27 2018, 04:59:51) [MSC v.1914 64 bit (AMD64)] on win32
Type "help", "copyright", "credits" or "license" for more information.
exit();
$> python -m pip -V
pip 18.0 from c:\users\user\appdata\local\programs\python\python37\lib\site-packages\pip (python 3.7)
```



### Installing Python modules

To install all needed Python modules, download or clone this repository by doing the following command:

```powershell
$> git clone git@github.com:dodancs/FIIT-BP-Canaries-Backend.git
```

After the repository is cloned, you should see a file called `requirements.txt`. Using the following command you can install all required modules:

```powershell
$> cd ./FIIT-BP-Canaries-Backend
$> python -m pip install -r requirements.txt
```



## Setup

### Automatic setup

To start the automatic setup run the canaries.py script with the --setup argument:

```powershell
$> cd ./FIIT-BP-Canaries-Backend
$> python canaries.py --setup
```



The on-screen instructions will guide you through the setup procedure. You will need a MySQL user account with access to one database which will also contain configuration for the Postfix and Dovecot services.



### Manual setup

Copy the included `config.example.ini` and rename it to `config.ini`. The structure of this file should look like this:

```ini
[DATABASE]
host='127.0.0.1'
port=3306
user='canaries'
password='password'
database='canaries'

[SECURITY]
secret='somesecretkey'
```

Change all necessary attributes. Make sure that the <u>secret</u> is fairly long (preferably at least 32 characters) string made up of different letters, numbers and other characters.

**<font color="red"><u>WARNING: Do not change the secret after the first setup! This will lead into potential data loss!</u></font>**



## Running the daemon

```powershell
$> cd ./FIIT-BP-Canaries-Backend
$> python canaries.py &
```



## Authors

- Dominik Dancs - dodancs11@gmail.com, https://dodancs.moow.info/

