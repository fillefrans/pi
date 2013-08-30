
/**
 *  π.util.math
 *  
 *  Useful math functions, trig tables, etc
 *  
 * 
 */



  π.require('util');


  if (!π.util) {
    pi.log("pi.util is undefined!");
  }



  π.util.math = {


    // init: calculate trig tables and what have you
    // 
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
        self = π.util.math;

      self.__handleError(error, self);
      pi.log("onerror: " + event.data);
    },


    start : function (DEBUG) {
      π.timer.start("session");

      if( !this.__init(DEBUG) ) {
        pi.log('session.__init() returned false, aborting...');
      }
    }
  };




  π.util.math.start();
