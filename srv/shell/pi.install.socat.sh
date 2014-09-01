#!/bin/sh

#Service installer shell script for Pi


USERNAME="pi"

SERVICE="socat"

URL="http://www.dest-unreach.org/socat/download/"

VERSION="socat-1.7.2.4"
GZFILE="$VERSION.tar.gz"

INSTALL="/home/$USERNAME/install"
DIRECTORY="$INSTALL/$SERVICE"



if ! [ -d "$INSTALL" ]; then
  # Control will enter here if $DIRECTORY exists.
  echo "[ERROR] pi is not installed. \n - Use \"pi install\" to retrieve latest version from GitHub." 1>&2
  exit 1
fi


if [ -f "$INSTALL/$GZFILE" ]; then
  # Control will enter here if $DIRECTORY exists.
  echo "[WARNING] $SERVICE is already installed. \n - removing tarball and retrying install..." 1>&2

  sudo rm -rf "$INSTALL/$GZFILE"
fi


# echo "changing to $INSTALL"
cd "$INSTALL"

# printf "downloading $URL$GZFILE..."
# run as pi system user
sudo -u "$USERNAME" wget "$URL$GZFILE" --progress=bar:force 2>&1 | tail -f -n +7
# echo "done!"

printf "decompressing $GZFILE..."
# run as pi system user
sudo -u "$USERNAME" tar xvfz "$GZFILE" > /dev/null
echo "done!"


# echo "changing to $VERSION"
cd "$VERSION"


echo "running ./configure :"
sudo -u "$USERNAME" ./configure > /dev/null


printf "running make..."
sudo -u "$USERNAME" make > /dev/null

printf "make install..."
sudo make install > /dev/null

echo "done!"
