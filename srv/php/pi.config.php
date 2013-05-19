<?php

    /**
     *  @author Johan Telstad <jt@kroma.no>
     *  
     *  Config file for Pi
     * 
     */

    define('APP_NAME', 'Pi WebSocket Application Server');

    define('APP_PLATFORM', 'Pi Server/WebSocket');

    define('APP_VERSION', 'v0.1');

    // define directories

    define('APP_ROOT', "/home/kroma/dev/www/pi/srv/php/");
    define('UTILITIES_DIR', APP_ROOT.'utility/');

    define('UPLOAD_ROOT',"/var/www/upload/pi/batch/");
    define('WORKER_DIR', APP_ROOT."workers/");
    define('WORKER_SPAWNER', WORKER_DIR.'pi.util.spawn.php');
    define('SESSION_SCRIPT', WORKER_DIR.'pi.session.php');
    

    // Redis settings

    define('PI_APP',   0);
    define('PI_CACHE', 7);
    define('PI_JOBS',  4);
    define('PI_DEBUG', 15);
    define('PI_CACHE_SIZE', 20000000-1); // 20 million

    define('UPDATE_FREQUENCY', 10);


?>