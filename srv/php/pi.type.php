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



  require_once('pi.php');
  // require_once('pi.db.php');

    


  class PiType {

    private   $name       = 'type';
    private   $address    = null;


    // basic types
    public static int $NULL     = null;
    public static int $NAN      = NaN;

    public static int $STRING   = 1;
    public static int $NUMBER   = 2;


    // basic integer types

    // unsigned
    public static int $UINT8    = 3;
    public static int $UINT16   = 4;
    public static int $UINT32   = 5;
    public static int $UINT64   = 6;

    // signed
    public static int $INT8    = 7;
    public static int $INT16   = 8;
    public static int $INT32   = 9;
    public static int $INT64   = 10;


    // typed arrays, unsigned
    public static int $UINT8ARRAY    = 11;
    public static int $UINT16ARRAY   = 12;
    public static int $UINT32ARRAY   = 13;
    public static int $UINT64ARRAY   = 14;

    // typed arrays, signed
    public static int $INT8ARRAY    = 15;
    public static int $INT16ARRAY   = 16;
    public static int $INT32ARRAY   = 17;
    public static int $INT64ARRAY   = 18;


    // floating point types
    public static int $FLOAT32  = 19;
    public static int $FLOAT64  = 20;

    // typed arrays, floating point values
    public static int $FLOAT32ARRAY = 21;
    public static int $FLOAT64ARRAY = 22;



    // complex types
    public static int $RANGE    = 23;
    public static int $ARRAY    = 24;
    public static int $BYTEARRAY    = 25;

    public static int $OBJECT    = 26;
    
    // synonyms
    public static int $STRUCT    = 27;
    public static int $RECORD    = 27;



    // higher order types
    public static int $FILE   = 28;
    public static int $IMAGE  = 29;
    public static int $DATA   = 30;
    public static int $TEL    = 31;
    public static int $GEO    = 32;
    public static int $EMAIL  = 33;
    public static int $URL    = 34;


    // internal types for Pi



    public static int $FORMAT   = 35;
    public static int $CHANNEL  = 36;
    public static int $ADDRESS  = 37;


    public static int $USER  = 100;
    public static int $USERGROUP  = 101;
    public static int $PERMISSIONS  = 102;
    public static int $TOKEN  = 103;
    public static int $JSON  = 104;
    public static int $MYSQL  = 105;
    public static int $REDIS  = 106;
    public static int $LIST  = 107;

    // PASCAL string, ZeroMQ-compatible fixed-length binary string
    public static int $SHORTSTRING   = 108;

    // ANSI string, C-compatible null-terminated binary string
    public static int $ANSISTRING   = 109;

    // UTF-8 string
    public static int $UTF8   = 110;



    // channels
    public static int $AUTH     = 38;
    public static int $CHAT     = 39;
    public static int $DEBUG    = 40;
    public static int $WARNING  = 41;
    public static int $ERROR    = 42;
    public static int $LOG      = 43;
    public static int $TYPE     = 44;
    public static int $DB       = 45;
    public static int $PING     = 46;
    public static int $CTRL     = 47;
    public static int $ADMIN    = 48;
    public static int $SYS      = 49;
    public static int $REGEX    = 50;


    public static int $PUSH    = 200;



    // date and time related types
    public static int $WEEK = 51;
    public static int $TIME = 52;
    public static int $DATE = 53;
    public static int $DATETIME = 54;
    public static int $DATETIME_LOCAL = 55;





    public function __construct($address, $object, $ttl=null) {
      // call Pi Base class constructor (takes no arguments)
      parent::__construct();
    }




    /*
      $object = PiType::New('PiType', $args);
      $file   = PiType::New('FileType', $args);
      $image  = PiType::New('ImageType', $args);
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







  class PersistentType extends PiType {

    public function __construct($address, $object) {
      parent::__construct($address, $object);
      $this->db = new PiDB();
    }

  }



  class TransientType extends PiType {
    public function __construct($address, $object, $ttl=null) {
      parent::__construct($address, $object, $ttl);
      $this->redis = new Redis();
    }

    public function 
  }



?>