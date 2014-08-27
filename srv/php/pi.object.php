<?

  /**
   *  Pi Object class
   *
   *  Container for a Pi Type, or any number of PiType properties
   *
   *  It has a PiType(s) as value, and has the following properties:
   *  - Address
   *  - type
   *  - Channel
   *  - size
   *  - signed
   *  - notnull (i.e. "required")
   *  - ttl
   *  - created
   *  - updated
   *  - id
   *  - islink
   *  - linkaddress
   *  
   *  Implements basic Object for the Pi namespace.
   *  ("Object" = "Instance of a Pi Type")
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   */



  require_once('pi.type.php');


  /*
    class FileObject extends PiObject
    class ImageObject extends PiObject
    class MovieObject extends PiObject
    class ScriptObject extends PiObject
    class DataObject extends PiObject
    class DBObject extends PiObject

    // class DateObject

   */



  class PiObject extends PiType {

    protected   $name         = 'piobject';
    protected   $TYPE         = PI_OBJECT;
    protected   $SIZE         = 0;
    protected   $address      = null;
    protected   $channel      = null;
    protected   $id           = null;
    protected   $islink       = null;
    protected   $linkaddress  = null;
    protected   $ttl          = null;
    protected   $nonnull      = null;
    protected   $signed       = null;
    protected   $properties   = null;
    protected   $created      = null;
    protected   $updated      = null;



    public function __construct($address=null, $type=null, $ttl = null, $required = null) {
      // call PiType class constructor (pass along arguments)
      parent::__construct($address, $type, $ttl);

      $this->nonnull = (bool) $required;

      // sets channel and address from full address given in constructor
      $this->parseAddress($address);
    }



    public function parseAddress($address = null) {

      try {

        // throw PiTypeException within try block to trigger an InvalidArgumentException

          if (!is_string($address)) {
            throw new PiTypeException("Expected 'address' to be String, " . gettype($address) . " received." , 1);
          }
          if(strpos($address, "|")) {

            if (substr_count($address, "|") > 1) {
              // error: more than on pipe character in raw address
              throw new PiTypeException("Too many pipe characters (" . substr_count($address, "|") . ") in raw address (there can be only one).", 1);
            }

            // rawaddress with channel
            $rawaddress = explode($address, "|", 2);
            if(count($rawaddress)==2) {
              $this->channel = $this->parseChannel($rawaddress[0]);
              $this->address = $this->parseAddress($rawaddress[1]);
              return $this->address;
            }

          }
          // if no "|" found, the default handling of simple address
          elseif (strpos($address, "pi.") === 0) {
            // simple address, no channel
            $this->address  = $address;
            $this->wildcard = (strpos($address, ".*") !== false);

            return $this->address;
          }
          // if address does NOT start with "pi."
          elseif (strpos($address, ".")) {
            // relative address, assume prefix "pi.app."
          }
          else {
            throw new PiTypeException("Invalid address", 1);
            
          }

      }
      catch (PiTypeException $e) {
          throw new InvalidArgumentException($e->getMessage(), 1);
          return false;
      }
    }



    public static function parseChannel($channel = null) {
      try {

  
        if(strpos($channel, "|")) {
          throw new PiTypeException("Invalid channel: cannot contain pipe character", 1);
        }
  
        elseif (strpos($channel, ":")) {
          $rawchannel = explode($channel, ":", 2);
          $this->channel  = trim($channel[0]);
          if(is_numeric(trim($channel[1]))) {
            // numeric id attached to channel (i.e. a session id or similar)
            $this->id  = intval(trim($channel[1]), 10);
          }
          else {
            // alphanumeric id attached to channel (i.e. a session id or similar)
            $this->id  = trim($channel[1]);
          }
        }

        else {
            // no value attached to channel
          $this->channel = trim($channel);
          
        }

        return $this->channel;

      }
      catch (PiTypeException $e) {
        throw new InvalidArgumentException($e->getMessage(), 1);
        return false;
      }
    }






    /*
      $object = PiObject::New('PiObject', $args);
      $file   = PiObject::New('FileObject', $args);
      $image  = PiObject::New('ImageObject', $args);
      etc, etc
     */

    /**
     * "Factory" of sorts, to create new instances of PiObject descendants
     * @param string $className Class name, e.g. : FileObject, ImageObject, DataObject, etc
     * @param Object $args      Arguments for the class constructor
     */

    public static function New($className, $args) { 
       if(class_exists($className) && is_subclass_of($className, 'PiObject'))
       { 
          return new $className($args); 
       } 
    }  



  }







  class PersistentObject extends PiObject {

    public function __construct($address, $object) {
      parent::__construct($address, $object);
      $this->db = new PiDB();
    }

  }



  class TransientObject extends PiObject {
    public function __construct($address, $object, $ttl=null) {
      parent::__construct($address, $object, $ttl);
      $this->redis = new Redis();
    }

  }



?>