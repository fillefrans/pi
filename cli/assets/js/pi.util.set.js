
/**
 *  π.util.set
 *  
 *  Implements js-equivalent of logical sets
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


    add : function (set, members) {
      var
        members = members || false,
        self    = π.util.set,
        set     = π.util.set.__items[set] || false,
        i;

      if ( members === false ) {
        return false;
      }

      if (set===false) {
        π.util.set.__items[set] = {};
        set = π.util.set.__items[set];
      }

      // ensure we have an array to work with
      if (! members instanceof Array ) {
        members = [members];
      }

      for (var i = 0, count = members.length; i < count; i++) {
        if (set[members[i]]) {
          continue;
        }
        set[members[i]] = true;
      }

      // return number of items in set after operation
      return π.util.objectLength(set);
    },


    remove : function (set, members) {
      var
        members = members || false,
        self    = π.util.set,
        set     = π.util.set.__items[set] || false,
        i;

      if ( (set === false) || (members === false) ) {
        return false;
      }

      // ensure we have an array to work with
      if (! members instanceof Array ) {
        members = [members];
      }

      for (var i = 0, count = members.length; i < count; i++) {
        if (set[members[i]]) {
          delete set[members[i]];
        }
      }
      // return number of items in set after operation
      return π.util.objectLength(set);
    },


    run : function (DEBUG) {

      if( !this.__init(DEBUG) ) {
        pi.log('session.__init() returned false, aborting...');
      }
    }
  };




  π.util.set.run();
