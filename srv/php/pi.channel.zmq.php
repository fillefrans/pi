<?

  /**
   *  Pi Channel ZMQ class
   *
   *  Implements basic Pi Types for ZMQ.
   *  ("Popt" = "Plain Old Pi Type")
   *  
   *  It defines proper aliases for all basic types for ZMQ
   *
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   */



  require_once('pi.channel.php');

  require_once('pi.type.zmq.php');





  /**
   * pid.channel.zmq
   *
   * PiChannel handler for ZMQ
   * Stores, reads, filters, updates PiChannel values in he Pi db
   *
   * @requires ZMQ Php extension (?)
   */
  class PiChannelZMQ extends PiChannel {

    private   $zmq    = null;
    private   $res    = null;

    protected $name       = 'zmq';
    private   $channel    = PIC_ZMQ;


    // CREATE, ADD, ALTER, DROP
    // SELECT, INSERT, UPDATE, DELETE

    /**
     * 
     * @param string  $address Optional adress in Pi namespace
     * @param mixed   $value   Optional initial value
     * @param int     $ttl     Optional Time-to-live in milliseconds
     * @param ZMQ  $zmq  Optional existing ZMQ instance
     */
    public function __construct($address=null, $value=null, $ttl=null, ZMQ $zmq = null) {
      // call PiChannel class constructor (pass along arguments)
      parent::__construct($address, $value, $ttl);
      if ($zmq instanceof ZMQ) {
        $this->zmq = $zmq;
      }
      else {
        $this->zmq = new ZMQ($PI_DB['host'], $PI_DB['user'], $PI_DB['password'], $PI_DB['db'], $PI_DB['port']);
        if (!$this->zmq instanceof ZMQ) {
          return null;
        }
      }

      $this->_init();

    }



    public function read($address = null, $id=null, $type=null, $size = null, $offset = 0) {

    }


    public function write(PiType $piobject = null, $address = null, $id = null, $type = null, $size = null, $offset = 0) {
      $zmqobject = new PiTypeZMQ($piobject);
    }

    public function update(PiType $object = null, $address = null, $id = null, $type = null, $size = null, $offset = 0) {
      
    }

    public function delete(PiType $object = null, $address = null, $id = null, $type = null, $size = null, $offset = 0) {
      
    }


    protected function _init () {
      // initialization code


    }


    /**
     * Query ZMQ database
     * @param  string   $query  SQL query
     * @return boolean          Boolean false on error, Array on success
     */

    public function query ($query = "" ) {
      $this->res = $this->zmq->query($query);
      return $this->res;
    }


    /**
     * Fetch result of previous query
     * @return Array|boolean  Boolean false on error, result from fetch_assoc() on success 
     */

    public function fetch () {
      if (!$this->res) {
        return false;
      }
      return $this->res->fetch_assoc();
    }


  }






?>