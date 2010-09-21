#!/bin/bash

# @author Gerard van Helden <drm@melp.nl>

# Utility script to switch php versions installed in
# /usr/local/php/$versionNumber/

# Available binaries are symlinked in /usr/local/bin/
# Current (running) version is written to /var/run/php-version.current



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
}

if [ "" == "$1" ]; then
-	echo "No version specified" >&2
	exit -1
fi;

if [ -x php ]; then
	current=`ls -l $(which php) | egrep -o '/[0-9.]+/' | tr -d /`
elif [ "$FORCE" == "" ]; then
	echo "php is not available, can not check current version"
	echo "Set FORCE env to force switch"
	exit -2;
fi

if [ "$current" == "$1" ]; then
	echo "$current already available"
	exit 0;
elif [ "" != "$current" ]; then
	disable $current;
fi

enable $1
