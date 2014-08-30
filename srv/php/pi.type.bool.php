<?

  /**
   *  Pi Type Bool class
   *
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');


  class PiTypeBool extends PiType {

    protected $name   = 'bool';
    protected $value  = false;
    protected $TYPE   = PiType::BOOL;
    protected $SIZE   = 1;


    public function __construct($value = false, $ttl = null) {

      if ($size && is_int($size)) {
        $this->SIZE = $size;
      }

      if (is_bool($value)) {
        $this->value = $value;
      }

      // call PiType class constructor (pass along arguments)
      parent::__construct($this->TYPE, $ttl);
    }


    public function toString () {
      return sprintf("%b", $this->value);
    }

    public function get () {
      return (bool) $this->value;
    }

    public function set ($value = false) {
      $this->value = (bool) $value;
    }

    public function getset ($value = null) {
      $previous = $this->value;
      $this->value = (bool) $value;
      return (bool) $previous;
    }

  }



?>