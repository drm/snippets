#!/bin/bash

# @author Gerard van Helden <drm@melp.nl>

# Utility script to switch php versions installed in
# /usr/local/php/$versionNumber/

[ "$PHP_PREFIX" == "" ] && PHP_PREFIX="/usr/local/php"
[ "$BIN_PREFIX" == "" ] && BIN_PREFIX="/usr/local/bin"
[ "$SBIN_PREFIX" == "" ] && SBIN_PREFIX="/usr/local/sbin"

function check_prefix {
	prefix=$1
	if [ ! -d $prefix ]; then
		echo $prefix does not exist >&2
		exit -1
	fi
}

function rm_links {
	src=$1
	target=$2
	for i in $src/*; do
		if [ -h "$target/`basename $i`" ]; then
			( cd $target && rm $VERBOSE -f `basename $i` )
		fi
	done
}


function mk_links {
	src=$1
	target=$2
	for i in $src/*; do
		if [ ! -h $i ]; then
			( cd $target && ln $VERBOSE -s $i );
		fi
	done
}



function disable {
	prefix="$PHP_PREFIX/$1"
	check_prefix $prefix
	rm_links $prefix/bin $BIN_PREFIX
	rm_links $prefix/sbin $SBIN_PREFIX
}


function enable {
	prefix="$PHP_PREFIX/$1"
	check_prefix $prefix
	mk_links $prefix/bin $BIN_PREFIX
	mk_links $prefix/sbin $SBIN_PREFIX
}

if [ "`which php`" != "" -a -x "`which php`" ]; then
	current=`ls -l $(which php) | sed s~.*$PHP_PREFIX/~~ | egrep -o '[^/]+' | head -1`
fi


if [ "" == "$1" ]; then
	echo "Usage: $0 {version [--force]|--off}" >&2
    if [ "$current" != "" ]; then
        echo "Current version: $current";
    fi;
	exit -1
fi;

if [ "$current" == "" ] &&  [ "$2" != "--force" ]; then
	echo "php is not available, can not check current version"
	echo "Use --force option to force switch"
	exit -2;
fi

if [ "$current" == "$1" ] && [ "$2" != "--force" ]; then
	echo "$current already available"
	exit 0;
elif [ "" != "$current" ]; then
	disable $current;
fi

if [ "$1" != "--off" ]; then
	enable $1
fi;