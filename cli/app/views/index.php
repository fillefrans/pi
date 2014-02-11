<?php

  $session = session_start();


  /**
   *  Quick and dirty login
   *
   * 
   */

  function connectToRedis($timeout=5){
    if(!defined('REDIS_SOCK')) {
      define('REDIS_SOCK', '/var/data/redis/redis.sock');
    }
    $redis = new Redis();
    try{ 
//      if(false===($redis->connect('127.0.0.1', 6379, $timeout))){
      if(false===($redis->connect(REDIS_SOCK))){
        return false;
      }
      return $redis;
    }
    catch(RedisException $e){
      print "Redis exception: ". $e->getMessage();
      return false;
    }
  }



  function login($username, $password) {
    $db = array('host' => 'localhost', 'user' => 'pi_readonly', 'password' => '3.141592', 'db' => 'pi');
    $redis = connectToRedis();

    if(!($username && $password)) {
      if($redis) {
        $redis->publish("pi.user.login", "invalid param(s): $username, $password");
      }
      return false;
    }


    $mysqli = new mysqli( $db['host'], $db['user'], $db['password'], $db['db'] );

    if(mysqli_connect_errno()){
      if($redis) {
        $redis->publish("pi.user.login", "mysql error: " . $mysqli->error);
      }
      return false;
    }

    // $query = "SELECT users.*, usergroups.name as usergroup, clients.*,roles.name as role 
    // FROM users
    // INNER JOIN usergroups ON users.usergroup = usergroups.id
    // INNER JOIN clients ON users.client_id = clients.id
    // INNER JOIN roles ON users.role = roles.id
    // WHERE username='{$_POST['username']}';";
    $query    = "SELECT * from user  
                 WHERE username='$username';";//" AND password=SHA1('$password');";

    if($redis) {
      $redis->publish("pi.user.login", "query: $query");
    }


    if( FALSE=== ($sqlresult = $mysqli->query($query)) ){
      if($redis) {
        $redis->publish("pi.user.login", "mysql returned FALSE result");
      }
      return false;
    }
    else{
      if($redis) {
        $redis->publish("pi.user.login", "mysql returned {$sqlresult->num_rows} result");
      }
      // $result is the value we will return at the end of the function
      $result = $sqlresult->fetch_assoc();

      $sqlresult->close();
    }
    $mysqli->close();
 
    if($redis) {
      $redis->publish("pi.user.login", "login result: " . json_encode($result));
    }

    if($result) {
      // let's DON'T send the password hash
      unset($result['password']);

      foreach ($result as $key => $value) {
        if(!isset($_SESSION[$key])) {
          $_SESSION[$key] = $value;
        }
      }
    }

    return $result;

  }

?>