<?php

    /**
     *  @author Johan Telstad <jt@viewshq.no>
     *  
     * @category pi
     * @package config
     *  Config file for Pi Server
     * 
     */

    // make sure DEBUG is always defined
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
    define('APP_VERSION',   'v0.5.2.1 Beta');

    // mysql settings

    $PI_DB  = array('db' => 'pi', 'user' => 'pi', 'password' => '3.141592', 'host' => 'localhost', 'port' => 3306);



    // define directories
    define('PI_ROOT',           __DIR__ . "/../../");
    define('SRV_ROOT',          __DIR__ . "/../");
    define('PHP_ROOT',          dirname(__FILE__) . "/");
    define('UTILITIES_DIR',     PHP_ROOT . 'util/');

    define('DATA_ROOT',         SRV_ROOT . "data/");
    define('FILE_ROOT',         SRV_ROOT . "data/files/");
    define('UPLOAD_ROOT',       SRV_ROOT . "data/upload/");
    define('GZ_ROOT',           UPLOAD_ROOT . "gz/");
    define('TMP_ROOT',          SRV_ROOT . "data/tmp/");
    define('LOG_DIR',           SRV_ROOT . "data/logs/");
    define('WORKER_DIR',        PHP_ROOT . "workers/");
    define('WORKER_SPAWNER',    WORKER_DIR . 'pi.util.spawn.php');
    define('SESSION_SCRIPT',    PHP_ROOT . 'pi.session.php');


    define('A_COOL_MILLION',    1000000);

    define('TICKS_PER_SECOND',  10);
    define('UPDATE_FREQUENCY',  1);
    define('SECONDS_IN_A_DAY',  24*60*60);

    define('TICK_LENGTH',  1/TICKS_PER_SECOND);


    // Redis settings $redis->connect('127.0.0.1');
    define('REDIS_SOCK', '127.0.0.1');
    //define('REDIS_SOCK', '/tmp/redis.sock');

    // the redis db to use
    define('PI_APP', 0);





?>