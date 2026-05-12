#!/bin/bash

if [ "$EUID" -ne 0 ]
  then echo "Please run this script using sudo!"
  exit
fi
CONFIG_FILE="/opt/lampp/phpmyadmin/config.inc.php"
sed -i "s/\$cfg\['Servers'\]\[\$i\]\['auth_type'\] = 'config';/\$cfg\['Servers'\]\[\$i\]\['auth_type'\] = 'cookie';/g" $CONFIG_FILE
sed -i "s/\/\/\$cfg\['Servers'\]\[\$i\]\['host'\] = 'localhost';/\$cfg\['Servers'\]\[\$i\]\['host'\] = '127.0.0.1';/g" $CONFIG_FILE
echo "phpMyAdmin configured!"
/opt/lampp/lampp startapache
echo "-"
echo "XAMPP Apache is now running!"
echo "-"