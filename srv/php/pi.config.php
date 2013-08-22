<?php

    /**
     *  @author Johan Telstad <jt@enfield.no>
     *  
     *  Config file for Pi
     * 
     */

    if(!defined('DEBUG')){
      define('DEBUG', true);
    }


    error_reporting(0);

    define('APP_NAME', 'Pi AppServer');

    define('APP_PLATFORM', 'Pi Client-Server/WebSocket');

    define('APP_VERSION', 'v0.3');

    // define directories

    define('PI_ROOT', dirname(__FILE__) . "/");
    define('UTILITIES_DIR', PI_ROOT.'utility/');

    define('UPLOAD_ROOT',"/var/www/upload/pi/batch/");
    define('WORKER_DIR', PI_ROOT."workers/");
    define('WORKER_SPAWNER', WORKER_DIR.'pi.util.spawn.php');
    define('SESSION_SCRIPT', PI_ROOT.'pi.session.php');
    

    // Redis settings

    define('REDIS_SOCK', '/var/run/redis/redis.sock');

    define('PI_APP',    0);
    define('PI_PHP',    2);
    define('PI_JOB',    4);
    define('PI_DBG',   15);
    define('PI_CACHE',  7);

    define('PI_CACHE_SIZE', 20000000-1); // 20 million

    define('UPDATE_FREQUENCY', 10);


?>