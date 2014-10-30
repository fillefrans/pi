#!/bin/sh

#Service installer shell script for Pi


USERNAME="pi"

SERVICE="redis"

URL="http://download.redis.io/"

VERSION="redis-stable"
GZFILE="$VERSION.tar.gz"

INSTALL="/home/$USERNAME/install"
DIRECTORY="$INSTALL/$SERVICE"



if ! [ -d "$INSTALL" ]; then
  # Control will enter here if $DIRECTORY exists.
  echo "[ERROR] pi is not installed. \n - Use \"pi install\" to retrieve latest version from GitHub." 1>&2
  exit 1
fi


if [ -f "$INSTALL/$GZFILE" ]; then
  # Control will enter here if $GZFILE already exists.
  echo "$SERVICE is already installed. \n - removing tarball and retrying install..." 1>&2
  rm -rf "$INSTALL/$GZFILE"
fi


# echo "changing to $INSTALL"
cd "$INSTALL"

echo "downloading : $URL$GZFILE"
# printf "downloading $URL$GZFILE..."
# run as pi system user
# sudo -u "$USERNAME" wget "$URL$GZFILE" --progress=bar:force 2>&1 | tail -f -n +7
wget "$URL$GZFILE" --progress=bar:force 2>&1 | tail -f -n +7
# echo "done!"

printf "decompressing $GZFILE..."
# run as pi system user
tar xvfz "$GZFILE" > /dev/null
echo "done!"


# echo "changing to $VERSION"
cd "$VERSION"


# echo "running ./configure :"
# sudo -u "$USERNAME" ./configure > /dev/null

apt-get install build-essential libc6-dev-i386

printf "running make..."
make 32bit

printf "make install..."
make install

echo "done!"
