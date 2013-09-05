<?php



    define('PI_APP',      0);
    define('PI_CORE',     1);

    define('PHP_SESSION', 2);
    define('PI_SESSION',  3);




$config = array(
  'servers' => array(

    /*array(
      'host' => 'localhost',
      'port' => 6380
    ),*/

    array(
      'name'    => 'PI_APP',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 0, 
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),

    array(
      'name'    => 'PI_CORE',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 1, 
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'PHP_SESSION', // Optional name.
      'host'    => '127.0.0.1',
      'port'    => 6379,
      'db'      => 2, 
      'filter'  => '*'

      // Optional Redis authentication.
      //'auth' => 'redispasswordhere' // Warning: The password is sent in plain-text to the Redis server.
    ),
    array(
      'name'    => 'PI_SESSION',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 3, 
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'PI_DATA',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 4, 
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'PI_CALLBACK',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 5, 
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'PI_FILES',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 6,
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'PI_SERVICES',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 7, 
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'PI_DB',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 8, 
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'PI_USER',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 9,
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'PI_TASKS',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 10, 
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'PI_PCL',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 11, 
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'PI_MAVERICK',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 12,
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'LUCKY_THIRTEEN',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 13,
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'PI_TEMP',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 14,
      'filter'  => '*' // Show only parts of database for speed or security reasons
    ),
    array(
      'name'    => 'PI_DEBUG',
      'host'    => 'localhost',
      'port'    => 6379,
      'db'      => 15,
      'filter'  => '*' // Show only parts of database for speed or security reasons
    )
  ),


  'seperator' => '.',


  // Uncomment to show less information and make phpRedisAdmin fire less commands to the Redis server. Recommended for a really busy Redis server.
  //'faster' => true,


  'login' => array(
    // Username => Password
    'xman' => array(
      'password' => 'tsxx',
    )
  ),


  // You can ignore settings below this point.

  'maxkeylen' => 100
);

?>
