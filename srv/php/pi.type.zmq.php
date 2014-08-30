<?

  /**
   *  Pi Type ZMQ class
   *
   *  Implements basic Pi Types for ZMQ.
   *  ("Popt" = "Plain Old Pi Type")
   *  
   *  It defines proper aliases for all basic types for ZMQ
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  // require_once('pi.type.php');
  require_once('pi.channel.zmq.php');


    define('ZMQ_ID', 'INT(4) UNSIGNED');


    define('ZMQ_NAN', 'PI_NAN');

    define('ZMQ_NULL', 'NULL');
    define('ZMQ_NOTNULL', 'NOT NULL');
    define('ZMQ_DEFAULT', '');

    define('ZMQ_STR', 'TEXT');
    define('ZMQ_STRING', 'TEXT');
    define('ZMQ_NUMBER', 'INT');


    // floating point types
    define('ZMQ_FLOAT32', 'FLOAT');
    define('ZMQ_FLOAT64', 'DOUBLE');


    // basic integer types

    // unsigned
    define('ZMQ_UINT8',  'TINYINT(1) UNSIGNED');
    define('ZMQ_UINT16', 'SMALLINT(2) UNSIGNED');
    define('ZMQ_UINT32', 'INT(4) UNSIGNED');
    define('ZMQ_UINT64', 'BIGINT(8) UNSIGNED');


    // signed
    define('ZMQ_INT8',   'TINYINT(1)');
    define('ZMQ_INT16',  'SMALLINT(2)');
    define('ZMQ_INT32',  'INT(4)');
    define('ZMQ_INT64',  'BIGINT(8)');


    // typed arrays, unsigned
    // define('ZMQ_UINT8ARRAY',   '');
    // define('ZMQ_UINT16ARRAY',  '');
    // define('ZMQ_UINT32ARRAY',  '');
    // define('ZMQ_UINT64ARRAY',  '');

    // typed arrays, signed
    // define('ZMQ_INT8ARRAY',  '');
    // define('ZMQ_INT16ARRAY', '');
    // define('ZMQ_INT32ARRAY', '');
    // define('ZMQ_INT64ARRAY', '');


    // typed arrays, floating point values
    // define('ZMQ_FLOAT32ARRAY', '');
    // define('ZMQ_FLOAT64ARRAY', '');



    // complex types
    define('ZMQ_RANGE',      'BIGINT');
    define('ZMQ_ARRAY',      'PI_ARRAY');
    define('ZMQ_BYTEARRAY',  'BLOB');

    // synonyms
    define('ZMQ_STRUCT', 'TABLE');
    define('ZMQ_RECORD', 'TABLE');



    // higher order types
    define('ZMQ_FILE',   ZMQ_ID);
    define('ZMQ_IMAGE',  ZMQ_ID);
    define('ZMQ_DATA',   'PI_DATA');
    define('ZMQ_TEL',    'VARCHAR(31)');
    define('ZMQ_GEO',    'POINT');
    define('ZMQ_EMAIL',  'VARCHAR(127)');
    define('ZMQ_URL',    'VARCHAR(767)');



      // Pi internal types

      define('ZMQ_FORMAT',   'PI_FORMAT');
      define('ZMQ_CHANNEL',  'PI_CHANNEL');
      define('ZMQ_ADDRESS',  'PI_ADDRESS');

      define('ZMQ_IGBINARY', 'VARBINARY');
      define('ZMQ_BASE64',   'VARCHAR');


      // common internal object types
      define('ZMQ_USER',         ZMQ_ID);
      define('ZMQ_USERGROUP',    ZMQ_ID);
      define('ZMQ_PERMISSIONS',  'BIT(12)');

      define('ZMQ_TOKEN',  'PI_TOKEN');
      define('ZMQ_JSON',   'TEXT');
      define('ZMQ_MYSQL',  'PI_MYSQL');
      define('ZMQ_REDIS',  'PI_REDIS');
      define('ZMQ_LIST',   'PI_LIST');


      // a UINT32
      define('ZMQ_IP',   'INT(4) UNSIGNED');
      define('ZMQ_IPV4', ZMQ_IP);

      // a UINT32 QUAD ?
      define('ZMQ_IPV6', 'BINARY(16)');


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      define('ZMQ_SHORTSTRING', 'VARCHAR');

      // ANSI string, C-compatible null-terminated binary string
      define('ZMQ_ANSISTRING', 'VARCHAR');

      // UTF-8 string
      define('ZMQ_UTF8', 'CHARACTER SET=utf8');



    // date and time related types
    define('ZMQ_DAY',  'DATE');
    define('ZMQ_WEEK', 'DATE');
    define('ZMQ_TIME', 'TIME');
    define('ZMQ_DATE', 'DATE');

    define('ZMQ_DATETIME',       'DATETIME');
    define('ZMQ_DATETIME_LOCAL', 'TIMESTAMP');

    define('ZMQ_TIMESTAMP', 'TIMESTAMP');
    define('ZMQ_DATE_UTC', ZMQ_TIMESTAMP);



    define('ZMQ_HOUR',   'TINYINT(1) UNSIGNED');
    define('ZMQ_MINUTE', 'TINYINT(1) UNSIGNED');
    define('ZMQ_SECOND', 'TINYINT(1) UNSIGNED');

    define('ZMQ_UNIXTIME',   'INT(4)');
    define('ZMQ_MILLITIME',  'BIGINT(8)');
    define('ZMQ_MICROTIME',  'DOUBLE');





  /**
   * pi.type.zmq
   *
   * PiType handler for ZMQ
   * Stores, reads, filters, updates PiType values in he Pi db
   *
   * @requires ZMQ Php extension (?)
   */
  class PiTypeZMQ extends PiType {

    private   $zmq = null;
    private   $res    = null;

    protected $name       = 'zmq';
    private   $channel    = PIC_ZMQ;


    // CREATE, ADD, ALTER, DROP
    // SELECT, INSERT, UPDATE, DELETE

    /**
     * 
     * @param string  $address Optional adress in Pi namespace
     * @param mixed   $value   Optional initial value
     * @param int     $ttl     Optional Time-to-live in milliseconds
     * @param ZMQ  $zmq  Optional existing ZMQ instance
     */
    public function __construct($address = null, $value = null, $ttl = null, ZMQ $zmq = null) {
      // call PiType class constructor (pass along arguments)
      parent::__construct($address, $value, $ttl);
      if ($zmq instanceof ZMQ) {
        $this->zmq = $zmq;
      }
      else {
        $this->zmq = new ZMQ($PI_DB['host'], $PI_DB['user'], $PI_DB['password'], $PI_DB['db'], $PI_DB['port']);
        if (!$this->zmq instanceof ZMQ) {
          return null;
        }
      }

      $this->_init();

    }



    public function read($address = null, $id=null, $type=null, $size = null, $offset = 0) {

    }


    public function write(PiType $piobject = null, $address = null, $id = null, $type = null, $size = null, $offset = 0) {
      $zmqobject = new PiTypeZMQ($piobject);
    }

    public function update(PiType $object = null, $address = null, $id = null, $type = null, $size = null, $offset = 0) {
      
    }

    public function delete(PiType $object = null, $address = null, $id = null, $type = null, $size = null, $offset = 0) {
      
    }


    protected function _init () {
      // initialization code


    }


    /**
     * Query ZMQ database
     * @param  string   $query  SQL query
     * @return boolean          Boolean false on error, Array on success
     */

    public function query ($query = "" ) {
      $this->res = $this->zmq->query($query);
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