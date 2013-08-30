/**   π.core.rpc
  *
  *   Store references to local callback functions
  *   Call remote procedure and create a listener for the result
  *   Invoke local callback when result arrives
  * 
  *   @author Johan Telstad, jt@enfield.no, 2011-2013
  */


  var π = π  || {};

 

  π.callback = π.callback || {

    /**
     * Callback handlers
     * 
     */

    id : 0,
    items : {},



    

    __add : function (address, replyaddress, command) {
      // insert item and return index of newly inserted item
      return 
        this.items.push({
          address: address, 
          replyaddress: replyaddress, 
          command: command, 
          time: {
            added: new Date().getTime()
          }
        });
    },


    //public

    add : function (address, replyaddress, command) {
      // insert item and return index of newly inserted item
      return 
        this.items.push({
          address: address, 
          replyaddress: replyaddress, 
          command: command, 
          time: {
            added: new Date().getTime()
          }
        });
    },


    call : function (address, command, callback, onerror) {
      // body...
    }

  };


