host = 'localhost'
port = 3306
user = 'user'
password = 'password'
database = 'database'
db_type = 'mysql'

if db_type == 'mysql':
    connect_info = 'mysql://%s:%s@%s:%s/%s' % (user, password, host, port, database)
