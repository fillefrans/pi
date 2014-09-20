<?

  /**
   *  Pi Channel DB MySQL class
   *
   *  Defines and implements basic MySQL DB Channel for the Pi namespace.
   *  The available types are defined in the Pi Type Library 
   *  A typed channel can be thought of as a stream
   *  Filters can be thought of as queries
   *
   * It has the following properties:
   * - host
   * - db
   * - id (PIC_AUTH, PIC_DB, etc)
   * - type (optional) Pi Type
   * - ttl
   *
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   */



  require_once('pi.channel.db.php');

  require_once('pi.type.mysql.php');


  class PiDBChannelMySQLException extends PiDBChannelException {};
    

  class PiDBChannelMySQL extends PiDBChannel {

    protected $name     = 'mysql';
    protected $TYPE     = null;
    protected $ttl      = null;
    protected $id       = PIC_DB;


    /**
     *  Pi internal types (built-in types)
     *
     * - File
     * - Image
     * - Url
     * - Email
     * - User
     * - Group
     * - Client
     * - Permissions
     * - Address (?)
     *
     */



    /**
     * MySQL DB class
     * @param int     $id      The Channel id
     * @param PiType  $type    The Pi Type of the channel, if any. Default is an untyped channel
     * @param String  $address The Pi Address of the Channel
     */
    public function __construct($id = null, PiType $type = null, $address = null) {

      if ($address && !is_string($address)) {
        throw new InvalidArgumentException("Expected address to be String, received : " . gettype($address), 1);
      }

      // call Pi Channel class constructor (pass arguments)
      parent::__construct($id, $type, $address);

      $this->address = $address;
      if (is_int($ttl)) {
        $this->$ttl = (int) $ttl;
      }
      if (is_int($type)) {
        $this->$TYPE = (int) ($type & 15);
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


  }



?>