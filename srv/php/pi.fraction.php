<?




  // activate debugging
  if (!defined('DEBUG')) {
    define('DEBUG', true);
  }


  /**
   *  Pi Fraction class
   *
   *  Defines a Pi Fraction
   *  a union of a scalar and a fractional representation of a number
   *
   * where the number has equal capacity in the whole part as in the fractional part
   * and where the 
   *
   * is has a scalar value along the outside of the circle,
   * and a fractional value inside the circle
   *
   * Uses primes, and prime fractions of primes to keep numbers in sequence at
   * two different orders (or levels) of magnitude simultaneously
   *
   * i.e. to know which event occurs before and after where
   * events (or their results) from different orders of magnitude meet
   *
   * @category pi
   * @package core
   *
   * @copyright 2011-2014 Views AS
   *
   * @author 2011-2014 Johan Telstad <jt@viewshq.no>
   * @since 28.08.2014 22:12
   * 
   */


  require_once ('pi.type.constants.primes.first10000.php');


  class PiFraction {

    protected   $name  = 'fraction';

    private $prime = PRIMES(); // returns a reference to the PRIMES array

    $numerator    = 1;
    $denominator  = 2;

    $ORDER = null;


    public function __construct($numerator = null, $denominator = null, $ORDER = 1) {
      $this->numerator    = $numerator;
      $this->denominator  = $denominator;
      $this->ORDER        = $ORDER;
    }

    public function tick () {
      $this->numerator++;
      $this->denominator++;
    }

    public function outside() {
      // the distance outside is the circumference of a circle
      return 2 * M_PI * ($this->prime[$this->numerator] * $this->prime[$this->numerator]);
    }

    public function inside() {
      // the distance inside is 1
      return new PiFraction($this->numerator, $this->denominator, $this->ORDER+1);
    }

  }


?>