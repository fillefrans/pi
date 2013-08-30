
/**
 *  π.util.math
 *  
 *  Useful math functions, trig tables, etc.
 *  
 * 
 */



  π.require('util.math');


  if (!π.util.math) {
    pi.log("pi.util.math is undefined!");
  }





  π.util.math.trig = {

    __tables : {

      cfg: {
        count : 360
      },

      cos : [],
      sin : [],
      tan : [],
      acos : [],
      asin : [],
      atan : [],

      generate : function(number) {
        var
          tables  = π.util.math.trig.__tables,
          pi_per_degree = Math.PI/180;        

        number = number || tables.count;

        for (var i = 0; i < number; i++) {
          tables.cos.push(Math.cos(i * pi_per_degree));            
          tables.sin.push(Math.sin(i * pi_per_degree));            
          tables.tan.push(Math.tan(i * pi_per_degree));            
          tables.acos.push(Math.acos(i * pi_per_degree));            
          tables.asin.push(Math.asin(i * pi_per_degree));            
          tables.atan.push(Math.atan(i * pi_per_degree));
        }
      }
    },


    cos : function (degrees) {
      var
        lookupTables = π.util.math.trig.__tables;
      return lookupTables.cos[Math.round(degrees) % lookupTables.count];
    },

    sin : function (degrees) {
      var
        lookupTables = π.util.math.trig.__tables;
      return lookupTables.sin[Math.round(degrees) % lookupTables.count];
    },

    tan : function (degrees) {
      var
        lookupTables = π.util.math.trig.__tables;
      return lookupTables.tan[Math.round(degrees) % lookupTables.count];
    },

    acos : function (degrees) {
      var
        lookupTables = π.util.math.trig.__tables;
      return lookupTables.acos[Math.round(degrees) % lookupTables.count];
    },

    asin : function (degrees) {
      var
        lookupTables = π.util.math.trig.__tables;
      return lookupTables.asin[Math.round(degrees) % lookupTables.count];
    },

    atan : function (degrees) {
      var
        lookupTables = π.util.math.trig.__tables;
      return lookupTables.atan[Math.round(degrees) % lookupTables.count];
    },


    __init : function (DEBUG) {

      var
        self = π.util.math.trig,


      if(this.__initialized === true){
        //something is not right
        pi.log("error: __init() called twice ");
        return false;
      }

      // generate trig tables
      self.__tables.generate(),
      
      this.__initialized = true;
      return this.__initialized;
    },


    __handleError  : function(msg, obj){
      pi.log('error: ' + msg, obj);
    },


    __onerror : function (error) {
      var
        self = π.util.math.trig;

      self.__handleError(error, self);
      pi.log("onerror: " + event.data);
    },


    start : function (DEBUG) {
      π.timer.start("util.math.trig");

      if( !this.__init(DEBUG) ) {
        pi.log('util.math.trig.__init() returned false, aborting...');
      }
      pi.log("initialized trig unit in " + π.timer.stop("util.math.trig") + " ms.");
    }
  };




  π.util.math.trig.start();
