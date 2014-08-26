<?

  /**
   *  Pi Type Uint class
   *
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');


  class PiTypeUint extends PiType {

    protected $name = 'uint';
    protected $type = null;


    public function __construct($size = 64, $ttl = null) {
      if (!$size) {
        throw new InvalidArgumentException("Wrong size : $size", 1);
      }

      // internal size, in bits
      switch ($size) {
        case 64 :
          $this->type = PiType::UINT64;
          break;
        case 8 :
          $this->type = PiType::UINT8;
          break;
        case 16 :
          $this->type = PiType::UINT16;
          break;
        case 32 :
          $this->type = PiType::UINT32;
          break;
        
        default:
          $this->type = PiType::UINT64;
          break;
      }

      // call PiType class constructor (pass along arguments)
      parent::__construct($type, $ttl);
    }


    public function toString () {
      return (sprintf("%u",$this->value));
    }

    public function get () {
      return (int) $this->value;
    }

    // public function set ($value = null) {
    //   $this->value = $value;
    // }

    public function getset ($value = null) {
      $previous = $this->value;
      $this->value = $value;
      return (sprintf("%u",$this->value));
    }

  }



?>