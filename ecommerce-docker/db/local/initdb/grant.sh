#!/bin/bash
set -e

# Use environment variables from docker-compose
mysql -uroot -p"$MYSQL_ROOT_PASSWORD" <<-EOSQL
  -- Ensure the user exists with mysql_native_password
  CREATE USER IF NOT EXISTS '$MYSQL_USER'@'%' IDENTIFIED WITH mysql_native_password BY '$MYSQL_PASSWORD';

  -- Grant full privileges
  GRANT ALL PRIVILEGES ON *.* TO '$MYSQL_USER'@'%' WITH GRANT OPTION;

  -- Apply changes
  FLUSH PRIVILEGES;
EOSQL