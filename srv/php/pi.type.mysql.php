<?

  /**
   *  Pi Type class
   *
   *  Implements basic Types for the Pi namespace.
   *  ("Popt" = "Plain Old Pi Type")
   *  These types reflect the available data types in HTML5 and Redis
   *  It defines proper aliases for all basic types for JSON, MySQL, PHP, JavaScript
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');
  // require_once('pi.db.php');

    


  class PiTypeMySQL extends PiType {

    private   $name       = 'mysql';
    private   $address    = null;

    private   $channel    = null;

    public function __construct($address, $value=null, $ttl=null) {
      // call PiType class constructor (pass along arguments)
      parent::__construct($address, $value, $ttl);
    }




    /**
     * "Factory" of sorts, to create new instances of PiType descendants
     * @param string $className Class name, e.g. : FileType, ImageType, DataType, etc
     * @param Type $args      Arguments for the class constructor
     */

    public static function New($className, $args) { 
       if(class_exists($className) && is_subclass_of($className, 'PiType'))
       { 
          return new $className($args); 
       } 
    }  




  }







  class PersistentType extends PiType {

    public function __construct($address, $object) {
      parent::__construct($address, $object);
      $this->db = new PiDB();
    }

  }



  class TransientType extends PiType {
    public function __construct($address, $object, $ttl=null) {
      parent::__construct($address, $object, $ttl);
      $this->redis = new Redis();
    }

    public function 
  }



?>