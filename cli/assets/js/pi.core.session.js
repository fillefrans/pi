
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

    __socket        : null,
    __initialized   : false,

    __server        : window.location.hostname,
    __protocol      : 'ws://',
    __port          : 8000,
    __uri           : '',


    // protected


    // public

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
        pi.log("publishing packet to '" + packet.address + "' : ", packet);
        π.events.publish(packet.address, packet);
      }
      if(!!packet.callback) {
        pi.log("invoking callback: '" + packet.callback + "' : ", π.callback.__items[packet.callback]);
        
        π.core.callback.call(packet.callback, packet);
      }
      else {
        pi.log("onmessage [" + typeof packet + "] : ", packet);
      }
    },



    //private

    __init : function (DEBUG) {
      π.timer.start("session.init");

      var 
        host        = 'ws://' + this.__server + ':' + this.__port + this.__uri;

      if(this.__initialized === true){
        //something is not right
        pi.log("error: __init() called twice ");
        return false;
      }
      
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
      // var
      //   self = π.core.session,
      //   bootstraptime = (new Date()).getTime() - π.__sessionstart;

      
      pi.log("pi session started in " + π.timer.stop("pi.session") + "\nElapsed since page head eval: " + ((new Date()).getTime() - π.__sessionstart) + "ms.");

      // lists all timers in console
      pi.timer.history.list();
    },


    __onerror : function (error) {
      var
        self = π.core.session;

      self.__handleError(error, self);
      pi.log("onerror: " + event.data);
    },


    __onclose : function (event) {
      var
        self    = π.core.session;

      pi.log("onclose:", event);
    },


    __createSocket : function (host) {
      try{
        pi.log("Connecting to: " + host);
        if (window.WebSocket){
          return new WebSocket(host);
        }
        else if (window.MozWebSocket){
          return new MozWebSocket(host);
        }
        else{
          return false;
        }
      }
      catch(ex) {
        pi.log(ex.name + ": " + ex.message, ex);
      }
    },


    __startSession : function (host) {
      try {
        pi.log('Connecting session socket: ' + host);
        if (false !== (this.__socket = this.__createSocket(host))) {
          this.__socket.addEventListener('error', this.__onerror);
          this.__socket.addEventListener('open', this.__onopen);
          this.__socket.addEventListener('message', this.__onmessage);
          this.__socket.addEventListener('close', this.__onclose);
          return true;
        }
      }
      catch (ex) {
        pi.log(ex.name + ": " + ex.message, ex);
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
          self.__socket.send(JSON.stringify(obj));
          return true;
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

  // do the thing
  π.session._loaded = π.session.start();
