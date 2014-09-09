<?

  /**
   *  Pi Object MYSQL class
   *
   *  A MySQL wrapper for a Pi Object
   *  Has methods to describe, read, and update a Pi Object as a MySQL object
   *  
   *  Simple types return a column description, 
   *  objects return a table description
   *
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   */



  require_once('pi.object.php');

  require_once('pi.type.mysql.php');

  require_once('lib/colors.php');

  /*
    class FileObject extends PiObject
    class ImageObject extends PiObject
    class MovieObject extends PiObject
    class ScriptObject extends PiObject
    class DataObject extends PiObject
    class DBObject extends PiObject

    // class DateObject

   */


  interface MySQLSerializable {
    public function MySQLSerialize ();
  }



  function mysql_encode(MySQLSerializable $object) {
    return $object->MySQLSerialize();
  }






  class PiObjectMySQL extends PiObject implements MySQLSerializable {

    protected   $name         = 'piobject';
    protected   $value        = null;
    protected   $address      = null;
    protected   $channel      = null;
    protected   $id           = null;
    protected   $islink       = null;
    protected   $linkaddress  = null;
    protected   $created      = null;
    protected   $updated      = null;


    // protected   $TYPE         = PI_STRUCT;
    protected   $SIZE         = 0;
    protected   $BINARY       = true;



/* INHERITED FROM PiType 

    protected $TYPE     = null;
    protected $DEFAULT  = null;

    protected $BINARY   = null;
    protected $FLOAT    = null;
    protected $STRING   = null;
    protected $INT      = null;
    protected $SIGNED   = null;
    protected $BITS     = null;
    protected $SIZE     = null;
    protected $UNIQUE   = null;
    protected $INDEX    = null;
    protected $MULTIPLE = null;

    // NOTNULL == required
    protected $NOTNULL  = false;



*/



    public function __construct($address=null, $type=null, $ttl = null, $required = null) {

      if ($type === null && is_int($address)) {
        // echo "setting TYPE to address($address)\n";
        $this->TYPE = $address;
      }

      // call PiType class constructor (pass along arguments)
      parent::__construct($address, $type, $ttl);

      $this->created = time();
      $this->updated = $this->created;
      $this->NOTNULL = (bool) $required;

      // sets channel and address from full address given in constructor
      if ($address && is_string($address)) {
        $this->parseAddress($address);
      }
    }



    public function MySQLSerializeObject() {
      // return the MySQL definition of this type/object

      // Object is defined as having count(members) > 0

      $result = "CREATE TABLE pidata." . $this->name . " (";

      foreach ($this->members as $name => $property) {
        // print("calling : MySQLSerialize()\n");
        $serialized = $this->MySQLSerialize($property);
        $result .= "\n\t$name \t$serialized,";
      }

      // remove final comma, add closing parenthesis

      $result = rtrim($result, ",") . "\n\t) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;\n\n";

      $colors = new Colors();

      $result = $colors->getColoredString($result);


      return $result;
      // return rtrim($result, ",") . "\n\t) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;\n\n";

      // $result = rtrim($result, ",") . "\n);"
      // return $result;
    }



    public function MySQLSerialize($type = null) {
      $result = "";
      // return the MySQL definition of this type/object
      // 
      // echo "MySQLSerialize : " . gettype($type) . "\n";

      if ($type === null) {
        // echo "TYPE is {$this->TYPE}\n";
        $type = $this->TYPE;
        // echo "type is NULL, setting to : $type\n";
      }

      if ($type === null) {
        // if type is still NULL
        $type = PI_NULL;
        // echo "we still have no type!";
      }

      if ($type instanceof PiType) {
        // print("type is PiType in MySQLSerialize()\n");
        $value = $type;
        $this->SIZE = $type->SIZE;
        $type = $type->TYPE;
      }
      switch ($type) {

        case PI_STRUCT  : 
          // print("calling : MySQLSerializeObject()\n");
          return $this->MySQLSerializeObject();
          break; // ?

        // case PI_UINT8     : $result .= MYSQL_UINT8;   break;
        // case PI_UINT16    : $result .= MYSQL_UINT16;  break;
        // case PI_UINT32    : $result .= MYSQL_UINT32;  break;
        // case PI_UINT64    : $result .= MYSQL_UINT64;  break;

        // case PI_INT8     : $result .= MYSQL_INT8;   break;
        // case PI_INT16    : $result .= MYSQL_INT16;  break;
        // case PI_INT32    : $result .= MYSQL_INT32;  break;
        // case PI_INT64    : $result .= MYSQL_INT64;  break;


        case PI_STR     :
          $result .= "VARCHAR";
          if ($this->SIZE > 1 && $this->SIZE <= 255) {
            $result .= "({$this->SIZE})";
          }
          break;


    case PI_STR : $result .= MYSQL_STR; echo "sz:" . $this->SIZE; break;
    case PI_STRING : $result .= MYSQL_STRING; break;
    case PI_NUMBER : $result .= MYSQL_NUMBER; break;


    // floating point types
    case PI_FLOAT32 : $result .= MYSQL_FLOAT32; break;
    case PI_FLOAT64 : $result .= MYSQL_FLOAT64; break;


    // basic integer types

    // unsigned
    case PI_UINT8 : $result .= MYSQL_UINT8; break;
    case PI_UINT16 : $result .= MYSQL_UINT16; break;
    case PI_UINT32 : $result .= MYSQL_UINT32; break;
    case PI_UINT64 : $result .= MYSQL_UINT64; break;


    // signed
    case PI_INT8 : $result .= MYSQL_INT8; break;
    case PI_INT16 : $result .= MYSQL_INT16; break;
    case PI_INT32 : $result .= MYSQL_INT32; break;
    case PI_INT64 : $result .= MYSQL_INT64; break;


    // typed arrays, unsigned
    // case PI_UINT8ARRAY : $result .= MYSQL_UINT8ARRAY; break;
    // case PI_UINT16ARRAY : $result .= MYSQL_UINT16ARRAY; break;
    // case PI_UINT32ARRAY : $result .= MYSQL_UINT32ARRAY; break;
    // case PI_UINT64ARRAY : $result .= MYSQL_UINT64ARRAY; break;

    // typed arrays, signed
    // case PI_INT8ARRAY : $result .= MYSQL_INT8ARRAY; break;
    // case PI_INT16ARRAY : $result .= MYSQL_INT16ARRAY; break;
    // case PI_INT32ARRAY : $result .= MYSQL_INT32ARRAY; break;
    // case PI_INT64ARRAY : $result .= MYSQL_INT64ARRAY; break;


    // typed arrays, floating point values
    // case PI_FLOAT32ARRAY : $result .= MYSQL_FLOAT32ARRAY; break;
    // case PI_FLOAT64ARRAY : $result .= MYSQL_FLOAT64ARRAY; break;



    // complex types
    case PI_RANGE : $result .= MYSQL_RANGE; break;
    case PI_ARRAY : $result .= MYSQL_ARRAY; break;
    case PI_BYTEARRAY : $result .= MYSQL_BYTEARRAY; break;

    // synonyms
    case PI_STRUCT : $result .= MYSQL_STRUCT; break;
    case PI_RECORD : $result .= MYSQL_RECORD; break;



    // higher order types
    case PI_FILE : $result .= MYSQL_FILE; break;
    case PI_IMAGE : $result .= MYSQL_IMAGE; break;
    case PI_DATA : $result .= MYSQL_DATA; break;
    case PI_TEL : $result .= MYSQL_TEL; break;
    case PI_GEO : $result .= MYSQL_GEO; break;
    case PI_EMAIL : $result .= MYSQL_EMAIL; break;
    case PI_URL : $result .= MYSQL_URL; break;



      // Pi internal types

      case PI_FORMAT : $result .= MYSQL_FORMAT; break;
      case PI_CHANNEL : $result .= MYSQL_CHANNEL; break;
      case PI_ADDRESS : $result .= MYSQL_ADDRESS; break;

      case PI_IGBINARY : $result .= MYSQL_IGBINARY; break;
      case PI_BASE64 : $result .= MYSQL_BASE64; break;


      // common internal object types
      case PI_USER : $result .= MYSQL_USER; break;
      case PI_USERGROUP : $result .= MYSQL_USERGROUP; break;
      case PI_PERMISSIONS : $result .= MYSQL_PERMISSIONS; break;

      case PI_TOKEN : $result .= MYSQL_TOKEN; break;
      case PI_JSON : $result .= MYSQL_JSON; break;
      case PI_MYSQL : $result .= MYSQL_MYSQL; break;
      case PI_REDIS : $result .= MYSQL_REDIS; break;
      case PI_LIST : $result .= MYSQL_LIST; break;


      // a UINT32
      case PI_IP : $result .= MYSQL_IP; break;
      case PI_IPV4 : $result .= MYSQL_IPV4; break;

      // a UINT32 QUAD ?
      case PI_IPV6 : $result .= MYSQL_IPV6; break;


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      case PI_SHORTSTRING : $result .= MYSQL_SHORTSTRING; break;

      // ANSI string, C-compatible null-terminated binary string
      case PI_ANSISTRING : $result .= MYSQL_ANSISTRING; break;

      // UTF-8 string
      case PI_UTF8 : $result .= MYSQL_UTF8; break;



    // date and time related types
    case PI_DAY : $result .= MYSQL_DAY; break;
    case PI_WEEK : $result .= MYSQL_WEEK; break;
    case PI_TIME : $result .= MYSQL_TIME; break;
    case PI_DATE : $result .= MYSQL_DATE; break;

    case PI_DATETIME : $result .= MYSQL_DATETIME; break;
    case PI_DATETIME_LOCAL : $result .= MYSQL_DATETIME_LOCAL; break;

    case PI_TIMESTAMP : $result .= MYSQL_TIMESTAMP; break;
    case PI_DATE_UTC : $result .= MYSQL_DATE_UTC; break;



    case PI_HOUR : $result .= MYSQL_HOUR; break;
    case PI_MINUTE : $result .= MYSQL_MINUTE; break;
    case PI_SECOND : $result .= MYSQL_SECOND; break;

    case PI_UNIXTIME : $result .= MYSQL_UNIXTIME; break;
    case PI_MILLITIME : $result .= MYSQL_MILLITIME; break;
    case PI_MICROTIME : $result .= MYSQL_MICROTIME; break;





        case PI_NUMBER     :
          if ($this->SIGNED === false) {
            $result .= "UNSIGNED ";
          }
          $result .= "INT";
          if ($this->SIZE) {
            $result .= "(" . $this->SIZE . ")";
          }
          else {
            $result .= "(" . PI_NUMBER . ")";
          }
          break;

        default:
          return "[unknown type]";
      } // switch

      // return result, or error string if result is empty
      // print "returning $result" . ($result ? "" : " --> [MySQLSerializer ERROR : The object is empty.]");

      // echo basename(__FILE__) . ": " . "returning '$result', size " . $this->SIZE ."\n";

      return $result;

    }


    public function parseAddress($address = null) {

      if (!$address) {
        return;
      }

      try {

        // throw PiTypeException within try block to trigger an InvalidArgumentException

          if (!is_string($address)) {
            throw new PiTypeException("Expected 'address' to be String, " . gettype($address) . " received." , 1);
          }
          if(strpos($address, "|")) {

            if (substr_count($address, "|") > 1) {
              // error: more than on pipe character in raw address
              throw new PiTypeException("Too many pipe characters (" . substr_count($address, "|") . ") in raw address (there can be only one).", 1);
            }

            // rawaddress with channel
            $rawaddress = explode($address, "|", 2);
            if(count($rawaddress)==2) {
              $this->channel = $this->parseChannel($rawaddress[0]);
              $this->address = $this->parseAddress($rawaddress[1]);
              return $this->address;
            }

          }
          // if no "|" found, the default handling of simple address
          elseif (strpos($address, "pi.") === 0) {
            // simple address, no channel
            $this->address  = $address;
            $this->wildcard = (strpos($address, ".*") !== false);

            return $this->address;
          }
          // if address does NOT start with "pi."
          elseif (strpos($address, ".")) {
            // relative address, assume prefix "pi.app."
          }
          else {
            throw new PiTypeException("Invalid address", 1);
            
          }

      }
      catch (PiTypeException $e) {
          throw new InvalidArgumentException($e->getMessage(), 1);
          return false;
      }
    }



    public static function parseChannel($channel = null) {
      try {

  
        if(strpos($channel, "|")) {
          throw new PiTypeException("Invalid channel: cannot contain pipe character", 1);
        }
  
        elseif (strpos($channel, ":")) {
          $rawchannel = explode($channel, ":", 2);
          $this->channel  = trim($channel[0]);
          if(is_numeric(trim($channel[1]))) {
            // numeric id attached to channel (i.e. a session id or similar)
            $this->id  = intval(trim($channel[1]), 10);
          }
          else {
            // alphanumeric id attached to channel (i.e. a session id or similar)
            $this->id  = trim($channel[1]);
          }
        }

        else {
            // no value attached to channel
          $this->channel = trim($channel);
          
        }

        return $this->channel;

      }
      catch (PiTypeException $e) {
        throw new InvalidArgumentException($e->getMessage(), 1);
        return false;
      }
    }


  }



?>