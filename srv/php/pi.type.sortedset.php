<?

  /**
   *  Pi Type Sorted Set class
   *
   *  Defines and implements Sorted Sets for the Pi namespace.
   *
   *  A Pi Sorted Set is equivalent to a sparse array
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');



  class PiTypeSortedSetException extends PiException {};



  /**
   * Rudimentary Sorted Set implementation.
   * We can probably do much better, but later
   */
  class PiTypeSortedSet extends PiType {

    protected $name     = 'sortedset';

    // Protected, can be accessed by descendants
    protected $value    = null;
    protected $score    = null;

    protected $names    = array();
    protected $values   = array();


    protected $TYPE     = PiType::SORTEDSET;
    protected $DEFAULT  = null;


    protected $SIGNED   = false;

    protected $UNIQUE   = true;
    protected $INDEX    = true;

    // NOTNULL == required
    // i.e., member value cannot be NULL
    protected $NOTNULL  = false;

    // not a constant size
    protected $SIZE     = null;



    public function __construct($value = null) {
      // call PiType class constructor (remember to pass arguments)
      parent::__construct($value);
    }


    private function updateSize() {
      $this->SIZE = count($this->names);
      // assert values and names arrays have matching lengths
      if (count($this->values) != $this->SIZE) {
        throw new PiTypeSortedSetException("[ERROR] Types and Names arrays have different lengths", 1);
      }
    }



    /**
     * Sets the name by value
     * @param  {String} $name   The type name as string
     * @param  {int}    $value  The type as id
     * 
     * @return void
     * 
     * @throws InvalidArgumentException If any of the required params are not given, or have the wrong type
     */
    public function setname($name = null, $value = null) {

      // both values are set
      if($name && ($value !== null)) {
        if (!is_string($name)) {
          throw new InvalidArgumentException("expected name to be String, received : " . gettype($name), 1);

        }
        if (!is_int($value)) {
          throw new InvalidArgumentException("expected type to be Int, received : " . gettype($value), 1);
        }

      }
      else {
        throw new InvalidArgumentException("setname(name, type) : both parameters required", 1);
      }


      // always update both indexes
      $this->names[$value] = $name;
      $this->values[$name] = $value;
      $this->updateSize();

    }


    /**
     * Sets the value by name
     * @param  {int}    $value  The type as id
     * @param  {String} $name   The type name as string
     * 
     * @return void
     * 
     * @throws InvalidArgumentException If any of the required params are not given, or have the wrong type
     */
    public function setvalue($name = null, $value = null) {
      // name is non-empty string, and value is non-null
      if($name && ($value !== null)) {
        if (!is_string($name)) {
          throw new InvalidArgumentException("expected name to be String, received : " . gettype($name), 1);

        }
        if (!is_int($value)) {
          throw new InvalidArgumentException("expected type to be Int, received : " . gettype($value), 1);
        }

      }
      else {
        throw new InvalidArgumentException("setname(name, type) : both parameters required", 1);
      }


      // always update both indexes
      $this->names[$value] = $name;
      $this->values[$name] = $value;

      // update SIZE
      $this->updateSize();
    }



    /**
     * Returns the type as int
     * @param  String $name The name of the type, i.e. 'UINT8', 'TEL', 'USER', etc
     * 
     * @return int          The type as int, i.e. PI_UINT8, PI_TEL, PI_USER, etc
     */
    public function getname($name = null) {
      return $this->values[$name];
    }

    /**
     * Returns the type as string
     * @param  int      $value   The type as int, i.e. PI_UINT8, PI_TEL, PI_USER, etc
     * 
     * @return String           The type as string, i.e. 'UINT8', 'TEL', 'USER', etc
     */
    public function gettype($value = null) {
      return $this->names[$value];
    }


    /**
     * Gets corresponding string or int
     * @param  {String|int} $which The type id or name to return
     * 
     * @return {int|String}        If given id, returns the name; if given name, returns the id;
     * 
     * @throws InvalidArgumentException If required param which is not given, or is the wrong type
     */
    public function get($which = null) {
      if (is_int($which)) {
        return $this->getname($which)
      }
      elseif (is_string($which)) {
        return $this->gettype($which)
      }
      else {
        throw new InvalidArgumentException("arg1 must be a non-null String or int", 1);
      }
    }


    /**
     * Gets all types, sorted by id or by name
     * @param  {bool}   $byid   Sort results by id (sorting by name is the default)
     * 
     * @return {Array}  If byid is TRUE, returns integer-indexed Array of Strings
     *                  If byid is FALSE, returns assoc Array of integers
     * @throws InvalidArgumentException If optional param byid is not a bool
     */
    public function all($byid = false) {
      if (!is_bool($byid)) {
        throw new InvalidArgumentException("Expectd param byid to be a bool, received : " . gettype($byid), 1);
      }
      elseif ($byid === true) {
        return $this->names;
      }
      else {
        return $this->values;
      }
    }




  }



?>