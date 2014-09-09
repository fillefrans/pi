<?

  /**
   *  Pi Object ZMQ class
   *
   *  A ZMQ wrapper for a Pi Object
   *  Has methods to describe, read, and update a Pi Object as a ZMQ object
   *  
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   */



  require_once('pi.object.php');

  require_once('pi.type.zmq.php');

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


  interface ZMQSerializable {
    public function ZMQSerialize ();
  }



  function zmq_encode(ZMQSerializable $object) {
    return $object->ZMQSerialize();
  }






  class PiObjectZMQ extends PiObject implements ZMQSerializable {

    protected   $name         = 'piobject';
    protected   $value        = null;
    protected   $address      = null;
    protected   $channel      = PI_ZMQ;
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



    public function ZMQSerializeObject() {
    // Wire format

    // ØMQ messages are transmitted over TCP in frames consisting of an encoded payload length, 
    // followed by a flags field and the message body. The payload length is defined as the combined 
    // length in octets of the message body and the flags field.

    // For frames with a payload length not exceeding 254 octets, the payload length shall be encoded 
    // as a single octet. The minimum valid payload length of a frame is 1 octet, thus a payload 
    // length of 0 octets is invalid and such frames SHOULD be ignored.

    // For frames with a payload length exceeding 254 octets, the payload length shall be encoded 
    // as a single octet with the value 255 followed by the payload length represented 
    // as a 64-bit unsigned integer in network byte order.

    // The flags field consists of a single octet containing various control flags:

    // Bit 0 (MORE): More message parts to follow. A value of 0 indicates that there are no more 
    // message parts to follow; or that the message being sent is not a multi-part message. 
    // A value of 1 indicates that the message being sent is a multi-part message and more message 
    // parts are to follow.

    // Bits 1-7: Reserved. Bits 1-7 are reserved for future expansion and MUST be set to zero.

    // The following ABNF grammar represents a single frame:

    //     frame           = (length flags data)
    //     length          = OCTET / (escape 8OCTET)
    //     flags           = OCTET
    //     escape          = %xFF
    //     data            = *OCTET
    // The following diagram illustrates the layout of a frame with a payload length not exceeding 254 octets:

    // 0                   1                   2                   3
    // 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1
    // +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
    // | Payload length|     Flags     |       Message body        ... |
    // +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
    // | Message body ...
    // +-+-+-+-+-+-+- ...
    // The following diagram illustrates the layout of a frame with a payload length exceeding 254 octets:

    // 0                   1                   2                   3
    // 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1
    // +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
    // |     0xff      |               Payload length              ... |
    // +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
    // |                       Payload length                      ... |
    // +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
    // | Payload length|     Flags     |        Message body       ... |
    // +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
    // |  Message body ...
    // +-+-+-+-+-+-+-+ ...


      // return the ZMQ definition of this type/object

      // Object is defined as having count(members) > 0

      // $result = "CREATE TABLE pidata." . $this->name . " (";

      // foreach ($this->members as $name => $property) {
      //   // print("calling : ZMQSerialize()\n");
      //   $serialized = $this->ZMQSerialize($property);
      //   $result .= "\n\t$name \t$serialized,";
      // }

      // // remove final comma, add closing parenthesis

      // $result = rtrim($result, ",") . "\n\t) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;\n\n";

      // $colors = new Colors();

      // $result = $colors->getColoredString($result);



      return $result;
      // return rtrim($result, ",") . "\n\t) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;\n\n";

      // $result = rtrim($result, ",") . "\n);"
      // return $result;
    }



    public function ZMQSerialize($type = null) {
      $result = "";
      // return the ZMQ definition of this type/object
      // 
      // echo "ZMQSerialize : " . gettype($type) . "\n";

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
        // print("type is PiType in ZMQSerialize()\n");
        $value = $type;
        $this->SIZE = $type->SIZE;
        $type = $type->TYPE;
      }
      switch ($type) {

        case PI_STRUCT  : 
          // print("calling : ZMQSerializeObject()\n");
          return $this->ZMQSerializeObject();
          break; // ?

        // case PI_UINT8     : $result .= ZMQ_UINT8;   break;
        // case PI_UINT16    : $result .= ZMQ_UINT16;  break;
        // case PI_UINT32    : $result .= ZMQ_UINT32;  break;
        // case PI_UINT64    : $result .= ZMQ_UINT64;  break;

        // case PI_INT8     : $result .= ZMQ_INT8;   break;
        // case PI_INT16    : $result .= ZMQ_INT16;  break;
        // case PI_INT32    : $result .= ZMQ_INT32;  break;
        // case PI_INT64    : $result .= ZMQ_INT64;  break;


        case PI_STR     :
          $result .= "VARCHAR";
          if ($this->SIZE > 1 && $this->SIZE <= 255) {
            $result .= "({$this->SIZE})";
          }
          break;


    case PI_STR : $result .= ZMQ_STR; echo "sz:" . $this->SIZE; break;
    case PI_STRING : $result .= ZMQ_STRING; break;
    case PI_NUMBER : $result .= ZMQ_NUMBER; break;


    // floating point types
    case PI_FLOAT32 : $result .= ZMQ_FLOAT32; break;
    case PI_FLOAT64 : $result .= ZMQ_FLOAT64; break;


    // basic integer types

    // unsigned
    case PI_UINT8 : $result .= ZMQ_UINT8; break;
    case PI_UINT16 : $result .= ZMQ_UINT16; break;
    case PI_UINT32 : $result .= ZMQ_UINT32; break;
    case PI_UINT64 : $result .= ZMQ_UINT64; break;


    // signed
    case PI_INT8 : $result .= ZMQ_INT8; break;
    case PI_INT16 : $result .= ZMQ_INT16; break;
    case PI_INT32 : $result .= ZMQ_INT32; break;
    case PI_INT64 : $result .= ZMQ_INT64; break;


    // typed arrays, unsigned
    // case PI_UINT8ARRAY : $result .= ZMQ_UINT8ARRAY; break;
    // case PI_UINT16ARRAY : $result .= ZMQ_UINT16ARRAY; break;
    // case PI_UINT32ARRAY : $result .= ZMQ_UINT32ARRAY; break;
    // case PI_UINT64ARRAY : $result .= ZMQ_UINT64ARRAY; break;

    // typed arrays, signed
    // case PI_INT8ARRAY : $result .= ZMQ_INT8ARRAY; break;
    // case PI_INT16ARRAY : $result .= ZMQ_INT16ARRAY; break;
    // case PI_INT32ARRAY : $result .= ZMQ_INT32ARRAY; break;
    // case PI_INT64ARRAY : $result .= ZMQ_INT64ARRAY; break;


    // typed arrays, floating point values
    // case PI_FLOAT32ARRAY : $result .= ZMQ_FLOAT32ARRAY; break;
    // case PI_FLOAT64ARRAY : $result .= ZMQ_FLOAT64ARRAY; break;



    // complex types
    case PI_RANGE : $result .= ZMQ_RANGE; break;
    case PI_ARRAY : $result .= ZMQ_ARRAY; break;
    case PI_BYTEARRAY : $result .= ZMQ_BYTEARRAY; break;

    // synonyms
    case PI_STRUCT : $result .= ZMQ_STRUCT; break;
    case PI_RECORD : $result .= ZMQ_RECORD; break;



    // higher order types
    case PI_FILE : $result .= ZMQ_FILE; break;
    case PI_IMAGE : $result .= ZMQ_IMAGE; break;
    case PI_DATA : $result .= ZMQ_DATA; break;
    case PI_TEL : $result .= ZMQ_TEL; break;
    case PI_GEO : $result .= ZMQ_GEO; break;
    case PI_EMAIL : $result .= ZMQ_EMAIL; break;
    case PI_URL : $result .= ZMQ_URL; break;



      // Pi internal types

      case PI_FORMAT : $result .= ZMQ_FORMAT; break;
      case PI_CHANNEL : $result .= ZMQ_CHANNEL; break;
      case PI_ADDRESS : $result .= ZMQ_ADDRESS; break;

      case PI_IGBINARY : $result .= ZMQ_IGBINARY; break;
      case PI_BASE64 : $result .= ZMQ_BASE64; break;


      // common internal object types
      case PI_USER : $result .= ZMQ_USER; break;
      case PI_USERGROUP : $result .= ZMQ_USERGROUP; break;
      case PI_PERMISSIONS : $result .= ZMQ_PERMISSIONS; break;

      case PI_TOKEN : $result .= ZMQ_TOKEN; break;
      case PI_JSON : $result .= ZMQ_JSON; break;
      case PI_ZMQ : $result .= ZMQ_ZMQ; break;
      case PI_REDIS : $result .= ZMQ_REDIS; break;
      case PI_LIST : $result .= ZMQ_LIST; break;


      // a UINT32
      case PI_IP : $result .= ZMQ_IP; break;
      case PI_IPV4 : $result .= ZMQ_IPV4; break;

      // a UINT32 QUAD ?
      case PI_IPV6 : $result .= ZMQ_IPV6; break;


      // PASCAL string, ZeroMQ-compatible fixed-length binary string
      case PI_SHORTSTRING : $result .= ZMQ_SHORTSTRING; break;

      // ANSI string, C-compatible null-terminated binary string
      case PI_ANSISTRING : $result .= ZMQ_ANSISTRING; break;

      // UTF-8 string
      case PI_UTF8 : $result .= ZMQ_UTF8; break;



    // date and time related types
    case PI_DAY : $result .= ZMQ_DAY; break;
    case PI_WEEK : $result .= ZMQ_WEEK; break;
    case PI_TIME : $result .= ZMQ_TIME; break;
    case PI_DATE : $result .= ZMQ_DATE; break;

    case PI_DATETIME : $result .= ZMQ_DATETIME; break;
    case PI_DATETIME_LOCAL : $result .= ZMQ_DATETIME_LOCAL; break;

    case PI_TIMESTAMP : $result .= ZMQ_TIMESTAMP; break;
    case PI_DATE_UTC : $result .= ZMQ_DATE_UTC; break;



    case PI_HOUR : $result .= ZMQ_HOUR; break;
    case PI_MINUTE : $result .= ZMQ_MINUTE; break;
    case PI_SECOND : $result .= ZMQ_SECOND; break;

    case PI_UNIXTIME : $result .= ZMQ_UNIXTIME; break;
    case PI_MILLITIME : $result .= ZMQ_MILLITIME; break;
    case PI_MICROTIME : $result .= ZMQ_MICROTIME; break;





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
      // print "returning $result" . ($result ? "" : " --> [ZMQSerializer ERROR : The object is empty.]");

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