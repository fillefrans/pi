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

    public function __construct($name = null, PiType $value = null) {
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


  class PiTypeStruct extends PiType {

    protected $name = 'struct';
    protected $TYPE = PiType::STRUCT;

    protected $members = array();
    protected $property = array();
    protected $length = null;
    protected $value  = array('struct' => array('definition' => array(), 'items' => array()));

    // protected $definition  = $value['struct']['definition'];


    // for et rot

    public function __construct($value = null, $length = null) {
      if (is_string($value)) {
        echo "value is a STRING : $value)\n";
      }

      if (is_int($length)) {
        // echo "setting SIZE to : $length\n";
        // $this->SIZE = $length;
      }

      // $this->write($value);

      // call PiType class constructor (pass along arguments)
      // echo "calling parent constructor({$this->TYPE})\n";
      parent::__construct($this->TYPE);
      // call PiType class constructor (pass along arguments)
      // echo "type is now  : {$this->TYPE}\n";

    }



    public function add ($name = null, PiType $type, $size = null) {
      if (isset($this->members[$name])) {
        throw new InvalidArgumentException("Property name already exists in add(\"{$type->name}\")", 1);
      }

      if (is_int($size)) {
        // echo basename(__FILE__) . " : setting SIZE to $size\n";
        $type->SIZE = $size;
      }

      $this->members[$name] = $type;

      // echo basename(__FILE__) . " : SIZE = {$this->members[$name]->SIZE}\n";

      // return new length of members array
      return count($this->members);
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


    public function read () {
      return $this->value;
    }

    public function write (PiTypeStructDefinition $value = null) {
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