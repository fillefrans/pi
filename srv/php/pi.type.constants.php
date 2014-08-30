<?

  /**
   *  Pi Type Constants definitions
   *
   *  Compatibility and interoperability layer
   *  
   *  Defines Pi Types and Pi Type Strings
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.sortedset.php');


  class PiTypeConstantException extends PiException {};
  


  class PiTypeConstants extends PiTypeSortedSet {

    protected $name       = 'constants';
    protected $TYPE       = PiType::ARRAY;
    protected $value      = array();


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



    /*  TYPE DEFINITIONS  */

    // basic types
     $this->names[PI_NAN] = 'NAN';

     $this->names[PI_STR] = 'TEXT';
     $this->names[PI_NUMBER] = 'INT';


    // floating point types
     $this->names[PI_FLOAT32] = 'FLOAT';
     $this->names[PI_FLOAT64] = 'DOUBLE';


    // basic integer types

    // unsigned
     $this->names[PI_UINT8] = 'UNSIGNED INT8';
     $this->names[PI_UINT16] = 'UINT16';
     $this->names[PI_UINT32] = 'UINT32';
     $this->names[PI_UINT64] = 'UINT64';


    // signed
     $this->names[PI_INT8] = 'INT8';
     $this->names[PI_INT16] = 'INT16';
     $this->names[PI_INT32] = 'INT32';
     $this->names[PI_INT64] = 'INT64';


    // typed arrays, unsigned
     $this->names[PI_UINT8ARRAY] = 'UINT8ARRAY';
     $this->names[PI_UINT16ARRAY] = 'UINT16ARRAY';
     $this->names[PI_UINT32ARRAY] = 'UINT32ARRAY';
     $this->names[PI_UINT64ARRAY] = 'UINT64ARRAY';

    // typed arrays, signed
     $this->names[PI_INT8ARRAY] = 'INT8ARRAY';
     $this->names[PI_INT16ARRAY] = 'INT16ARRAY';
     $this->names[PI_INT32ARRAY] = 'INT32ARRAY';
     $this->names[PI_INT64ARRAY] = 'INT64ARRAY';


    // typed arrays, floating point values
     $this->names[PI_FLOAT32ARRAY] = 'FLOAT32ARRAY';
     $this->names[PI_FLOAT64ARRAY] = 'FLOAT64ARRAY';



    // complex types
     $this->names[PI_RANGE] = 'RANGE';
     $this->names[PI_ARRAY] = 'ARRAY';
     $this->names[PI_BYTEARRAY] = 'BYTEARRAY';

    // synonyms
     $this->names[PI_STRUCT] = 'STRUCT';
     $this->names[PI_RECORD] = 'RECORD';



    // higher order types
     $this->names[PI_FILE] = 'FILE';
     $this->names[PI_IMAGE] = 'IMAGE';
     $this->names[PI_DATA] = 'DATA';
     $this->names[PI_TEL] = 'TEL';
     $this->names[PI_GEO] = 'GEO';
     $this->names[PI_EMAIL] = 'EMAIL';
     $this->names[PI_URL] = 'URL';



      // Pi internal types

       $this->names[PI_FORMAT] = 'FORMAT';
       $this->names[PI_CHANNEL] = 'CHANNEL';
       $this->names[PI_ADDRESS] = 'ADDRESS';

       $this->names[PI_IGBINARY] = 'IGBINARY';
       $this->names[PI_BASE64] = 'BASE64';


      // common internal object types
       $this->names[PI_USER] = 'USER';
       $this->names[PI_USERGROUP] = 'USERGROUP';
       $this->names[PI_PERMISSIONS] = 'PERMISSIONS';

       $this->names[PI_TOKEN] = 'TOKEN';
       $this->names[PI_JSON] = 'JSON';
       $this->names[PI_MYSQL] = 'MYSQL';
       $this->names[PI_REDIS] = 'REDIS';
       $this->names[PI_LIST] = 'LIST';


      // a UINT32
       $this->names[PI_IP] = 'IP';
       $this->names[PI_IPV4] = 'IPV4';

      // a UINT32 QUAD ?
       $this->names[PI_IPV6] = 'IPV6';


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
       $this->names[PI_SHORTSTRING] = 'SHORTSTRING';

      // ANSI string, C-compatible null-terminated binary string
       $this->names[PI_ANSISTRING] = 'ANSISTRING';


    // date and time related types
     $this->names[PI_WEEK] = 'WEEK';
     $this->names[PI_TIME] = 'TIME';
     $this->names[PI_DATE] = 'DATE';
     $this->names[PI_DATETIME] = 'DATETIME';
     $this->names[PI_DATETIME_LOCAL] = 'DATETIME_LOCAL';




    public function __construct($type = null) {
      // call Pi Base class constructor (takes no arguments)
      // parent::__construct();

      if (is_int($type)) {
        $this->TYPE = $type;
      }

    }

  }



?>