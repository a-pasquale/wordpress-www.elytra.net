
sudo /usr/local/Cellar/php/5.3.6/bin/php-cgi -b 9000 &
/usr/local/sbin/nginx -c /usr/local/etc/nginx/nginx.conf &
/usr/local/bin/mysqld_safe --datadir=/usr/local/Cellar/mysql/5.5.10/data &
