<?

  /**
   *  Pi Type class
   *
   *  Defines and implements basic Types for the Pi namespace.
   *  ("Popt" = "Plain Old Pi Type")
   *  These types reflect the available data types in HTML5 and Redis
   *  It defines proper aliases for all basic types for JSON, MySQL, PHP, JavaScript
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  // require_once('pi.php');
  // require_once('pi.db.php');



    // basic types
    define('PI_NAN', NaN);

    define('PI_NULL', null);
    define('PI_DEFAULT', null);

    define('PI_STR', 1);
    define('PI_STRING', 1);
    define('PI_NUMBER', 2);


    // floating point types
    define('PI_FLOAT32', 5);
    define('PI_FLOAT64', 6);


    // basic integer types

    // unsigned
    define('PI_UINT8',  9);
    define('PI_UINT16', 10);
    define('PI_UINT32', 11);
    define('PI_UINT64', 12);


    // signed
    define('PI_INT8',   17);
    define('PI_INT16',  18);
    define('PI_INT32',  19);
    define('PI_INT64',  20);


    // typed arrays, unsigned
    define('PI_UINT8ARRAY',   31);
    define('PI_UINT16ARRAY',  32);
    define('PI_UINT32ARRAY',  33);
    define('PI_UINT64ARRAY',  34);

    // typed arrays, signed
    define('PI_INT8ARRAY',  65);
    define('PI_INT16ARRAY', 66);
    define('PI_INT32ARRAY', 67);
    define('PI_INT64ARRAY', 68);


    // typed arrays, floating point values
    define('PI_FLOAT32ARRAY', 7);
    define('PI_FLOAT64ARRAY', 8);



    // complex types
    define('PI_RANGE',      123);
    define('PI_ARRAY',      124);
    define('PI_BYTEARRAY',  125);

    // synonyms
    define('PI_STRUCT', 127);
    define('PI_RECORD', 127);



    // higher order types

    define('PI_ID', PI_UINT64);

    define('PI_FILE',   128);
    define('PI_IMAGE',  129);
    define('PI_DATA',   130);
    define('PI_TEL',    131);
    define('PI_GEO',    132);
    define('PI_EMAIL',  133);
    define('PI_URL',    134);



      // Pi internal types

      define('PI_FORMAT',   135);
      define('PI_CHANNEL',  136);
      define('PI_ADDRESS',  137);

      define('PI_IGBINARY', 240);
      define('PI_BASE64',   241);


      // common internal object types
      define('PI_USER',         100);
      define('PI_USERGROUP',    101);
      define('PI_PERMISSIONS',  102);

      define('PI_TOKEN',  103);
      define('PI_JSON',   104);
      define('PI_MYSQL',  105);
      define('PI_REDIS',  106);
      define('PI_LIST',   107);

      define('PI_SET',        200);
      define('PI_SORTEDSET',  201);


      // a UINT32
      define('PI_IP',   108);
      define('PI_IPV4', 108);

      // a UINT32 QUAD ?
      define('PI_IPV6', 109);


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      define('PI_SHORTSTRING', 110);

      // ANSI string, C-compatible null-terminated binary string
      define('PI_ANSISTRING', 111);

      // UTF-8 string
      define('PI_UTF8', 112);



    // date and time related types
    define('PI_DAY',  50);
    define('PI_WEEK', 51);
    define('PI_TIME', 52);
    define('PI_DATE', 53);

    define('PI_DATETIME',       54);
    define('PI_DATETIME_LOCAL', 55);

    define('PI_TIMESTAMP', PI_DATETIME_LOCAL);
    define('PI_DATE_UTC', PI_DATETIME_LOCAL);

    define('PI_HOUR',   56);
    define('PI_MINUTE', 57);
    define('PI_SECOND', 58);

    define('PI_UNIXTIME',   59);
    define('PI_MILLITIME',  60);
    define('PI_MICROTIME',  61);




  class PiTypeException extends PiException {};
    


  class PiType {

    private   $name     = 'type';

    // Protected, can be accessed by descendants
    protected $value    = null;

    protected $TYPE     = null;
    protected $DEFAULT  = null;
    protected $SIGNED   = null;
    protected $UNIQUE   = null;
    protected $INDEX    = null;

    // NOTNULL == required
    protected $NOTNULL  = false;
    protected $SIZE     = null;



    /**
     * Const, singleton values shared between all descendants
     *
     * @example
     *   $float = PiType.New(PiType::FLOAT32);
     *
     *   if ($object->TYPE === PiType::TEL) {
     *     // handle telephone numbers here
     *   }
     */

    /*  TYPE DEFINITIONS  */

    // basic types
    const NAN      = PI_NAN;

    const STR      = PI_STR;
    const NUMBER   = PI_NUMBER;


    // floating point types
    const FLOAT32  = PI_FLOAT32;
    const FLOAT64  = PI_FLOAT64;


    // basic integer types

    // unsigned
    const UINT8    = PI_UINT8;
    const UINT16   = PI_UINT16;
    const UINT32   = PI_UINT32;
    const UINT64   = PI_UINT64;


    // signed
    const INT8    = PI_INT8;
    const INT16   = PI_INT16;
    const INT32   = PI_INT32;
    const INT64   = PI_INT64;


    // typed arrays, unsigned
    const UINT8ARRAY    = PI_UINT8ARRAY;
    const UINT16ARRAY   = PI_UINT16ARRAY;
    const UINT32ARRAY   = PI_UINT32ARRAY;
    const UINT64ARRAY   = PI_UINT64ARRAY;

    // typed arrays, signed
    const INT8ARRAY    = PI_INT8ARRAY;
    const INT16ARRAY   = PI_INT16ARRAY;
    const INT32ARRAY   = PI_INT32ARRAY;
    const INT64ARRAY   = PI_INT64ARRAY;


    // typed arrays, floating point values
    const FLOAT32ARRAY = PI_FLOAT32ARRAY;
    const FLOAT64ARRAY = PI_FLOAT64ARRAY;



    // complex types

    const SET       =  PI_SET;
    const SORTEDSET =  PI_SORTEDSET;

    const RANGE    = PI_RANGE;
    const ARRAY    = PI_ARRAY;
    const BYTEARRAY    = PI_BYTEARRAY;

    // synonyms
    const STRUCT    = PI_STRUCT;
    const RECORD    = PI_RECORD;



    // higher order types
    const FILE   = PI_FILE;
    const IMAGE  = PI_IMAGE;
    const DATA   = PI_DATA;
    const TEL    = PI_TEL;
    const GEO    = PI_GEO;
    const EMAIL  = PI_EMAIL;
    const URL    = PI_URL;



      // Pi internal types

      const FORMAT   = PI_FORMAT;
      const CHANNEL  = PI_CHANNEL;
      const ADDRESS  = PI_ADDRESS;

      const IGBINARY  = PI_IGBINARY;
      const BASE64    = PI_BASE64;


      // common internal object types
      const USER        = PI_USER;
      const USERGROUP   = PI_USERGROUP;
      const PERMISSIONS = PI_PERMISSIONS;

      const TOKEN = PI_TOKEN;
      const JSON  = PI_JSON;
      const MYSQL = PI_MYSQL;
      const REDIS = PI_REDIS;
      const LIST  = PI_LIST;


      // a UINT32
      const IP     = PI_IP;
      const IPV4   = PI_IPV4;

      // a UINT32 QUAD ?
      const IPV6     = PI_IPV6;


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      const SHORTSTRING   = PI_SHORTSTRING;

      // ANSI string, C-compatible null-terminated binary string
      const ANSISTRING   = PI_ANSISTRING;

      // UTF-8 string
      const UTF8   = PI_UTF8;



    // date and time related types
    const WEEK = PI_WEEK;
    const TIME = PI_TIME;
    const DATE = PI_DATE;
    const DATETIME = PI_DATETIME;
    const DATETIME_LOCAL = PI_DATETIME_LOCAL;





    public function __construct($type = null) {
      // call Pi Base class constructor (takes no arguments)
      // parent::__construct();

      if (is_int($type)) {
        $this->TYPE = $type;
      }

    }


    // Default getter and setter : override in Subclasses

    public function __set($name,$value){
      $this->str[$name] = $value;
    }


    public function __get($name){
      echo "Overloaded Property name = " . $this->str[$name] . "<br/>";
    }


    public function __isset($name){
      if(isset($this->str[$name])){
        echo "Property \$$name is set.<br/>";   
      } else {
        echo "Property \$$name is not set.<br/>";
      }
    }


    public function __unset($name){
      unset($this->str[$name]);
      echo "\$$name is unset <br/>";
    }






    /**
     * "Factory" of sorts, to create new instances of PiType descendants
     * @param string $className Class name, e.g. : File, Image, Uint, Date, Week, etc
     * @param Type $args      Arguments for the class constructor
     * 
     * @return PiType Instance of class, if found
     * 
     * @example
     *       $object  = PiType::New('Object', $args);
     *       $record  = PiType::New('Struct', $typedef);
     *       $file    = PiType::New('File', $args);
     *       $uint    = PiType::New('Uint', $size);
     *       $image   = PiType::New('Image', $args);
     *       $utc     = PiType::New('Timestamp'[, $UTC]);
     * 
     */

    public static function New($className, $args) { 
      if(!$className) {
        throw new InvalidArgumentException("Invalid className : '$className'", 1);
      }
      if (!is_string($className)) {
        throw new InvalidArgumentException("Expected className to be String, received : " . gettype($className), 1);
      }

      require_once('pi.type.' . strtolower($className) . '.php');
      $className = 'PiType' . $className;

      if(class_exists($className) && is_subclass_of($className, 'PiType')) { 
        return new $className($args);
      }
      else {
        throw new InvalidArgumentException("Class not found : $className", 1);
      }
    }


  }



?>