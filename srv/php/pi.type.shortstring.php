<?

  /**
   *  Pi Type String class
   *
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');
  // require_once('pi.db.php');


  class PiTypeShortstring extends PiType {

    private   $name = 'shortstring';
    protected $type = PiType::SHORTSTRING;

    protected $length     = null;
    protected $maxlength  = null;

    public function __construct($maxlength = 255, $value = '') {
      if (!is_string($value)) {
        throw new InvalidArgumentException("Expected value to be String, received : " . gettype($value), 1);
      }

      if (!is_int($maxlength)) {
        throw new InvalidArgumentException("Expected maxlength to be Int, received : " . gettype($value), 1);
      }

      $this->maxlength = $maxlength;

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
      $valuelength = strlen($value);
      if ($valuelength > $this->maxlength) {
        throw new InvalidArgumentException("String length ({$valuelength}) exceeds maxlength ({$this->maxlength})", 1);
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