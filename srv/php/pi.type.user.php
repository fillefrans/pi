<?

  /**
   *  Pi User class
   *
   * handles everything user related
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.struct.php');


  // class PiTypeStructMember extends StdClass {
    
  //   protected $name   = null;
  //   protected $value  = null;

  //   public function __construct($name = null, PiType $value = null) {
  //     if (!is_string($name)) {
  //       throw new InvalidArgumentException("Expected name to be String, received : " . gettype($name), 1);
  //     }

  //     if (!$value instanceof PiType) {
  //       throw new InvalidArgumentException("Expected value to be PiType, received : " . get_class($value), 1);
  //     }

  //     $this->name   = $name;
  //     $this->value  = $value;
  //   }

  // }


  class PiUser extends PiTypeStruct {

    protected $name = 'user';
    protected $TYPE = PiType::USER;

    protected $groups = array();



    /**
     * Creates a new user object
     * @param int|string  $value  Id or username to load
     * @param int         $length Size of new object
     */
    public function __construct($value = null, $length = null) {
      
      if (is_int($value)) {
        $this->loadFromInt($value);
      }
      if (is_string($value)) {
        $this->loadFromString($value);
      }

      // call PiTypeStruct class constructor (pass along arguments)
      // echo "calling PiTypeStruct constructor($value, $length)\n";
      parent::__construct($value, $length);
      // echo "type is now  : {$this->TYPE}\n";

    }


    /**
     * Load user by id
     * @param  integer  $id   User id
     * @return bool           Success or failure of load operation
     */
    public function loadFromInt ($id = -1) {

    }

    /**
     * Load user by username
     * @param  string   $name   User name
     * @return bool             Success or failure of load operation
     */
    public function loadFromString ($name = "") {

    }


    /**
     * Load user
     * @param  string|int $user User to load
     * @return bool       Success or failure of load operation
     */
    public function load($user) {
      if(is_int($user)) {
        return $this->loadFromInt($user);
      }
      elseif(is_numeric($user)) {
        return $this->loadFromInt((int) $user);
      }
      elseif(is_string($user)) {
        return $this->loadFromString($user);
      }
      else {
        return false;
      }
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