#!/bin/bash
MYSQL_DATA=/home/runner/mysql-data
MYSQL_RUN=/home/runner/mysql-run
MYSQL_SOCK=$MYSQL_RUN/mysql.sock
mkdir -p $MYSQL_RUN
if [ ! -f "$MYSQL_RUN/mysql.pid" ] || ! kill -0 $(cat "$MYSQL_RUN/mysql.pid" 2>/dev/null) 2>/dev/null; then
    mysqld --datadir=$MYSQL_DATA \
      --socket=$MYSQL_SOCK \
      --pid-file=$MYSQL_RUN/mysql.pid \
      --port=3306 \
      --user=runner \
      --daemonize
    sleep 3
fi
echo "MySQL ready"
