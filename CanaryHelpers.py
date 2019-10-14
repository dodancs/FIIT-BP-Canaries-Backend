import configparser
import sys
import random
import string

class CanaryHelpers:

    version = '1.0.0'

    yes = ('Y', 'y', 'yes', 'Yes', 'YES')

    def hello(self):
        print('Canaries backend v{} is starting up!'.format(self.version))

    # Parse configuration file
    def testConfig(self, configPath):
        try:
            config = configparser.RawConfigParser()
            config.read(configPath)

            config['DATABASE']
            config['DATABASE']['host']
            config['DATABASE']['port']
            config['DATABASE']['user']
            config['DATABASE']['password']
            config['DATABASE']['database']

            config['SECURITY']
            config['SECURITY']['secret']

            config['API']
            config['API']['bind_ip']
            config['API']['port']
            config['API']['debug']
            config['API']['swagger']
            config['API']['swagger_url']

        except configparser.Error as e:
            print('Unable to load configuration file!', file=sys.stderr)
            print('Error: {}'.format(e), file=sys.stderr)
            exit(1)
        except KeyError as e:
            print('Error parsing configuration file, missing {} attribute.'.format(e), file=sys.stderr)
            exit(1)

        print("Using: {}".format(configPath))
        return config

    def randomString(self, stringLength=10):
        password_characters = string.ascii_letters + string.digits + string.punctuation
        return ''.join(random.choice(password_characters) for i in range(stringLength))