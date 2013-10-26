<?

    /**
     *  Pi Channel base class
     *
     *  Implements basic functions that other Pi 
     *  channels will need.
     *
     *
     * @author 2011-2013 Johan Telstad <jt@enfield.no>
     * 
     */

    require_once('pi.php');





    class PiChannel extends Pi {

      protected   $subscribercount  = -1;

      private $name = 'channel';



      public function __construct() {
      }


    }


?>