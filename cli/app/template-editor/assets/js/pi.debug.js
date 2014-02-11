


/**
 * π.debug
 *
 * 
 *
 */


var

  π = π || {};


    π.debug = {
      
      _loaded : false,
      __pi : {},

      callPrevious : false,


      onerror : function (msg, url, line){
        pi.log("error : " + msg + "")

      },


      __init : function() {

        var
          self = π.debug,
          self.__pi._errfunc = window.onerror || null;


        if (typeof window.onerror === "function") {
          self.callPrevious = true;
          self.previousErrorFunction = window.onerror;
          window.onerror = self.onerror;
        }


      }




    };


    π.debug._loaded = true;

  