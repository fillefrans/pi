
/**
 *  Session object. This is where we have to debug the pants off of this library
 *  This bit should be bulletproof
 *
 *  Basically, this is where our application runs. 
 *
 * 
 *  We should implement a feedback mechanism for js errors
 *  That way we could monitor apps in the wild and pick up on problems quickly
 * 
 */


  π.core.session = {

    // private

    __socket      : null,
    __initialized : false,

    __server      : window.location.hostname,
    __port        : 8008,
    __protocol    : 'ws://',
    __uri         : '',


    // protected


    // public
    sessionid : false,
    active    : false,
    user      : null,



  /**
   * π.core.session.__onmessage
   * 
   * Handles incoming messages on the session WebSocket
   * By far the most important function in the session object
   *
   * This is where we will spend a big part of our time.
   * There shouldn't be any blocking code in here at all
   * and no error checking, this function is only called by trusted code
   * 
   */

    __onmessage : function (event) {
      var
        packet   = JSON.parse(event.data);

      // publish all messages on local session channel, for debugging
      π.events.publish('pi.session', packet);

      if(!!packet.address) {
        // this is our normal case, a packet with an address
        // pi.log("publishing packet to '" + packet.address + "' : ", packet);
        π.events.publish(packet.address, packet);
      }
      if(!!packet.callback) {
        // check for callback and invoke if present
        // pi.log("invoking callback: '" + packet.callback + "' : ", π.callback.__items[packet.callback]);
        π.core.callback.call(packet.callback, packet);
      }
      else {

        // rien, c'est parfait

      }
    },



    //private

    __init : function (DEBUG) {
      π.timer.start("session.init"); 

      var 
        host = this.__protocol + this.__server + ':' + this.__port + this.__uri;

      if(this.__initialized === true){
        //something is not right
        pi.exception.add("error: __init() called twice", this);
        return false;
      }

      // wait for session connect msg from server
      π.events.subscribe("pi.session.connect", function(evt) {
        var
          json = evt,
          sessionstart = new CustomEvent("pi.session.start");

        π.session.sessionid = json.data.sessionid || false;
        π.events.publish("pi.session.start", π.session.sessionid);

        // unregister
        π.events.unsubscribe("pi.session.connect");
        
        // some browsers complain if we set .detail in the CustomEvent constructor
        sessionstart.detail = {sessionid : π.session.sessionid};

        window.dispatchEvent(sessionstart);
        return;
      });
      
      this.__initialized = this.__startSession(host);
      return this.__initialized;
    },


    __handleError  : function(msg, obj){
      pi.log('error: ' + msg, obj);
    },


    __login : function(credentials) {
      pi.log("login: ", credentials);
      return true;
    },


    __onopen : function (event) {
      π.timer.stop("pi.session");
      
      this.send('{ command: "pi.session" }');
      pi.log("onopen:");

      // lists all timers in console
      /// pi.timer.history.list();
    },


    __onerror : function (error) {
      var
        self = π.core.session;

      self.__handleError(error, self);
      pi.log("onerror: " + error, error);
    },


    __onclose : function (event) {
      var
        self = π.core.session;

      pi.log("onclose:" + event.data);
    },


    __createSocket : function (host) {
        pi.log("Connecting to: " + host);

        // return ( window.MozWebSocket ? new MozWebSocket(host) : (window.WebSocket ? new WebSocket(host) : false) );

        if (window.WebSocket) {
          return new WebSocket(host);
        }
        else {
          if (window.MozWebSocket) {
              return new MozWebSocket(host);
            }
          else {
            pi.log("error: No WebSockets");
            return false;
          }
        }
    },


    __startSession : function (host) {
      var 
        self = π.core.session;

      try {
        self.__socket = self.__createSocket(host);
        self.__socket.addEventListener("error", self.__onerror);
        self.__socket.addEventListener("open", self.__onopen);
        self.__socket.addEventListener("close", self.__onclose);
        self.__socket.addEventListener("message", self.__onmessage);

        return true;
      }
      catch (ex) {
        pi.log("exception in __startSession() - " + ex.name + ": " + ex.message, ex);
        return false;
      }
    },

    //protected


    //public

    send : function (obj) {
      var
        self = π.core.session;

      try {
        if(self.__socket && (self.__socket.readyState === 1) ){
          return self.__socket.send(JSON.stringify(obj));
        }
        else {
          pi.log("Error: Socket not ready.");
          pi.log("__socket.readyState: " + self.__socket.readyState);
          return false;
        }
      }
      catch (ex) {
        pi.log(ex.name + ": " + ex.message, obj);
        return false;
      }
    },


    quit : function () {
      var
        self = π.core.session;

      pi.log('Goodbye!');
      self.sessionid = false;
      self.__socket.close();
      self.__socket = null;
    },


    start : function (DEBUG) {
      π.timer.start("pi.session");

      if( !this.__init(DEBUG) ) {
        pi.log('session.__init() returned false, aborting...');
        return false;
      }
      else {
        return true;
      }
    }
  };


  // Create pi.session as an alias for pi.core.session 
  π.session = π.core.session;
  π.session._loaded = π.session.start();


