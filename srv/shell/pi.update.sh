#!/bin/sh

#Shell script Pi updater


USERNAME="pi"

DIRECTORY="/home/$USERNAME"

PIDIRECTORY="$DIRECTORY/src"


if ! [ -d "$PIDIRECTORY" ]; then
  # Control will enter here if $DIRECTORY does not exist.
  tput setaf 1
  printf "pi is not installed. "
  tput sgr 0
  tput setaf 6
  echo "\n    Use \"pi install\" to retrieve latest version from GitHub."
  tput sgr 0
  exit 1
fi

cd "$PIDIRECTORY"
sudo -H git pull
