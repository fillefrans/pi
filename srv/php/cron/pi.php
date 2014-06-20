<?php

  /**
   *  Pi command line utility script
   *
   *  provides basic admin functions
   *  
   *  
   *
   * @author 2011-2013 Johan Telstad <jt@enfield.no>
   * 
   */




  // load pi config
  require_once( __DIR__ . "/../pi.config.php");

  // include utility classes and libraries
  require_once(PHP_ROOT."pi.exception.php");
  require_once(PHP_ROOT."pi.util.php");

  // this is NOT the same file as this one, even if the names are the same
  require_once(PHP_ROOT."pi.php");




  class PiAdmin extends Pi {



    public function __construct() {

    }


    protected function __init() {

      $this->namespace = "pi.admin.scripts.util";
      // open a data connection for redis 
      if( false === ($this->redis = $this->connectToRedis())){
        throw new PiException("Unable to connect data client to redis on " . REDIS_SOCK, 1);
        return false;
      }
    }

  }


?>