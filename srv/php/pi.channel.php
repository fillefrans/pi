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
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   */



  require_once('pi.type.address.php');


    // channels
    define('PIC_AUTH',     1);
    define('PIC_CHAT',     2);
    define('PIC_DEBUG',    3);
    define('PIC_FILE',     4);
    define('PIC_ERROR',    5);
    define('PIC_LOG',      6);
    define('PIC_TYPE',     7);
    define('PIC_DB',       8);
    define('PIC_PING',     9);
    define('PIC_CTRL',     10);
    define('PIC_ADMIN',    11);
    define('PIC_SYS',      12);

    // push channel
    define('PIC_PUSH',     14);
    define('PIC_ZMQ',      15);




  class PiChannelException extends PiException {};


  class PiChannel extends Pi implements JSONSerializable {

    protected $name     = 'channel';
    private   $value    = null;
    protected $address  = null;
    protected $TYPE     = null;
    protected $ttl      = null;
    protected $id       = null;


    // channels
    const AUTH    = PIC_AUTH;
    const CHAT    = PIC_CHAT;
    const DEBUG   = PIC_DEBUG;
    const FILE    = PIC_FILE;
    const ERROR   = PIC_ERROR;
    const LOG     = PIC_LOG;
    const TYPE    = PIC_TYPE;
    const DB      = PIC_DB;
    const PING    = PIC_PING;
    const CTRL    = PIC_CTRL;
    const ADMIN   = PIC_ADMIN;
    const SYS     = PIC_SYS;

    const PUSH    = PIC_PUSH;
    const ZMQ     = PIC_ZMQ;


    public function __construct($id = null, PiType $type = null, $address = null, $ttl = null) {

      if ($address && !is_string($address)) {
        throw new InvalidArgumentException("Expected address to be String, received : " . gettype($address), 1);
      }

      // call Pi Base class constructor (takes no arguments)
      parent::__construct();

      $this->address = $address;
      if (is_int($ttl)) {
        $this->$ttl = (int) $ttl;
      }
      if (is_int($id)) {
        $this->$id = (int) ($id & 15);
      }

    }



    /**
     * List the addresses in this channel
     * @param  String   $address
     * 
     * @return {Array|NULL|bool}    Array on success, 
     *                              Boolean FALSE on error,
     *                              NULL if not found.
     */
    public function list ($address = null, $filter = null) {
      if ($address === null) {
        
      }
    }


    public function send($data = null, $address = null, PiType $type = null, $ttl = null) {
      if ($data === null) {
        throw new InvalidArgumentException("data cannot be null", 1);
      }
      if ($address === null) {
        // broadcast
        $this->redis->rPush($this->channel . $this->address, $data);
      }
    }


    public function receive($data = null, $address = null, $type = null, $ttl = null) {
      
    }



    /**
     * "Factory" of sorts, to create new instances of PiChannel descendants
     * @param string $className Class name, e.g. : FileType, ImageType, DataType, etc
     * @param Type $args      Arguments for the class constructor
     */

    public static function Create($className, $args) { 
       if(class_exists($className) && is_subclass_of($className, 'PiChannel'))
       { 
          return new $className($args);
       } 
    }

  }



?>