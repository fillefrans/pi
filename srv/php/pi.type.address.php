<?

  /**
   *  Pi Type Address class
   *
   *
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   */



  require_once('pi.type.php');



  class PiTypeAddress extends PiType {

    protected $name   = 'address';
    protected $TYPE   = PiType::ADDRESS;

    protected $SIZE   = null;

    // bitfield (12), 4 octal numbers
    protected $BITS   = 8;

    protected $value  = null; //default

    protected $channel    = null;
    protected $address    = null;
    protected $channelvar = null;
    protected $host       = null;


    public function __construct($value = null) {

      if ($value && is_string($value)) {
        $this->parse($value);
      }

      // call PiType class constructor (pass along arguments)
      // parent::__construct($this->TYPE, $ttl);
    }



    private function parse ($value) {
      $result = explode("|", $value, 2);
      if (count($result) === 2) {
        $this->address = $result[1];
        if (strpos($result[0],":")) {
          $result = explode(":", $result[0], 2);
          $this->channel    = $result[0];
          $this->channelvar = $result[1];
        }
        else {
          $this->channel    = $result[0];
          $this->channelvar = null;
        }
      }
      elseif (count($result === 1)) {
          $this->address    = $result[0];
          $this->channel    = null;
          $this->channelvar = null;
      }

      if (strpos($this->address, "@")) {
        $result = explode("@", $this->address, 2);
        if (count($result) === 2) {
          $this->host     = $result[1];
          $this->address  = $result[0];
        }
        elseif (count($result) === 1) {
          $this->address = $result[0];
        }
        else {
          return false;
        }
      }
      $this->value = $value;
    }


  }



?>