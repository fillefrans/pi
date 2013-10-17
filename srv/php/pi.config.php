<?php

    /**
     *  @author Johan Telstad <jt@enfield.no>
     *  
     *  Config file for Pi Server
     * 
     */


    if (!defined('DEBUG')) {
      define('DEBUG', false);
    }


    if(DEBUG) {
      error_reporting(-1);
    }
    else {
      error_reporting(0);
    }


    define('APP_NAME',      'Pi Server');
    define('APP_PLATFORM',  'Pi Server/WebSocket');
    define('APP_VERSION',   'v0.2@');


    // define directories
    define('PI_ROOT',           __DIR__ . "/../../");
    define('SRV_ROOT',          __DIR__ . "/../");
    define('PHP_ROOT',          dirname(__FILE__) . "/");
    define('UTILITIES_DIR',     PHP_ROOT . 'utility/');

    define('DATA_ROOT',         SRV_ROOT . "data/");
    define('FILE_ROOT',         SRV_ROOT . "data/files/");
    define('UPLOAD_ROOT',       SRV_ROOT . "data/upload/");
    define('TMP_ROOT',          SRV_ROOT . "data/tmp/");
    define('LOG_DIR',           SRV_ROOT . "data/logs/");
    define('WORKER_DIR',        PHP_ROOT . "workers/");
    define('WORKER_SPAWNER',    WORKER_DIR . 'pi.util.spawn.php');
    define('SESSION_SCRIPT',    PHP_ROOT . 'pi.session.php');




    define('TICKS_PER_SECOND',  10);
    define('UPDATE_FREQUENCY',  1);
    define('SECONDS_IN_A_DAY',  24*60*60);

    define('A_COOL_MILLION',    1000000);


    // Redis settings
    define('REDIS_SOCK', '/var/data/redis/redis.sock');


    // Redis database names and numbers
    define('PI_APP',      0);
    define('PI_CORE',     1);

    // PHP is using one db for session storage
    define('PHP_SESSION', 2);

    define('PI_SESSION',  3);
    define('PI_DATA',     4);
    define('PI_CALLBACK', 5);
    define('PI_FILES',    6);
    define('PI_SVC',      7);
    define('PI_DB',       8);
    define('PI_USERS',    9);
    define('PI_TASKS',   10);

    define('PI_PCL',     11);
    define('PI_MVRCK',   12);

    define('PI_TMP',     14);
    define('PI_DBG',     15);


?>