#!/bin/sh

#Shell script installer for phpMyAdmin



DIRECTORY="/var/www/phpMyAdmin"

if [ -d "$DIRECTORY" ]; then
  # Control will enter here if $DIRECTORY exists.
  echo "[ERROR] phpMyAdmin is already installed. \n - Use \"pi update phpmyadmin\" to retrieve latest version from GitHub."
  exit 1
fi

sudo -H git clone --single-branch --depth=1 -b STABLE https://github.com/phpmyadmin/phpmyadmin.git "$DIRECTORY"

if [ "$?" > "0" ]; then
  echo "[ERROR] git clone exited with error code : $?" 1>&2
  exit 1
fi

sudo useradd phpmyadmin

if [ "$?" > "0" ]; then
  echo "[ERROR] Unable to create user phpmyadmin" 1>&2
  exit 1
fi

sudo chgrp -R phpmyadmin "$DIRECTORY"

if [ "$?" > "0" ]; then
  echo "[ERROR] unable to change group for $DIRECTORY to phpmyadmin" 1>&2
  exit 1
fi

sudo chmod -R g+rwx "$DIRECTORY"

if [ "$?" > "0" ]; then
  echo "[ERROR] Unable to set rwx access for group phpmyadmin on $DIRECTORY error code : $?" 1>&2
  exit 1
fi
