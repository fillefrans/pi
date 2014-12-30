0 0 * * * php /home/pi/src/srv/php/pi.service.time.php >> /var/log/pi/pi.service.time.log
0 0 * * * php /home/pi/src/srv/php/cron/pi.varnish.aggregator.php >> /var/log/pi/pi.varnish.aggregator.log

* * * * * php /home/pi/src/srv/php/cron/pi.cron.every.minute.php >> /var/log/pi/pi.cron.log
0 * * * * php /home/pi/src/srv/php/cron/pi.cron.every.hour.php >> /var/log/pi/pi.cron.log
0 0 * * * php /home/pi/src/srv/php/cron/pi.cron.every.midnight.php >> /var/log/pi/pi.cron.log
