<?

  /**
   *  Pi Channel DB class
   *
   *  Defines and implements basic DB Channel for the Pi namespace.
   *  The available types are defined in the Pi Type Library 
   *  A typed channel can be thought of as a stream
   *
   * It has the following properties:
   * - type (MySQL, Postgresql, etc)
   * - host
   * - db
   * - id (PIC_AUTH, PIC_DB, etc)
   * - type (optional) Pi Type
   * - ttl
   *
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   */



  require_once('pi.channel.php');


    // // channels
    // define('PIC_AUTH',     1);
    // define('PIC_CHAT',     2);
    // define('PIC_DEBUG',    3);
    // define('PIC_WARNING',  4);
    // define('PIC_ERROR',    5);
    // define('PIC_LOG',      6);
    // define('PIC_TYPE',     7);
    // define('PIC_DB',       8);
    // define('PIC_PING',     9);
    // define('PIC_CTRL',     10);
    // define('PIC_ADMIN',    11);
    // define('PIC_SYS',      12);
    // define('PIC_REGEX',    13);

    // // push channel
    // define('PIC_PUSH',     14);
    // define('PIC_ZMQ',      15);


  class PiDBChannelException extends PiChannelException {};
    

  class PiDBChannel extends PiChannel {

    protected $name     = 'db';
    protected $TYPE     = null;
    protected $ttl      = null;
    protected $id       = PIC_DB;


    /**
     * Generic DB class
     * @param int $id      The Channel id
     * @param PiType $type    The Pi Type of the channel, if any. Default is an untyped channel
     * @param String $address The Pi Address of the Channel
     */
    public function __construct($id = null, PiType $type = null, $address = null) {

      if ($address && !is_string($address)) {
        throw new InvalidArgumentException("Expected address to be String, received : " . gettype($address), 1);
      }

      // call Pi Channel class constructor (pass any arguments)
      parent::__construct();

      $this->address = $address;
      if (is_int($ttl)) {
        $this->$ttl = (int) $ttl;
      }
      if (is_int($type)) {
        $this->$TYPE = (int) ($type & 15);
      }

    }



  }



?>