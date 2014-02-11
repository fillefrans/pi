/**
 *  π.core.user 
 *  
 *  User object.
 *
 * 
 */


  π.core.user = {

    /**
     *  Properties
     * 
     */


    // private
    __initialized    : false,


    // protected


    // public
    active    : false,





    /**
     *  Methods
     * 
     */


    //private

    __init : function (DEBUG) {
      if(this.__initialized === true){
        // something is not right
        pi.log("error: __init() called twice ");
        return false;
      }
      return true;
    },


    __login : function(credentials) {
      pi.log("login: ", credentials);
      return true;
    },

    //protected


    //public


    logout : function () {
      var
        self = π.core.user;



      pi.log('Goodbye!');

    },


    start : function (DEBUG) {
      if( !this.__init(DEBUG) ) {
        pi.log('user.__init() returned false, aborting...');
        return false;
      }
      else {
        return true;
      }
    }
  };


  // Create pi.user as an alias for pi.core.user 
  π.user = π.core.user;

  π.user._loaded = π.user.start();