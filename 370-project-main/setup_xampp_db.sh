#!/bin/bash

if [ "$EUID" -ne 0 ]
  then echo "Please run this script using sudo!"
  exit
fi

echo "Starting XAMPP MySQL..."
/opt/lampp/lampp startmysql

echo "Waiting for MySQL to start..."
sleep 3

# Check if XAMPP MySQL is running
if /opt/lampp/bin/mysqladmin ping -u root 2>/dev/null; then
    echo "XAMPP MySQL is running!"
else
    echo "XAMPP MySQL failed to start. Trying system MySQL..."
    systemctl start mysql 2>/dev/null || service mysql start 2>/dev/null
    sleep 2
fi

echo "Configuring MySQL and loading the database..."

# Try XAMPP MySQL first, then system MySQL
if [ -x "/opt/lampp/bin/mysql" ] && /opt/lampp/bin/mysqladmin ping -u root 2>/dev/null; then
    /opt/lampp/bin/mysql -u root <<EOF
source /home/mahir/Downloads/370-project-main/db.sql;
CREATE USER IF NOT EXISTS 'php_user'@'localhost' IDENTIFIED BY 'password123';
ALTER USER 'php_user'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON *.* TO 'php_user'@'localhost';
CREATE USER IF NOT EXISTS 'php_user'@'127.0.0.1' IDENTIFIED BY 'password123';
ALTER USER 'php_user'@'127.0.0.1' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON *.* TO 'php_user'@'127.0.0.1';
FLUSH PRIVILEGES;
EOF
else
    # Fall back to system MySQL using debian-sys-maint
    DEBIAN_PASS=$(awk -F'=' '/password/ {print $2; exit}' /etc/mysql/debian.cnf 2>/dev/null | tr -d ' ' | tr -d '\n' | tr -d '\r')
    mysql -u debian-sys-maint -p"$DEBIAN_PASS" <<EOF
source /home/mahir/Downloads/370-project-main/db.sql;
CREATE USER IF NOT EXISTS 'php_user'@'localhost' IDENTIFIED BY 'password123';
ALTER USER 'php_user'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON *.* TO 'php_user'@'localhost';
CREATE USER IF NOT EXISTS 'php_user'@'127.0.0.1' IDENTIFIED BY 'password123';
ALTER USER 'php_user'@'127.0.0.1' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON *.* TO 'php_user'@'127.0.0.1';
FLUSH PRIVILEGES;
EOF
fi

echo "-"
echo "Success! The database is loaded and ready to go!"
echo "-"
