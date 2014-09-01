<?

  /**
   *  Pi Type AnsiString class
   *
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');
  // require_once('pi.db.php');


  class PiTypeAnsistring extends PiType {

    protected $name = 'ansistring';
    protected $type = PiType::ANSISTRING;

    protected $length     = null;
    protected $maxlength  = null;

    public function __construct($value = '') {
      if (!is_string($value)) {
        throw new InvalidArgumentException("Expected value to be AnsiString, received : " . gettype($value), 1);
      }

      $this->set($value);

      // call PiType class constructor (pass along arguments)
      parent::__construct($this->type);
    }


    public function toString () {
      return $this->value;
    }

    public function get () {
      return $this->value;
    }

    public function set ($value = '') {
      if (!is_string($value)) {
        throw new InvalidArgumentException("Expected value to String, received " . gettype($value), 1);
      }
      $this->value = $value;
    }

    public function getset ($value = null) {
      $previous = $this->value;
      $this->value = $value;
      return $previous;
    }

  }



?>