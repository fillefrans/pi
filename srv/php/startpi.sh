#!/bin/sh

# nohup php ~/dev/pi/srv/php/pi.srv.v2.php &
nohup php ~/dev/pi/srv/php/pi.service.time.php &
nohup php ~/dev/pi/srv/php/pi.service.numberstation.php &

jobs
ps aux | grep php
php ~/dev/pi/srv/php/pi.srv.v4.php &

