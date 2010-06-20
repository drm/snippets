#!/bin/bash

if [ "" == "$1" ]; then
	echo "No version specified" >2
	exit -1
fi;

PHP_VERSION_DIR="/usr/local/php/$1"
if [ ! -d $PHP_VERSION_DIR ]; then
	echo "$PHP_VERSION_DIR does not exist"
	exit -2
fi

for i in ls $PHP_VERSION_DIR/bin/*; do
	( cd /usr/local/bin && rm -f `basename $i`; ln -s $i )
done
rm -f /usr/local/apache2/modules/libphp5.so
ln -s $PHP_VERSION_DIR/lib/apache/libphp5.so \
	/usr/local/apache2/modules/libphp5.so
/usr/local/apache2/bin/apachectl restart

