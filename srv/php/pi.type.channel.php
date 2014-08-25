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



  require_once('pi.php');
  // require_once('pi.db.php');


  class PiTypeException extends PiException {};
    


  class PiType {

    private   $name       = 'type';
    private   $address    = null;
    protected $type       = null;

    protected $handlers   = array();

    // basic types
    const NULL     = null;
    const NAN      = NaN;

    const STRING   = 1;
    const NUMBER   = 2;


    // basic integer types

    // unsigned
    const UINT8    = 3;
    const UINT16   = 4;
    const UINT32   = 5;
    const UINT64   = 6;


    // signed
    const INT8    = 7;
    const INT16   = 8;
    const INT32   = 9;
    const INT64   = 10;


    // typed arrays, unsigned
    const UINT8ARRAY    = 11;
    const UINT16ARRAY   = 12;
    const UINT32ARRAY   = 13;
    const UINT64ARRAY   = 14;

    // typed arrays, signed
    const INT8ARRAY    = 15;
    const INT16ARRAY   = 16;
    const INT32ARRAY   = 17;
    const INT64ARRAY   = 18;


    // floating point types
    const FLOAT32  = 19;
    const FLOAT64  = 20;

    // typed arrays, floating point values
    const FLOAT32ARRAY = 21;
    const FLOAT64ARRAY = 22;



    // complex types
    const RANGE    = 23;
    const ARRAY    = 24;
    const BYTEARRAY    = 25;

    const OBJECT    = 26;
    
    // synonyms
    const STRUCT    = 27;
    const RECORD    = 27;



    // higher order types
    const FILE   = 28;
    const IMAGE  = 29;
    const DATA   = 30;
    const TEL    = 31;
    const GEO    = 32;
    const EMAIL  = 33;
    const URL    = 34;



      // internal types for Pi

      const FORMAT   = 35;
      const CHANNEL  = 36;
      const ADDRESS  = 37;

      const IGBINARY  = 240;
      const BASE64    = 241;



      const USER  = 100;
      const USERGROUP  = 101;
      const PERMISSIONS  = 102;
      const TOKEN  = 103;
      const JSON  = 104;
      const MYSQL  = 105;
      const REDIS  = 106;
      const LIST  = 107;

      // a UINT32
      const IP     = 108;
      const IPV4   = 108;

      // a UINT32 QUAD ?
      const IPV6     = 109;


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      const SHORTSTRING   = 108;

      // ANSI string, C-compatible null-terminated binary string
      const ANSISTRING   = 109;

      // UTF-8 string
      const UTF8   = 110;



    // channels
    const AUTH     = 38;
    const CHAT     = 39;
    const DEBUG    = 40;
    const WARNING  = 41;
    const ERROR    = 42;
    const LOG      = 43;
    const TYPE     = 44;
    const DB       = 45;
    const PING     = 46;
    const CTRL     = 47;
    const ADMIN    = 48;
    const SYS      = 49;
    const REGEX    = 50;

    // push channel
    const PUSH    = 200;



    // date and time related types
    const WEEK = 51;
    const TIME = 52;
    const DATE = 53;
    const DATETIME = 54;
    const DATETIME_LOCAL = 55;





    public function __construct($type = null, $ttl = null) {
      // call Pi Base class constructor (takes no arguments)
      parent::__construct();


    }







    /*
      $object = PiType::New('Object', $args);
      $file   = PiType::New('File', $args);
      $image  = PiType::New('Image', $args);
      etc, etc
     */

    /**
     * "Factory" of sorts, to create new instances of PiType descendants
     * @param string $className Class name, e.g. : FileType, ImageType, DataType, etc
     * @param Type $args      Arguments for the class constructor
     */

    public static function New($className, $args) { 
       if(class_exists($className) && is_subclass_of($className, 'PiType'))
       { 
          return new $className($args);
       } 
    }  


  }



?>