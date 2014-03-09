<?

    namespace Pi;

    /**
     *  Pi Service base class
     *
     *  Implements basic functions that other Pi 
     *  services will need.
     *
     *
     * @author 2011-2014 Johan Telstad <jt@enfield.no>
     * 
     */

    // require_once('pi.php');

    use Pi;


    class Service extends Pi {

      private $name = 'service';

      public function __construct() {
      }


    }


?>