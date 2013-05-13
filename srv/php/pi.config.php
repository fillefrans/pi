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

    define('APP_ROOT', "/home/kroma/dev/www/pi/srv/php//php/server/");
    define('UTILITIES_DIR', APP_ROOT.'utility/');

    define('UPLOAD_ROOT',"/var/www/upload/Pi/batch/");
    define('WORKER_DIR', APP_ROOT."workers/");
    define('WORKER_SPAWNER', WORKER_DIR.'Pi.spawn.worker.script.php');
    define('SESSION_SCRIPT', WORKER_DIR.'Pi.sessionhandler.script.php');
    

    // Redis settings

    define('DB_APP',   0);
    define('DB_CACHE', 7);
    define('DB_JOBS',  4);
    define('DB_CACHE_SIZE', 20000000-1); // 20 million


    define('UPDATE_FREQUENCY', 10);

?>