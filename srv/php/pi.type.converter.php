<?

  /**
   *  Pi Type Converter class
   *
   *  Compatibility and interoperability layer
   *  
   *  Converts between Pi Types in different formats: 
   *  JSON, MySQL, PHP, JavaScript, igbinary, Redis types, etc
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');



  class PiTypeConversionException extends PiException {};
  


  class PiTypeConverter {

    protected $name       = 'converter';
    protected $TYPE       = null;
    protected $value      = null;


    /*  TYPE DEFINITIONS  */

    // // basic types
    //  PI_NAN;

    //  PI_STR;
    //  PI_NUMBER;


    // // floating point types
    //  PI_FLOAT32;
    //  PI_FLOAT64;


    // // basic integer types

    // // unsigned
    //  PI_UINT8;
    //  PI_UINT16;
    //  PI_UINT32;
    //  PI_UINT64;


    // // signed
    //  PI_INT8;
    //  PI_INT16;
    //  PI_INT32;
    //  PI_INT64;


    // // typed arrays, unsigned
    //  PI_UINT8ARRAY;
    //  PI_UINT16ARRAY;
    //  PI_UINT32ARRAY;
    //  PI_UINT64ARRAY;

    // // typed arrays, signed
    //  PI_INT8ARRAY;
    //  PI_INT16ARRAY;
    //  PI_INT32ARRAY;
    //  PI_INT64ARRAY;


    // // typed arrays, floating point values
    //  PI_FLOAT32ARRAY;
    //  PI_FLOAT64ARRAY;



    // // complex types
    //  PI_RANGE;
    //  PI_ARRAY;
    //  PI_BYTEARRAY;

    // // synonyms
    //  PI_STRUCT;
    //  PI_RECORD;



    // // higher order types
    //  PI_FILE;
    //  PI_IMAGE;
    //  PI_DATA;
    //  PI_TEL;
    //  PI_GEO;
    //  PI_EMAIL;
    //  PI_URL;



    //   // Pi internal types

    //    PI_FORMAT;
    //    PI_CHANNEL;
    //    PI_ADDRESS;

    //    PI_IGBINARY;
    //    PI_BASE64;


    //   // common internal object types
    //    PI_USER;
    //    PI_USERGROUP;
    //    PI_PERMISSIONS;

    //    PI_TOKEN;
    //    PI_JSON;
    //    PI_MYSQL;
    //    PI_REDIS;
    //    PI_LIST;


    //   // a UINT32
    //    PI_IP;
    //    PI_IPV4;

    //   // a UINT32 QUAD ?
    //    PI_IPV6;


    //   // PASCAL string, ZeroMQ-compatible fixed-length binary string
    //    PI_SHORTSTRING;

    //   // ANSI string, C-compatible null-terminated binary string
    //    PI_ANSISTRING;

    //   // UTF-8 string
    //    PI_UTF8;



    // // date and time related types
    //  PI_WEEK;
    //  PI_TIME;
    //  PI_DATE;
    //  PI_DATETIME;
    //  PI_DATETIME_LOCAL;




    public $MYSQL = array();


    /*  TYPE DEFINITIONS  */

    // basic types
     $MYSQL['TYPE'][PI_NAN] = 'NAN';

     $MYSQL['TYPE'][PI_STR] = 'TEXT';
     $MYSQL['TYPE'][PI_NUMBER] = 'INT';


    // floating point types
     $MYSQL['TYPE'][PI_FLOAT32] = 'FLOAT';
     $MYSQL['TYPE'][PI_FLOAT64] = 'DOUBLE';


    // basic integer types

    // unsigned
     $MYSQL['TYPE'][PI_UINT8] = 'UNSIGNED INT8';
     $MYSQL['TYPE'][PI_UINT16] = 'UINT16';
     $MYSQL['TYPE'][PI_UINT32] = 'UINT32';
     $MYSQL['TYPE'][PI_UINT64] = 'UINT64';


    // signed
     $MYSQL['TYPE'][PI_INT8] = 'INT8';
     $MYSQL['TYPE'][PI_INT16] = 'INT16';
     $MYSQL['TYPE'][PI_INT32] = 'INT32';
     $MYSQL['TYPE'][PI_INT64] = 'INT64';


    // typed arrays, unsigned
     $MYSQL['TYPE'][PI_UINT8ARRAY] = 'UINT8ARRAY';
     $MYSQL['TYPE'][PI_UINT16ARRAY] = 'UINT16ARRAY';
     $MYSQL['TYPE'][PI_UINT32ARRAY] = 'UINT32ARRAY';
     $MYSQL['TYPE'][PI_UINT64ARRAY] = 'UINT64ARRAY';

    // typed arrays, signed
     $MYSQL['TYPE'][PI_INT8ARRAY] = 'INT8ARRAY';
     $MYSQL['TYPE'][PI_INT16ARRAY] = 'INT16ARRAY';
     $MYSQL['TYPE'][PI_INT32ARRAY] = 'INT32ARRAY';
     $MYSQL['TYPE'][PI_INT64ARRAY] = 'INT64ARRAY';


    // typed arrays, floating point values
     $MYSQL['TYPE'][PI_FLOAT32ARRAY] = 'FLOAT32ARRAY';
     $MYSQL['TYPE'][PI_FLOAT64ARRAY] = 'FLOAT64ARRAY';



    // complex types
     $MYSQL['TYPE'][PI_RANGE] = 'RANGE';
     $MYSQL['TYPE'][PI_ARRAY] = 'ARRAY';
     $MYSQL['TYPE'][PI_BYTEARRAY] = 'BYTEARRAY';

    // synonyms
     $MYSQL['TYPE'][PI_STRUCT] = 'STRUCT';
     $MYSQL['TYPE'][PI_RECORD] = 'RECORD';



    // higher order types
     $MYSQL['TYPE'][PI_FILE] = 'FILE';
     $MYSQL['TYPE'][PI_IMAGE] = 'IMAGE';
     $MYSQL['TYPE'][PI_DATA] = 'DATA';
     $MYSQL['TYPE'][PI_TEL] = 'TEL';
     $MYSQL['TYPE'][PI_GEO] = 'GEO';
     $MYSQL['TYPE'][PI_EMAIL] = 'EMAIL';
     $MYSQL['TYPE'][PI_URL] = 'URL';



      // Pi internal types

       $MYSQL['TYPE'][PI_FORMAT] = 'FORMAT';
       $MYSQL['TYPE'][PI_CHANNEL] = 'CHANNEL';
       $MYSQL['TYPE'][PI_ADDRESS] = 'ADDRESS';

       $MYSQL['TYPE'][PI_IGBINARY] = 'IGBINARY';
       $MYSQL['TYPE'][PI_BASE64] = 'BASE64';


      // common internal object types
       $MYSQL['TYPE'][PI_USER] = 'USER';
       $MYSQL['TYPE'][PI_USERGROUP] = 'USERGROUP';
       $MYSQL['TYPE'][PI_PERMISSIONS] = 'PERMISSIONS';

       $MYSQL['TYPE'][PI_TOKEN] = 'TOKEN';
       $MYSQL['TYPE'][PI_JSON] = 'JSON';
       $MYSQL['TYPE'][PI_MYSQL] = 'MYSQL';
       $MYSQL['TYPE'][PI_REDIS] = 'REDIS';
       $MYSQL['TYPE'][PI_LIST] = 'LIST';


      // a UINT32
       $MYSQL['TYPE'][PI_IP] = 'IP';
       $MYSQL['TYPE'][PI_IPV4] = 'IPV4';

      // a UINT32 QUAD ?
       $MYSQL['TYPE'][PI_IPV6] = 'IPV6';


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
       $MYSQL['TYPE'][PI_SHORTSTRING] = 'SHORTSTRING';

      // ANSI string, C-compatible null-terminated binary string
       $MYSQL['TYPE'][PI_ANSISTRING] = 'ANSISTRING';


    // date and time related types
     $MYSQL['TYPE'][PI_WEEK] = 'WEEK';
     $MYSQL['TYPE'][PI_TIME] = 'TIME';
     $MYSQL['TYPE'][PI_DATE] = 'DATE';
     $MYSQL['TYPE'][PI_DATETIME] = 'DATETIME';
     $MYSQL['TYPE'][PI_DATETIME_LOCAL] = 'DATETIME_LOCAL';




    public function __construct($type = null) {
      // call Pi Base class constructor (takes no arguments)
      // parent::__construct();

      if (is_int($type)) {
        $this->TYPE = $type;
      }

    }

  }



?>