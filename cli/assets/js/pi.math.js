
/**
 *  π.util.sets
 *  
 *  Implements js-equivalents of sets, sorted sets, etc
 *  with related functions to operate on them
 *  
 *  @author Johan Telstad, <jt@viewshq.no>, 2011-2014
 * 
 */



  π.require('util');


  if (!π.util) {
    pi.log("pi.util is undefined!");
  }



  π.util.sets = {


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


    run : function (DEBUG) {
      π.timer.start("session");

      if( !this.__init(DEBUG) ) {
        pi.log('session.__init() returned false, aborting...');
      }
    }
  };




  π.util.sets.run();
