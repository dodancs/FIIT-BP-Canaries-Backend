import sys
import os
import getpass
import mysql.connector
from mysql.connector import Error
from CanaryHelpers import CanaryHelpers

class ConfigData(object):
    host = ""
    port = 0
    user = ""
    password = ""
    database = ""
    secret = ""

class CanarySetup:
    def __init__(self, configPath = ''):
        data = ConfigData()

        if not configPath:
            configPath = 'config.ini'

        print('Welcome to Canaries Backend first time setup.')
        print('Please follow the on-screen instructions.')

        print('\n\nWARNING: DO NOT RUN THIS COMMAND AFTER FIRST')
        print('         INITIALIZATION! THIS WILL RESULT IN A')
        print('         BROKEN INSTANCE AND UNRECOVERABLE DATA!\n\n')
        print('**************************\n\
~     DATABASE SETUP     ~\n\
**************************')

        i = input('Database host (localhost): ')
        data.host = 'localhost' if not i else i

        try:
            i = input('Database port (3306): ')
            data.port = 3306 if not i else int(i)
        except:
            print('Port must be a number!', file=sys.stderr)
            exit(1)

        data.user = input('Connection user: ')
        data.password = getpass.getpass('Connection password: ')

        i = input('Database name (canaries): ')
        data.database = 'canaries' if not i else i

        print('\nChecking connection...')

        if self.testDB(data):
            exit(1)

        print('**************************\n\
~     SECURITY SETUP     ~\n\
**************************')

        print('Generating new secret key...')
        data.secret = CanaryHelpers().randomString(32).replace('\'','"')
        print('Done.')

        if os.path.exists(configPath):
            if input('Configuration file already exists ({}), overwrite? (y/N): '.format(configPath)) not in ('Y', 'y', 'yes', 'Yes', 'YES'):
                exit(0)
            
            try:
                config = open(configPath, "w")
            except IOError:
                print('Error opening configuration file!', file=sys.stderr)
                exit(1)

            try:
                config.write('[DATABASE]\n')
                config.write('host=\'{}\'\n'.format(data.host))
                config.write('port={}\n'.format(data.port))
                config.write('user=\'{}\'\n'.format(data.user))
                config.write('password=\'{}\'\n'.format(data.password))
                config.write('database=\'{}\'\n'.format(data.database))

                config.write('\n')

                config.write('[SECURITY]\n')
                config.write('secret=\'{}\'\n'.format(data.secret))

                config.close()
            except IOError:
                print('Error writing configuration file!', file=sys.stderr)
                exit(1)

        print('Configuration updated!')
        print('You may now run canaries in daemon mode.')

        exit(0)

    def testDB(self, data):
        connection = None
        try:
            connection = mysql.connector.connect(host=data.host, port=data.port, database=data.database, user=data.user, password=data.password)

            if connection.is_connected():
                db_Info = connection.get_server_info()
                print('Connected to MySQL Server version {}\n'.format(db_Info))
                cursor = connection.cursor()
                cursor.execute("select database();")
                cursor.fetchone()
                return 0

        except Error as e:
            print('Error while connecting to MySQL {}'.format(e), file=sys.stderr)
            return 1
        finally:
            if connection and connection.is_connected():
                cursor.close()
                connection.close()