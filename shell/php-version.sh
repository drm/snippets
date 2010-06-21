#!/bin/bash

# @author Gerard van Helden <drm@melp.nl>

# Utility script to switch php versions installed in
# /usr/local/php/$versionNumber/

# Available binaries are symlinked in /usr/local/bin/

# This script assumes the debian layout for apache configuration using
# the a2enmod and a2dismod scripts

# The ./configure is supplied next to this file in the repository


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
