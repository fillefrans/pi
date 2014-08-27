<?

  /**
   *  Pi Type MySQL class
   *
   *  Implements basic Pi Types for MySQL.
   *  ("Popt" = "Plain Old Pi Type")
   *  
   *  It defines proper aliases for all basic types for MySQL
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');
  // require_once('pi.db.php');


    define('MYSQL_ID', 'PI_ID');


    define('MYSQL_NAN', 'PI_NAN');

    define('MYSQL_NULL', 'NULL');
    define('MYSQL_NOTNULL', 'NOT NULL');
    define('MYSQL_DEFAULT', '');

    define('MYSQL_STR', 'TEXT');
    define('MYSQL_STRING', 'TEXT');
    define('MYSQL_NUMBER', 'INT');


    // floating point types
    define('MYSQL_FLOAT32', 'FLOAT');
    define('MYSQL_FLOAT64', 'DOUBLE');


    // basic integer types

    // unsigned
    define('MYSQL_UINT8',  'UNSIGNED TINYINT');
    define('MYSQL_UINT16', 'UNSIGNED SMALLINT');
    define('MYSQL_UINT32', 'UNSIGNED INT');
    define('MYSQL_UINT64', 'UNSIGNED BIGINT');


    // signed
    define('MYSQL_INT8',   'TINYINT');
    define('MYSQL_INT16',  'SMALLINT');
    define('MYSQL_INT32',  'INT');
    define('MYSQL_INT64',  'BIGINT');


    // typed arrays, unsigned
    // define('MYSQL_UINT8ARRAY',   '');
    // define('MYSQL_UINT16ARRAY',  '');
    // define('MYSQL_UINT32ARRAY',  '');
    // define('MYSQL_UINT64ARRAY',  '');

    // typed arrays, signed
    // define('MYSQL_INT8ARRAY',  '');
    // define('MYSQL_INT16ARRAY', '');
    // define('MYSQL_INT32ARRAY', '');
    // define('MYSQL_INT64ARRAY', '');


    // typed arrays, floating point values
    // define('MYSQL_FLOAT32ARRAY', '');
    // define('MYSQL_FLOAT64ARRAY', '');



    // complex types
    define('MYSQL_RANGE',      'BIGINT');
    define('MYSQL_ARRAY',      'PI_ARRAY');
    define('MYSQL_BYTEARRAY',  'BLOB');

    // synonyms
    define('MYSQL_STRUCT', 'TABLE');
    define('MYSQL_RECORD', 'TABLE');



    // higher order types
    define('MYSQL_FILE',   'BIGINT');
    define('MYSQL_IMAGE',  'BIGINT');
    define('MYSQL_DATA',   'PI_DATA');
    define('MYSQL_TEL',    'VARCHAR');
    define('MYSQL_GEO',    'PI_GEO');
    define('MYSQL_EMAIL',  'PI_EMAIL');
    define('MYSQL_URL',    'PI_URL');



      // Pi internal types

      define('MYSQL_FORMAT',   'PI_FORMAT');
      define('MYSQL_CHANNEL',  'PI_CHANNEL');
      define('MYSQL_ADDRESS',  'PI_ADDRESS');

      define('MYSQL_IGBINARY', 'VARBINARY');
      define('MYSQL_BASE64',   'VARCHAR');


      // common internal object types
      define('MYSQL_USER',         'PI_USER');
      define('MYSQL_USERGROUP',    'PI_USERGROUP');
      define('MYSQL_PERMISSIONS',  'BIT(12)');

      define('MYSQL_TOKEN',  'PI_TOKEN');
      define('MYSQL_JSON',   'PI_JSON');
      define('MYSQL_MYSQL',  'PI_MYSQL');
      define('MYSQL_REDIS',  'PI_REDIS');
      define('MYSQL_LIST',   'PI_LIST');


      // a UINT32
      define('MYSQL_IP',   'UNSIGNED INT');
      define('MYSQL_IPV4', MYSQL_IP);

      // a UINT32 QUAD ?
      define('MYSQL_IPV6', 'BINARY(16)');


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      define('MYSQL_SHORTSTRING', 'VARCHAR');

      // ANSI string, C-compatible null-terminated binary string
      define('MYSQL_ANSISTRING', 'VARCHAR');

      // UTF-8 string
      define('MYSQL_UTF8', 'CHARACTER SET=utf8');



    // date and time related types
    define('MYSQL_DAY',  'DATE');
    define('MYSQL_WEEK', 'DATE');
    define('MYSQL_TIME', 'TIME');
    define('MYSQL_DATE', 'DATE');

    define('MYSQL_DATETIME',       'DATETIME');
    define('MYSQL_DATETIME_LOCAL', 'TIMESTAMP');

    define('MYSQL_TIMESTAMP', 'TIMESTAMP');
    define('MYSQL_DATE_UTC', MYSQL_TIMESTAMP);



    define('MYSQL_HOUR',   'UNSIGNED TINYINT(1)');
    define('MYSQL_MINUTE', 'UNSIGNED TINYINT(1)');
    define('MYSQL_SECOND', 'UNSIGNED TINYINT(1)');

    define('MYSQL_UNIXTIME',   'INT');
    define('MYSQL_MILLITIME',  'BIGINT');
    define('MYSQL_MICROTIME',  'DOUBLE');





  /**
   * pi.type.mysql
   *
   * PiType handler for MySQL
   * Stores, reads, filters, updates PiType values in he Pi db
   *
   * @requires MySQLi
   */
  class PiTypeMySQL extends PiType {

    private   $mysqli = null;
    private   $res    = null;

    protected $name       = 'mysql';
    private   $channel    = PI_DB;


    // CREATE, ADD, ALTER, DROP
    // SELECT, INSERT, UPDATE, DELETE

    /**
     * 
     * @param string  $address Optional adress in Pi namespace
     * @param mixed   $value   Optional initial value
     * @param int     $ttl     Optional Time-to-live in milliseconds
     * @param MySQLi  $mysqli  Optional existing MySQLi instance
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



    public function read($address = null, $id=null, $type=null, $size = null, $offset = 0) {

    }


    public function write(PiType $piobject = null, $address = null, $id = null, $type = null, $size = null, $offset = 0) {
      $mysqlobject = new PiTypeMySQL($piobject);
    }

    public function update(PiType $object = null, $address = null, $id = null, $type = null, $size = null, $offset = 0) {
      
    }

    public function delete(PiType $object = null, $address = null, $id = null, $type = null, $size = null, $offset = 0) {
      
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