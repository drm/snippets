#!/bin/bash

function check_prefix {
	prefix=$1
	if [ ! -d $prefix ]; then
		echo $prefix does not exist >&2
		exit -1
	fi
}


function disable {
	prefix="/usr/local/php/$1"
	check_prefix $prefix
	for i in $prefix/bin/*; do
		if [ -h "/usr/local/bin/`basename $i`" ]; then
			( cd /usr/local/bin && rm -f `basename $i` )
		fi
	done
	if [ -e /etc/apache2/mods-available/php-$1.load ]; then
		a2dismod php-$1
	fi
}


function enable {
	prefix="/usr/local/php/$1"
	check_prefix $prefix
	for i in $prefix/bin/*; do
		if [ ! -h $i ]; then
			( cd /usr/local/bin/ \
				&& ln -s $i );
		fi
	done
	if [ -e /etc/apache2/mods-available/php-$1.load ]; then
		a2enmod php-$1
	fi
}

if [ "" == "$1" ]; then
	echo "No version specified" >&2
	exit -1
fi;

if [ -f /var/run/php-version.current ]; then
	current=`cat /var/run/php-version.current`
	disable $current
fi
enable $1
echo $1 > /var/run/php-version.current

/etc/init.d/apache2 restart
