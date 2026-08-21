#!/bin/bash
set -e
cd /var/www/html

HOST="${DB_HOST:-127.0.0.1}"
PORT="${DB_PORT:-4000}"
DBUSER="${DB_USER}"
DBPASS="${DB_PASS}"
NAME="${DB_NAME:-mi_findra}"

echo "--- Attente de la base ---"
for i in $(seq 1 30); do
  if mysqladmin --ssl-ca=/etc/ssl/certs/ca-certificates.crt \
      --host="$HOST" --port="$PORT" --user="$DBUSER" --password="$DBPASS" ping >/dev/null 2>&1; then
    echo "Base joignable."
    break
  fi
  echo "Tentative $i/30..."
  sleep 2
done

MYSQL="mysql --ssl-ca=/etc/ssl/certs/ca-certificates.crt --host=$HOST --port=$PORT --user=$DBUSER --password=$DBPASS"

echo "--- Creation de la base si absente ---"
$MYSQL -e "CREATE DATABASE IF NOT EXISTS \`$NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

TABLES=$($MYSQL "$NAME" -N -e "SHOW TABLES;" | wc -l)

if [ "$TABLES" -eq 0 ]; then
  echo "--- Import du schema ---"
  $MYSQL "$NAME" < database/schema.sql
  echo "Schema importe."
else
  echo "Tables deja presentes ($TABLES), import ignore."
fi

exec "$@"
