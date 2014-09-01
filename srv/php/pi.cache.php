<?

  // activate debugging
  if (!defined('DEBUG')) {
    define('DEBUG', true);
  }


  /**
   *  Pi Cache class
   *
   *  provides redis cache with 
   *
   * @category pi
   * @package cache
   *
   * @copyright 2011-2014 Views AS
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   * 
   */


  require_once("pi.exception.php");
  require_once("pi.config.php");

  require_once("pi.util.functions.php");




          // "pi.user.(name~='jonna').groups.[*]";
          // "pi.user.[name~='john jr'].groups.[*]";

          // pi.user.group.name
          // 
          // select *, group.name from user
          //    LEFT JOIN 

          // "pi.user.age<67";
          // "pi.user.(name=dajkjh asd).groups.*";
          // "pi.user.{name=dajkjh asd}.groups.*";
          // "pi.user.[id=876876234].groups.[*]";

          // "pi.user.id=876876234.groups.[*]";

          // "pi.user.id:876876234.groups.[*]";

          // "pi.user.id|876876234.groups.[*]";

          // "pi.user.876876234.groups.[*]";

          // "pi.user.876876234.group.id";

          // "pi.user.876876234.group.*";
          // "pi.user.876876234.client";

          // "pi.user.876876234.group.name := ('kjkajsd')";

          // "pi.user.87687623 := (name='kjkajsd';id=23123;)";

          // "pi.user.876876234";
          // "pi.user.876876234.*";

          // "pi.user.*";

          // "pi.user.id:null";

          // "pi.user.[987234,234234,234].group";

          // "file:kjhskdfjhsf | pi.user.876876234.client";

          // "file:kjhskdfjhsf | pi.client.3";



  class PiCache {

    protected   $starttime  = null;
    protected   $redis      = null;


    public function __construct() {
      $this->starttime = microtime(true);

      $this->__init();
    }


    protected function __init() {

      // open a data connection for redis 
      if (false === ($this->redis = $this->connectToRedis())) {
        throw new PiException("Unable to connect data client to redis on " . REDIS_SOCK, 1);
        print("Connection to redis failed!\n");
        return false;
      }
  
      return true;
    }


    public static function exceptionToArray(&$e) {
      return array(
                   'class'    => get_class($e),
                   'message'  => $e->getMessage(),
                   'file'     => $e->getFile(),
                   'code'     => $e->getCode(),
                   'file'     => $e->getFile(),
                   'line'     => $e->getLine(),
                   'trace'    => $e->getTrace()
                   );
    }


    private function handleException(&$e) {
      if (DEBUG) {
        $json = json_encode($e, JSON_PRETTY_PRINT) . "\n";
        file_put_contents(basename(__FILE__, '.php') . '.errorlog', $json, FILE_APPEND);
      }
      $this->say("UNHANDLED " . get_class($e) . ": " . json_encode(self::exceptionToArray($e), JSON_PRETTY_PRINT));
    }


    public function connectToRedis($db = 0, $timeout = 5) {
      $redis = new Redis();
      try {
        if (false === ($redis->connect(REDIS_SOCK))) {
          $debug[] = 'Unable to connect to Redis';
          return false;
        }
        $redis->select($db);
        return $redis;
      }
      catch (RedisException $e) {
        $this->handleException($e);
        return false;
      }
    }


    // "  SELECT a.*, p.height, p.width, p.selfadheight, c.cdndomain
    //       FROM ads a
    //         INNER JOIN (SELECT MAX(adid) adid FROM ads WHERE approved=1 AND subscriberid>0 AND widgetid=$widgetid GROUP BY subscriberid) adid2 ON a.adid=adid2.adid
    //         LEFT JOIN product p ON p.productid=a.productid
    //         LEFT JOIN widgets w ON w.widgetid=a.widgetid
    //         LEFT JOIN countries c ON c.countryid=w.countryid
    //       ORDER BY adid2.adid DESC LIMIT $limit";


    // pi.user.group.name
    // 
    // select u.*, g.name 
    //    FROM user u
    //      LEFT JOIN group g ON g.id = u.pi_group
    //      WHERE id = 2;


// select user.id, user.name, email.value 
// FROM user
// INNER JOIN email ON email.id = user.pi_email 
// WHERE user.id = 5;


    private function hasQuery ($str) {
      if(is_numeric($str)) {
        return true;
      }
      elseif ($result = strpos($str, "=")) {
        return $result;
      }
      elseif ($result = strpos($str, "<") !== false) {
        return $result;
      }
      elseif ($result = strpos($str, ">") !== false) {
        return $result;
      }
      elseif ($result = strpos($str, "~") !== false) {
        return $result;
      }

      return false;
    }


    private function getQuery ($str) {
      if ($querypos = $this->hasQuery($str)) {

      }
      if(is_numeric($str)) {
        return "id = $str";
      }
      elseif (strpos($str, "=")) {
        
      }
      elseif (strpos($str, "<") !== false) {
        
      }
      elseif (strpos($str, ">") !== false) {
        
      }
      elseif (strpos($str, "~") !== false) {
        
      }
    }



    private function addressToReadQuery ($address = null) {
      // non-empty string
      if ($address && is_string($address)) {

        if (strpos($address, "*")) {
          // handle wildcards
        }
        $path = explode(".", $address);
        if (0 === ($items = count($address))) {
          return false;
        }
        elseif ($items === 1) {
          $this->db = $path[0];
        }
        elseif ($items > 1) {
          $this->db = $path[0];
          $localpath = $path[1];
          $querypart = "SELECT ";
          for ($i = 1; $i < $items; $i++) {
            if (is_numeric($path[$i])) {
              // it's an ID
              $querypart .= "* FROM " . $path[$i-1] . " WHERE id = " . $path[$i];
            }
            elseif ($this->hasQuery($path[$i])) {
              // decode query string
              if ($localpath == "pi") {
                // 
                // $localpath = "";
              }

// select user.id, user.name, email.value 
// FROM user
// INNER JOIN email ON email.id = user.pi_email 
// WHERE user.id = 5;

// pi.user(name~='j',verified=true,created).*

// pi.user(name,verified=true,created).*

// pi.user[name;verified;created].*

// pi.user[name,verified=true,created].*
// pi.user(name,verified,created).*
// pi.user[name,email].*
// pi.user[name].*
// pi.user.*.name
// pi.user.*.email
// pi.user.*.name
// pi.user.(*).name
// pi.user.[*].name

// pi.user.*.pi_email


              // $querypart .= "$localpath.id " . 

            }
            elseif (is_string($path[$i])) {
              // its a tree node
              $localpath .= ".".$path[$i];
            }
          }

        }
        else {
          return false;
        }
      }
      else { // not string, or empty
        throw new InvalidArgumentException("address should be non-empty string", 1);
        
      }
    }


    private function addressToTable ($address = null) {
      // non-empty string
      if ($address && is_string($address)) {
        $path = explode(".", $address);
        if (0 === ($items = count($address))) {
          return false;
        }
        elseif ($items === 1) {
          $this->db = $items[0];
        }
        else {
          // 
        }
      }
      else {
        throw new InvalidArgumentException("address should be non-empty string", 1);
        
      }
    }


    private function readFromDB ($address) {
      if (!$this->mysqli instanceof MySQLi) {
        $DB  = array('db' => 'pi', 'user' => 'pi', 'password' => '3.141592', 'host' => 'localhost', 'port' => 3306);
        $this->mysqli = new MySQLi($DB['host'], $DB['user'], $DB['password'], $DB['db'], $DB['port']);
        if ($this->mysqli->connect_errno) {
          print("Mysql connect error : " . $this->mysqli->connect_error ."\n");
        }
      }

      if (false === ($addr = new PiAddress($address))) {
        print("invalid address : '$address'\n");
      }

    }


    private function writeToDB ($address, PiType $value) {
      if (!$this->mysqli instanceof MySQLi) {
        $DB  = array('db' => 'pi', 'user' => 'pi', 'password' => '3.141592', 'host' => 'localhost', 'port' => 3306);
        $this->mysqli = new MySQLi($DB['host'], $DB['user'], $DB['password'], $DB['db'], $DB['port']);
      }
      else {
        if (!$this->mysqli instanceof MySQLi) {
          throw new PiException("Unable to create MySQLi instance", 1);
          return null;
        }
        if ($this->mysqli->connect_errno) {
          print("Mysql connect error : " . $this->mysqli->connect_error ."\n");
        }
      }
      
    }


    private function readFromCache($address = null) {

      if ($address === null) {
        throw new InvalidArgumentException("address is null", 1);
      }
      else {
        if (false !== ($result = $this->redis->get($address))) {
          return unserialize($result);
        }
        else {
          return $this->readFromDB($address);
        }
      }

    }


    private function writeToCache() {

      if ($address === null) {
        throw new InvalidArgumentException("address is null", 1);
      }
      elseif ($value === null) {
        throw new InvalidArgumentException("value is null", 1);
      }
      else {
        return $this->redis->set($address, serialize($value));
      }
      
    }



    public function write ($address = null, $value = null) {
      if (false !== ($result = $this->writeToCache($address, $value))) {
        return $result;
      }
      else {
        return $this->writeToDB($address, $value);
      }
    }


    public function read ($address = null) {

    }


  }


?>