<?

  /**
   *  Pi Type ShortString class
   *  Pascal-style string, up to 255 chars
   *
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   */



  require_once('pi.type.php');


  class PiTypeShortstring extends PiType {

    protected $name = 'shortstring';
    protected $type = PiType::SHORTSTRING;

    protected $length     = null;
    protected $maxlength  = null;

    /**
     * Pi Type ShortString constructor
     * 
     * @param string  $value     [description]
     * @param integer $maxlength [description]
     *
     * @throws RangeException If maxlength is outside of range 0-255
     * @throws InvalidArgumentException   If not called by on of the following signatures:
     *                                    (int length), (string value), (string value, int maxlength[0-255])
     * @throws InvalidArgumentException   If 
     */
    public function __construct($value = '', $maxlength = 255) {
      if (!is_string($value)) {
        if (is_int($value)) {
          if ($value > 255 || $value < 0) {
            throw new RangeException("Expected value to be String, received : " . gettype($value), 1);
          }
        }
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