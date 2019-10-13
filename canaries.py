from flask import Flask
from flask_sqlalchemy import SQLAlchemy
import os.path
import argparse
import sys
from CanarySetup import CanarySetup
from CanaryHelpers import CanaryHelpers

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

if __name__ == '__main__':
    app.run(debug=True)
