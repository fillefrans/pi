
/**
 *  π.util.sets
 *  
 *  Implements js-equivalents of sets, sorted sets, etc
 *  with related functions to operate on them
 *  
 *  @author Johan Telstad, <jt@enfield.no>, 2011-2013
 * 
 */



  π.require('util');


  if (!π.util) {
    pi.log("pi.util is undefined!");
  }



  π.util.set = {

    __items : {},


    __init : function (DEBUG) {

      if(this.__initialized === true){
        //something is not right
        pi.log("error: __init() called twice ");
        return false;
      }
      
      this.__initialized = true;
      return this.__initialized;
    },


    __handleError  : function(msg, obj){
      pi.log('error: ' + msg, obj);
    },


    __onerror : function (error) {
      var
        self = π.util.sets;

      self.__handleError(error, self);
      pi.log("onerror: " + event.data);
    },


    add : function (set, members) {


    // return number of items in set after operation
    return 
    },


    remove : function (set, members) {

      var
        self    = π.util.set,
        set     = set     || false,
        members = members || false;

      // return number of items in set after operation
      return 
    },


    run : function (DEBUG) {
      π.timer.start("session");

      if( !this.__init(DEBUG) ) {
        pi.log('session.__init() returned false, aborting...');
      }
    }
  };




  π.util.sets.run();
