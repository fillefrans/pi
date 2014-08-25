<?

  /**
   *  Pi Type Array class
   *
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');
  // require_once('pi.db.php');


  class PiTypeArrayMember extends PiType {
    
    protected $value = null;


  }


  class PiTypeArrayDefinition extends StdClass {
    
    protected $member = null;

    public function __construct($value = null) {
    }

  }

  class PiTypeArray extends PiType {

    private   $name = 'array';
    protected $type = PiType::ARRAY;

    protected $itemtype = null;

    protected $items  = null;
    protected $length = null;

    public function __construct(PiType $itemtype = null, $length = null) {
      if (!$value instanceof PiType) {
        throw new InvalidArgumentException("Expected itemtype to be PiType, received : " . gettype($itemtype), 1);
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

    public function set (PiTypeStructDefinition $value = null) {
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