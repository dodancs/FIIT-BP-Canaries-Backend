import sys
import mysql.connector
from mysql.connector import Error

class CanaryDBHandler:
    def test(self, host, port, user, password, database):
        print('Testing connection...')
        connection = None
        try:
            connection = mysql.connector.connect(host=host, port=port, database=database, user=user, password=password)

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