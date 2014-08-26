#!/bin/sh

# Pi Shell Script Wrapper
# Wraps pi/srv/shell/pi.php as shell script
# 
# @requires php5
# @author Johan Telstad <jt@enfield.no>


# Absolute path to this script, e.g. {/home/user}/pi/srv/shell/pi.sh
SCRIPT=$(readlink -f "$0")

# Absolute path this script is in, e.g. {/home/user}/pi/srv/shell
SCRIPTPATH=$(dirname "$SCRIPT")

# invoke pi.php, passing along any params
php "$SCRIPTPATH/pi.php" "$@"
