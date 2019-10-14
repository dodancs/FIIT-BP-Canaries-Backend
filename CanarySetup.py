import sys
import os
import getpass
from CanaryHelpers import CanaryHelpers
from CanaryDBHandler import CanaryDBHandler

class ConfigData(object):
    # Database
    host = ''
    port = 0
    user = ''
    password = ''
    database = ''
    # Security
    secret = ''
    # API
    bindIP = ''
    bindPort = 0
    debug = False
    swagger = False
    swaggerURL = ''


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

        print()

        if CanaryDBHandler().test(data.host, data.port, data.user, data.password, data.database):
            exit(1)

        print('**************************\n\
~     SECURITY SETUP     ~\n\
**************************')

        print('Generating new secret key...')
        data.secret = CanaryHelpers().randomString(32).replace('\'','"')
        print('Done.\n')

        print('**************************\n\
~        API SETUP       ~\n\
**************************')

        i = input('REST API bind address (0.0.0.0): ')
        data.bindIP = '0.0.0.0' if not i else i

        try:
            i = input('REST API listen on port (5000): ')
            data.bindPort = 5000 if not i else int(i)
        except:
            print('Port must be a number!', file=sys.stderr)
            exit(1)

        data.debug = True if input('Enable debugging? (y/N): ') in CanaryHelpers().yes else False
        data.swagger = True if input('Enable swagger UI? (y/N): ') in CanaryHelpers().yes else False

        if data.swagger:
            i = input('Swagger URL (/swagger): ')
            data.swaggerURL = '/swagger' if not i else i
        else:
            data.swaggerURL = '/swagger'

        if os.path.exists(configPath):
            if input('Configuration file already exists ({}), overwrite? (y/N): '.format(configPath)) not in CanaryHelpers().yes : i
            
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

                config.write('\n')

                config.write('[API]\n')
                config.write('bind_ip=\'{}\'\n'.format(data.bindIP))
                config.write('port={}\n'.format(data.bindPort))
                config.write('debug={}\n'.format(data.debug))
                config.write('swagger={}\n'.format(data.swagger))
                config.write('swagger_url=\'{}\'\n'.format(data.swaggerURL))

                config.close()
            except IOError:
                print('Error writing configuration file!', file=sys.stderr)
                exit(1)

        print('Configuration updated!')
        print('You may now run canaries in daemon mode.')

        exit(0)