#!/bin/sh

#Shell script installer for Pi



USERNAME="pi"

DIRECTORY="/home/$USERNAME"

PIDIRECTORY="$DIRECTORY/src"

GITURL="https://github.com/fillefrans/pi.git"


if [ -d "$DIRECTORY/" ]; then
  echo "user pi already exists"
else
  echo "creating user for pi"
  sudo useradd "$USERNAME"
  echo "  - creating directories"
  sudo mkdir "$DIRECTORY/install"
  echo "  - setting directory group"
  sudo chgrp -R "$USERNAME" "$DIRECTORY"
  echo "  - setting permissions"
  sudo chmod -R g+rwx "$DIRECTORY"
  echo "done!"
fi



if [ -d "$PIDIRECTORY" ]; then
  # Control will enter here if $PIDIRECTORY exists.
  tput setaf 1
  printf "pi is already installed. "
  tput sgr 0
  tput setaf 6
  echo " - use \"pi update\" to retrieve latest version from GitHub."
  tput sgr 0
  exit 1
fi

sudo -u pi git clone --single-branch --depth=1 -b develop "$GITURL" "$PIDIRECTORY"

if [ "$?" > "0" ]; then
  tput setaf 1
  echo "git clone exited with error code : $?" 1>&2
  tput sgr 0
  exit 1
fi



tput setaf 6
echo "done! You should make sure you have php5-dev installed."
tput sgr 0
exit 1






# Colour commands
# tput setab [1-7] # Set the background colour using ANSI escape
# tput setaf [1-7] # Set the foreground colour using ANSI escape
# Colours are as follows:

# Num  Colour    #define         RGB

# 0    black     COLOR_BLACK     0,0,0
# 1    red       COLOR_RED       max,0,0
# 2    green     COLOR_GREEN     0,max,0
# 3    yellow    COLOR_YELLOW    max,max,0
# 4    blue      COLOR_BLUE      0,0,max
# 5    magenta   COLOR_MAGENTA   max,0,max
# 6    cyan      COLOR_CYAN      0,max,max
# 7    white     COLOR_WHITE     max,max,max
# 
# There are also non-ANSI versions of the colour setting functions (setb instead of setab, and setf instead of setaf) which use different numbers, not given here.

# Text mode commands
# tput bold    # Select bold mode
# tput dim     # Select dim (half-bright) mode
# tput smul    # Enable underline mode
# tput rmul    # Disable underline mode
# tput rev     # Turn on reverse video mode
# tput smso    # Enter standout (bold) mode
# tput rmso    # Exit standout mode
# tput sgr 0   # Reset all attributes

# Cursor movement commands
# tput cup Y X # Move cursor to screen postion X,Y (top left is 0,0)
# tput cuf N   # Move N characters forward (right)
# tput cub N   # Move N characters back (left)
# tput cuu N   # Move N lines up
# tput ll      # Move to last line, first column (if no cup)
# tput sc      # Save the cursor position
# tput rc      # Restore the cursor position
# tput lines   # Output the number of lines of the terminal
# tput cols    # Output the number of columns of the terminal

# Clear and insert commands
# tput ech N   # Erase N characters
# tput clear   # Clear screen and move the cursor to 0,0
# tput el 1    # Clear to beginning of line
# tput el      # Clear to end of line
# tput ed      # Clear to end of screen
# tput ich N   # Insert N characters (moves rest of line forward!)
# tput il N    # Insert N lines
 
# Other commands
# tput bel     # play a bell
# With compiz wobbly windows, the bel command makes the terminal wobble for a second to draw the user's attention.

# Example usage
# echo "$(tput setaf 1)Red text $(tput setab 7)and white background$(tput sgr 0)"
# Use command sgr 0 to reset the colour at the end.
 
# Performing multiple operations at once
#   tput accepts scripts containing one command per line, which are executed in order before tput exits.

# Avoid temporary files by echoing a multiline string and piping it:
#   echo -e "setf 7\nsetb 1" | tput -S  # set fg white and bg red

