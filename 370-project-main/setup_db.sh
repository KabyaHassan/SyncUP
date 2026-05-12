#!/bin/bash

if [ "$EUID" -ne 0 ]
  then echo "Please run this script using sudo!"
  exit
fi

echo "Extracting admin password..."
DEBIAN_PASS=$(awk -F'=' '/password/ {print $2; exit}' /etc/mysql/debian.cnf | tr -d ' ' | tr -d '\n' | tr -d '\r')

echo "Configuring MySQL and loading the database..."
mysql -u debian-sys-maint -p"$DEBIAN_PASS" <<EOF
source /home/mahir/Downloads/370-project-main/db.sql;
CREATE USER IF NOT EXISTS 'php_user'@'localhost' IDENTIFIED BY 'password123';
ALTER USER 'php_user'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON *.* TO 'php_user'@'localhost';
FLUSH PRIVILEGES;
EOF

echo "-"
echo "Success! The database is loaded and ready to go!"
echo "-"