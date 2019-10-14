import sys
import mysql.connector
from mysql.connector import Error
from flask_sqlalchemy import SQLAlchemy

class CanaryDBHandler:
    db = None

    def test(self, host, port, user, password, database):
        print('Testing connection...')
        test = None
        try:
            test = mysql.connector.connect(host=host, port=port, database=database, user=user, password=password)

            if test.is_connected():
                db_Info = test.get_server_info()
                print('Connected to MySQL Server version {}\n'.format(db_Info))
                testCursor = test.cursor()
                testCursor.execute("select database();")
                testCursor.fetchone()
                return 0

        except Error as e:
            print('Error while connecting to MySQL {}'.format(e), file=sys.stderr)
            return 1
        finally:
            if test and test.is_connected():
                testCursor.close()
                test.close()

    def connect(self, app, config):
        app.config['SECRET_KEY'] = config['SECURITY']['secret']
        app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://{}:{}@{}:{}/{}'.format(config['DATABASE']['user'],
                                                                                config['DATABASE']['password'],
                                                                                config['DATABASE']['host'],
                                                                                config['DATABASE']['port'],
                                                                                config['DATABASE']['database'])
        app.config['SQLALCHEMY_ECHO'] = True if config['API']['debug'] == 'True' else False
        self.db = SQLAlchemy(app)
        