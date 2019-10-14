from flask import Flask, jsonify
import os.path
import argparse
import sys
from CanarySetup import CanarySetup
from CanaryHelpers import CanaryHelpers
from CanaryDBHandler import CanaryDBHandler
from flask_swagger_ui import get_swaggerui_blueprint

from modules.API_Auth import *
from modules.API_Workers import *
from modules.API_Domains import *
from modules.API_Sites import *
from modules.API_Canaries import *

parser = argparse.ArgumentParser(prog='canaries', description='Canary REST API backend.', epilog='Please visit https://github.com/dodancs/FIIT-BP-Canaries-Backend for more information!')
parser.add_argument('--setup', action='store_true', help='first time setup')
parser.add_argument('-c', '--config', type=str, help='configuration file path')

args = parser.parse_args()

configPath = args.config if args.config else 'config.ini'

# First time setup
if args.setup:
    CanarySetup(configPath)

# Say hello
CanaryHelpers().hello()

# Test configuration file
if not os.path.exists(configPath):
    print("Cannot open config file {}. Running first-time setup...".format(configPath.strip()))
    CanarySetup(configPath)

config = CanaryHelpers().testConfig(configPath)

# Flask REST API daemon
app = Flask(__name__)

# Connect to database
CanaryDBHandler().connect(app, config)

# Swagger UI
if config['API']['swagger'] == 'True':
    print('Enabling Swagger UI.')
    API_URL = '/static/swagger.json'
    SWAGGERUI_BLUEPRINT = get_swaggerui_blueprint(
        config['API']['swagger_url'].replace('\'',''),
        API_URL,
        config={
            'app_name': "Canaries Backend REST API"
        }
    )
    app.register_blueprint(SWAGGERUI_BLUEPRINT, url_prefix=config['API']['swagger_url'].replace('\'',''))

@app.route('/', methods=['GET'])
def main():
    return jsonify({})

# Auth module
API_Auth(app)

# Workers module
API_Workers(app)

# Domains module
API_Domains(app)

# Monitored sites module
API_Sites(app)

# Canaries module
API_Canaries(app)

if __name__ == '__main__':
    app.run(debug=True if config['API']['debug'] == 'True' else False, host=config['API']['bind_ip'], port=config['API']['port'])

