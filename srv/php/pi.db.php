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

  require_once('pi.config.php');



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
        $this->mysqli = new MySQLi(PI_DB['host'], PI_DB['user'], PI_DB['password'], PI_DB['db'], PI_DB['port']);
        if (!$this->mysqli instanceof MySQLi) {
          return null;
        }
      }
    }


    /**
     * Query MySQL database
     * @param  string   $query  SQL query
     * @return boolean          Boolean false on error, Array on success
     */

    public function query ($query = "" ) {
      $this->res = $this->mysqli->query($query);
      return $this->res;
    }


    /**
     * Fetch result of previous query
     * @return Array|boolean  Boolean false on error, result from fetch_assoc() on success 
     */

    public function fetch () {
      if (!$this->res) {
        return false;
      }
      return $this->res->fetch_assoc();
    }


  }



?>