<?

  /**
   *  Pi Type class
   *
   *  Implements basic Types for the Pi namespace.
   *  ("Popt" = "Plain Old Pi Type")
   *  These types reflect the available data types in HTML5 and Redis
   *  It defines proper aliases for all basic types for JSON, MySQL, PHP, JavaScript
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');
  // require_once('pi.db.php');

    



  /**
   * pi.type.mysql
   *
   * PiType handler for MySQL
   * Stores, reads, filters, updates PiType values in he Pi db
   *
   * @requires MySQLi
   */
  class PiTypeMySQL extends PiType {

    private     $mysqli = null;
    private     $res    = null;

    private   $name       = 'mysql';
    private   $channel    = null;

    public function __construct() {
    }




    /**
     * Constructor
     * @param   MySQLi  $mysqli   Optional existing MySQLi instance
     */
    
    public function __construct($address=null, $value=null, $ttl=null, MySQLi $mysqli = null) {
      // call PiType class constructor (pass along arguments)
      parent::__construct($address, $value, $ttl);
      if ($mysqli instanceof MySQLi) {
        $this->mysqli = $mysqli;
      }
      else {
        $this->mysqli = new MySQLi($PI_DB['host'], $PI_DB['user'], $PI_DB['password'], $PI_DB['db'], $PI_DB['port']);
        if (!$this->mysqli instanceof MySQLi) {
          return null;
        }
      }

      $this->_init();


    }



    protected function _init () {
      // initialization code


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