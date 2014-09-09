<?

  /**
   *  Pi Type Int class
   *
   *
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   */



  require_once('pi.type.php');


  class PiTypeInt extends PiType {

    protected $name = 'int';
    protected $type = null;


    public function __construct($size = 64, $ttl = null) {
      if (!$size) {
        throw new InvalidArgumentException("Wrong size : $size", 1);
      }

      // internal size, in bits
      switch ($size) {
        case 64 :
          $this->type = PiType::INT64;
          break;
        case 8 :
          $this->type = PiType::INT8;
          break;
        case 16 :
          $this->type = PiType::INT16;
          break;
        case 32 :
          $this->type = PiType::INT32;
          break;
        
        default:
          $this->type = PiType::INT64;
          break;
      }

      // call PiType class constructor (pass along arguments)
      parent::__construct($type, $ttl);
    }


    public function toString () {
      return (sprintf("%d",$this->value));
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
      return (int) $previous;
    }

  }



?>