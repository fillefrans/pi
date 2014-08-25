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


  class PiTypeException extends PiException {};
    


  class PiType {

    private   $name       = 'type';
    protected $type       = null;
    protected $value      = null;


    /*  TYPE DEFINITIONS  */

    // basic types
    const NAN      = NaN;

    const STR      = 1;
    const NUMBER   = 2;


    // floating point types
    const FLOAT32  = 5;
    const FLOAT64  = 6;


    // basic integer types

    // unsigned
    const UINT8    = 9;
    const UINT16   = 10;
    const UINT32   = 11;
    const UINT64   = 12;


    // signed
    const INT8    = 17;
    const INT16   = 18;
    const INT32   = 19;
    const INT64   = 20;


    // typed arrays, unsigned
    const UINT8ARRAY    = 31;
    const UINT16ARRAY   = 32;
    const UINT32ARRAY   = 33;
    const UINT64ARRAY   = 34;

    // typed arrays, signed
    const INT8ARRAY    = 65;
    const INT16ARRAY   = 66;
    const INT32ARRAY   = 67;
    const INT64ARRAY   = 68;


    // typed arrays, floating point values
    const FLOAT32ARRAY = 7;
    const FLOAT64ARRAY = 8;



    // complex types
    const RANGE    = 123;
    const ARRAY    = 124;
    const BYTEARRAY    = 125;

    // synonyms
    const STRUCT    = 127;
    const RECORD    = 127;



    // higher order types
    const FILE   = 128;
    const IMAGE  = 129;
    const DATA   = 130;
    const TEL    = 131;
    const GEO    = 132;
    const EMAIL  = 133;
    const URL    = 134;



      // Pi internal types

      const FORMAT   = 135;
      const CHANNEL  = 136;
      const ADDRESS  = 137;

      const IGBINARY  = 240;
      const BASE64    = 241;


      // common internal object types
      const USER        = 100;
      const USERGROUP   = 101;
      const PERMISSIONS = 102;

      const TOKEN = 103;
      const JSON  = 104;
      const MYSQL = 105;
      const REDIS = 106;
      const LIST  = 107;


      // a UINT32
      const IP     = 108;
      const IPV4   = 108;

      // a UINT32 QUAD ?
      const IPV6     = 109;


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      const SHORTSTRING   = 110;

      // ANSI string, C-compatible null-terminated binary string
      const ANSISTRING   = 111;

      // UTF-8 string
      const UTF8   = 112;



    // date and time related types
    const WEEK = 51;
    const TIME = 52;
    const DATE = 53;
    const DATETIME = 54;
    const DATETIME_LOCAL = 55;





    public function __construct($type = null) {
      // call Pi Base class constructor (takes no arguments)
      // parent::__construct();

      if (is_int($type)) {
        $this->type = $type;
      }

    }


    // Default getter and setter : override in Subclasses

    public function get () {
      return $this->value;
    }

    public function set ($value = null) {
      $this->value = $value;
    }

    public function getset ($value = null) {
      $previous = $this->value;
      $this->value = $value;
      return $previous;
    }



    /*
      $object = PiType::New('Object', $args);
      $file   = PiType::New('File', $args);
      $image  = PiType::New('Image', $args);
      etc, etc
     */

    /**
     * "Factory" of sorts, to create new instances of PiType descendants
     * @param string $className Class name, e.g. : File, Image, Uint, Date, Week, etc
     * @param Type $args      Arguments for the class constructor
     * @example
     *       $object = PiType::New('Object', $args);
     *       $file   = PiType::New('File', $args);
     *       $uint   = PiType::New('Uint', $size);
     *       $image  = PiType::New('Image', $args);
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