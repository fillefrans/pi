<?

  /**
   *  Pi Array class
   *
   *  quick wrapper around PHP array, to provide TYPE support
   *  and serialization
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.TYPE.php');
  // require_once('pi.db.php');

// implements JSONSerializable

  class PiTypeArray extends PiType { 
    protected $name = 'array';
    protected $TYPE = PiType::PIARRAY;

    protected $value      = array();
    protected $itemtype   = null;
    private   $length     = null;
    protected $maxlength  = null;
    protected $MULTIPLE   = true;

    public function __construct($value = null) {
      if (is_int($value)) {
        $this->itemtype = $value & 0xff;
      }
      elseif ($value instanceof PiArrayType) {
        $this->value = $value;
      }
      elseif ($value instanceof PiType) {
        $this->itemtype = $value->TYPE;
        $this->push($value);
      }
      elseif ($value) {
        throw new InvalidArgumentException("value is of unexpected type : " . gettype($value), 1);
      }

      // call PiType class constructor (pass along arguments)
      parent::__construct($this->TYPE);
    }


    // public function JSONSerialize () {
    //   $this->$value['name'] = $this->name;
    //   $this->$value['TYPE'] = $this->TYPE;
    //   return $this->value;
    // }


    public function toString () {
      return $this->value;
    }

    public function get () {
      return $this->value;
    }


    public function push (PiType $value) {
      return array_push($this->items, $value);
    }

    public function pop () {
      return array_pop($this->items);
    }

    public function merge (PiTypeArray $value) {
      $this->items = array_merge($this->items, $value);
    }


    public function set ($value = '') {
      if (!is_string($value)) {
        throw new InvalidArgumentException("Expected value to be String, received " . getTYPE($value), 1);
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