<?

  /**
   *  Pi Type Struct class
   *
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');


  class PiTypeStructMember extends StdClass {
    
    protected $name   = null;
    protected $value  = null;

    public function __construct($name = null, $value = null) {
      if (!is_string($name)) {
        throw new InvalidArgumentException("Expected name to be String, received : " . gettype($name), 1);
      }

      if (!$value instanceof PiType) {
        throw new InvalidArgumentException("Expected value to be PiType, received : " . get_class($value), 1);
      }

      $this->name   = $name;
      $this->value  = $value;
    }

  }



  class PiTypeStructDefinition extends StdClass {
    
    protected $member = null;

    public function __construct($definition = null) {
    }

    public function parse($definition = null) {

    }

  }


  class PiTypeStruct extends PiType {

    protected $name = 'struct';
    protected $type = PiType::STRUCT;

    protected $member = null;
    protected $length = null;
    protected $value  = array('struct' => array('definition' => array(), 'items' => array()));

    // protected $definition  = $value['struct']['definition'];


    public function __construct($value = null) {
      if (is_string($value)) {

      }

      if (!$value instanceof PiTypeStructDefinition) {
        throw new InvalidArgumentException("Expected value to be PiTypeStructDefinition, received : " . gettype($value), 1);
      }

      $this->set($value);

      // call PiType class constructor (pass along arguments)
      parent::__construct($this->type);
    }


    public function toString () {
      return $this->value;
    }

    public function toJson () {
      return json_encode($this->value);
    }

    public function toSQL () {
      return $this->value;
    }

    public function toAssoc () {
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