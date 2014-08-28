<?

  /**
   *  Pi DB class
   *
   *  Implements rudimentary db access for the Pi namespace.
   *  This version uses MySQL with the MySQLi driver
   *
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   * 
   */

  require_once('pi.php');



  class PiDB {

    private     $mysqli = null;
    private     $res    = null;



    /**
     * Constructor
     * @param   MySQLi  $mysqli   Optional existing MySQLi instance
     */
    
    public function __construct(MySQLi $mysqli = null) {
      if ($mysqli instanceof MySQLi) {
        $this->mysqli = $mysqli;
      }
      else {
        $DB  = array('db' => 'pi', 'user' => 'pi', 'password' => '3.141592', 'host' => 'localhost', 'port' => 3306);

        $this->mysqli = new MySQLi($DB['host'], $DB['user'], $DB['password'], $DB['db'], $DB['port']);
        if (!$this->mysqli instanceof MySQLi) {
          return null;
        }
        if ($this->mysqli->connect_errno) {
          print("Mysql connect error : " . $this->mysqli->connect_error ."\n");
        }
      }
    }





    public function info ($what = null, $table = "user") {
      $query = "SHOW TABLES;";
      $getter = 'array';
      switch ($what) {
        case null : 
          break;
        case 'tables':
          $query = "SHOW TABLES;";
          $getter = 'fetch';
          break;
        case 'table':
          $query = "DESC $table;";
          $getter = 'fetch_assoc';
          break;
        case 'create':
          $query = "SHOW CREATE TABLE $table;";
          $getter = 'fetch_array';
          break;
        case 'all':
          $query = "SHOW FULL COLUMNS FROM $table;";
          $getter = 'fetch_assoc';
          break;
        
        default:
          # code...
          break;
      }

      if ($this->query($query)) {
        switch ($getter) {
          case 'array' : 
            $data = $this->fetch_array();
            break;
          case 'fetch' : 
            $data = $this->fetch();
            break;
          case 'fetch_assoc' : 
            $data = $this->fetch_assoc();
            break;
          case 'fetch_object' : 
            $data = $this->fetch_object();
            break;
          default :
            $data = $this->fetch_array();
        }

      }
      return $data;

    }







    /**
     * Query MySQL database
     * @param  string   $query  SQL query
     * @return boolean          Boolean false on error, Array on success
     */

    public function query ($query = "SHOW TABLES;" ) {
      $this->res = $this->mysqli->query($query);

      if ($this->res) {
        return $this->res;
      }
      else {
        if ($this->mysqli->errno) {
          print($this->mysqli->errno . " : " . $this->mysqli->error . "\n");
          return false;
        }
      }
      return $this->res;
    }


    /**
     * @param  string   $query  SQL query
     * @return boolean          Boolean false on error, Array on success
     */

    public function error () {
      if ($this->mysqli->errno) {
        print($this->mysqli->errno . " : " . $this->mysqli->error);
        return $this->mysqli->error;
      }
    }


    /**
     * Fetch result of previous query
     * @return Array|boolean  Boolean false on error, result from fetch_assoc() on success 
     */

    public function fetch () {

      $result = array();
      if (!$this->res) {
        return false;
      }

      while ($row = ($this->res->fetch_array())) {
        array_push($result, $row[0]);
      }

      return $result;
    }



    public function fetch_array () {

      $result = array();
      if (!$this->res) {
        return false;
      }

      while ($row = ($this->res->fetch_array())) {
        array_push($result, $row);
      }

      return $result;
    }

    /**
     * Fetch result of previous query
     * @return Array|boolean  Boolean false on error, result from fetch_assoc() on success 
     */

    public function fetch_assoc () {

      $result = array();
      if (!$this->res) {
        return false;
      }

      while ($row = ($this->res->fetch_assoc())) {
        array_push($result, $row);
      }

      return $result;
    }


    /**
     * Fetch result of previous query
     * @return Array|boolean  Boolean false on error, result from fetch_assoc() on success 
     */

    public function fetch_object () {

      $result = array();
      if (!$this->res) {
        return false;
      }

      while ($row = ($this->res->fetch_object())) {
        array_push($result, $row);
      }

      return $result;
    }


  }



?>