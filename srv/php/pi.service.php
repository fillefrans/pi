<?

    /**
     *  Pi Service base class
     *
     *  Implements basic functions that other Pi 
     *  services will need.
     *
     *
     * @author 2011-2013 Johan Telstad <jt@enfield.no>
     * 
     */

    require_once('pi.php');





    class PiService extends Pi {

      protected   $subscribercount  = -1;

      private $name = 'service';



      public function __construct() {
      }


    }


?>