#!/bin/bash
set -e

mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    GRANT ALL ON *.* TO '${MYSQL_USER}'@'%' WITH GRANT OPTION;
    FLUSH PRIVILEGES;
EOSQL