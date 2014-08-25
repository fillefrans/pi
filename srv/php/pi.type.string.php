<?

  /**
   *  Pi Type String class
   *
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');
  // require_once('pi.db.php');


  class PiTypeString extends PiType {

    private   $name = 'string';
    protected $type = PiType::STRING;

    protected $length = null;

    public function __construct($value = '') {
      if (!is_string($value)) {
        throw new InvalidArgumentException("Expected value to be String, received : " . gettype($value), 1);
      }

      $this->set($value);

      // call PiType class constructor (pass along arguments)
      parent::__construct($type);
    }


    public function toString () {
      return $this->value;
    }

    public function get () {
      return $this->value;
    }

    public function set ($value = null) {
      if(is_string($value)) {
        $this->length = strlen($value);
      }
      else {
        $this->length = null;
      }
      $this->value = $value;
    }

    public function getset ($value = null) {
      $previous = $this->value;
      if(is_string($value)) {
        $this->length = strlen($value);
      }
      else {
        $this->length = null;
      }
      $this->value = $value;
      return $previous;
    }

  }



?>