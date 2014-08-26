<?

  /**
   *  Pi Channel class
   *
   *  Defines and implements basic (typed and untyped) Channels for the Pi namespace.
   *  The available types are defined in the Pi Type Library 
   *  A typed channel can be thought of as a stream
   *
   * It has the following properties:
   * - name (null, '*', or 'zmq', 'auth', etc)
   * - id (PIC_AUTH, PIC_DB, etc)
   * - address (optional) Pi Address
   * - type (optional) Pi Type
   * - ttl
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');


    // channels
    define('PIC_AUTH',     1);
    define('PIC_CHAT',     2);
    define('PIC_DEBUG',    3);
    define('PIC_WARNING',  4);
    define('PIC_ERROR',    5);
    define('PIC_LOG',      6);
    define('PIC_TYPE',     7);
    define('PIC_DB',       8);
    define('PIC_PING',     9);
    define('PIC_CTRL',     10);
    define('PIC_ADMIN',    11);
    define('PIC_SYS',      12);
    define('PIC_REGEX',    13);

    // push channel
    define('PIC_PUSH',     14);
    define('PIC_ZMQ',      15);




  class PiChannelException extends PiException {};
    


  class PiChannel {

    protected $name     = 'channel';
    private   $value    = null;
    protected $type     = null;
    protected $ttl      = null;


    // channels
    const AUTH    = PIC_AUTH;
    const CHAT    = PIC_CHAT;
    const DEBUG   = PIC_DEBUG;
    const WARNING = PIC_WARNING;
    const ERROR   = PIC_ERROR;
    const LOG     = PIC_LOG;
    const TYPE    = PIC_TYPE;
    const DB      = PIC_DB;
    const PING    = PIC_PING;
    const CTRL    = PIC_CTRL;
    const ADMIN   = PIC_ADMIN;
    const SYS     = PIC_SYS;
    const REGEX   = PIC_REGEX;

    const PUSH    = PIC_PUSH;
    const ZMQ     = PIC_ZMQ;


    public function __construct($address = null, $type = null, $ttl = null) {

      if ($address === null) {
        throw new InvalidArgumentException("Invalid address : null", 1);
      }
      if (!is_string($address)) {
        throw new InvalidArgumentException("Expected address to be String, received : " . gettype($address), 1);
      }

      // call Pi Base class constructor (takes no arguments)
      parent::__construct();

      if (is_int($ttl)) {
        $this->$ttl = (int) $ttl;
      }

      if ($type === null) {
        // no particular type
        return;
      }

      switch ($type) {
        case PI_UINT8;
        case PI_UINT16;
        case PI_UINT32;
        case PI_UINT64;

          break;
        
        default:
          // object
          break;
      }

    }







    /*
      $object = PiChannel::New('Object', $args);
      $file   = PiChannel::New('File', $args);
      $image  = PiChannel::New('Image', $args);
      etc, etc
     */

    /**
     * "Factory" of sorts, to create new instances of PiChannel descendants
     * @param string $className Class name, e.g. : FileType, ImageType, DataType, etc
     * @param Type $args      Arguments for the class constructor
     */

    public static function New($className, $args) { 
       if(class_exists($className) && is_subclass_of($className, 'PiChannel'))
       { 
          return new $className($args);
       } 
    }  


  }



?>