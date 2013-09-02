/**   π.core.callback
  *
  *   Store references to local callback functions
  *   Call remote procedure and create a listener for the result
  *   Invoke local callback when result arrives
  * 
  *   @author Johan Telstad, jt@enfield.no, 2011-2013
  */


  var π = π  || {};

  π.core.callback = π.core.callback || {

    /**
     * Manages callback handlers
     *
     * Issues replyaddresses, and invokes related
     * callback when response is received from server
     * 
     */

    __id      : 0,
    __prefix  : "___callback",
    __items   : {},

    

    //public

    add : function (callback) {

      // check input
      if(typeof callback !== "function") {
        return false;
      }

      // insert callback and return name of newly inserted item
      var
        self  = π.core.callback;
      var
        id    = self.__prefix + (self.__id++).toString(16);



      self.__items[id] = { callback : callback };

      pi.log("added callback '" + id + "': " + callback);

      return id;

    },


    call : function (id, data) {
      var 
        item    = π.core.callback.__items[id],
        result  = false;

      if(item && (typeof item.callback === "function")) {

        pi.log("invoking callback...");
        result = item.callback.call(this, data);
        pi.log(result);
        
        // clear callback item
        item = null;
      }
      else {
        pi.log("Error invoking callback: " + id, item);
      }

      return result;
    }

  };


π.callback = π.core.callback;
