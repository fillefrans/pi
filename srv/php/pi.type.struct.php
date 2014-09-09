<?

  /**
   *  Pi Type Struct class
   *
   *
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   */



  require_once('pi.type.php');
  require_once('pi.cache.php');


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
    protected $length = null;
    protected $value  = array('struct' => array('definition' => array(), 'items' => array()));

    // protected $definition  = $value['struct']['definition'];


    // for et rot

    public function __construct($value = null, $length = null) {
      if (is_string($value)) {
        // echo "value is a STRING : $value\n";
      }

      if (is_int($length)) {
        // echo "setting SIZE to : $length\n";
        // $this->SIZE = $length;
      }

      // call PiType class constructor (pass along type)
      parent::__construct($this->TYPE);
      // echo "type is now  : {$this->TYPE}\n";

    }



    /*    PROPERTY OVERLOADING   */

    // Provides property overloading to subclasses

    /**
     * property setter
     * @param string $name  Property name
     * @param PiType $value Property value
     */
    public function __set($name, $value = null){
      if ($value === null) {
        echo "Setting property value to '$name'\n";
        $this->value = $name;
      }
      elseif ($name && is_string($name)) {
        
        echo "Setting overloaded property '$name' to ".gettype($value)."($value)\n";
        $this->members[$name] = $value;
        $this->length = count($this->members);
      }
      else {
        throw new InvalidArgumentException("wrong type", 1);
        
      }
    }

    /**
     * property getter
     * @param  string $name Property name
     * @return PiType       The property value
     */
    public function __get($name){
      if (!isset($this->members[$name])) {
        print("reading non-existing property '$name'\n");
      }
      echo "Overloaded Property '$name' = " . $this->members[$name] . "\n";
      // var_dump($this->members);
      return $this->members[$name];
    }

    public function __isset($name){
      if(isset($this->members[$name])){
        echo "Property \$$name is set.\n";   
      } else {
        echo "Property \$$name is not set.\n";
      }
      return isset($this->members[$name]) && isset($this->value);
    }

    public function __unset($name){
      unset($this->members[$name]);
      $this->length = count($this->members);
      echo "\$$name is unset\n";
    }


    // posix_getgroups(oid)


    public function add ($name = null, PiType $type, $size = null) {
      if(!$name) {
        return false;
      }
      if (isset($this->members[$name])) {
        throw new InvalidArgumentException("Property name already exists in add(\"{$type->name}\")", 1);
      }

      if (is_int($size)) {
        $type->SIZE = $size;
      }

      $this->members[$name] = $type;
      $this->length = count($this->members);

      // return new length of members array
      return $this->length;
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