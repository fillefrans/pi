
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

    __socket         : null,
    __sessionsocket  : null,
    __initialized    : false,

    __sessionserver  : window.location.hostname,
    __protocol       : 'ws://',
    __initport       : 8000,
    __inituri        : '/session',
    __sessionport    : 8101,
    __sessionuri     : '',


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
        // not a callback
        // pi.log("onmessage [" + typeof packet + "] : ", packet);
      }
    },



    //private

    __init : function (DEBUG) {
      π.timer.start("session.init"); 

      var 
        host        = 'ws://' + this.__sessionserver + ':' + this.__initport + this.__inituri;

      if(this.__initialized === true){
        //something is not right
        pi.log("error: __init() called twice");
        return false;
      }

      // wait for session connect msg from server
      π.events.subscribe("pi.session.connect", function(evt) {
        var
          json = evt,
          sessionstart = new CustomEvent("pi.session.start");

        π.session.sessionid = json.data.sessionid || false;
        π.events.publish("pi.session.start", π.session.sessionid);
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

      // lists all timers in console
      /// pi.timer.history.list();
    },


    __onerror : function (error) {
      var
        self = π.core.session;

      self.__handleError(error, self);
      pi.log("onerror: " + event.data);
    },


    __onclose : function (event) {
      var
        self = π.core.session;

      // pi.log("onclose:" + event.data);
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
      var 
        self = π.core.session;


      try {
        /// pi.log('Connecting session request socket: ' + host);
        self.__socket = self.__createSocket(host);

        self.__socket.addEventListener('error', function(error) {
          self.__handleError(error, self);
        });

        self.__socket.addEventListener('open', function(event) {
          π.timer.stop("session.init");
          π.timer.start("session.request");          
          self.send({command: 'session'});
        });

        self.__socket.addEventListener('message', function(event) {

          var
            json = JSON.parse(event.data);
          var
            message = json.content;

          pi.log('Received (' + event.data.length + ' bytes): ' + event.data);
          // handle message event
          if(message.OK) {

            // pi.log(message);
            // we have to release the execution pointer to allow the session to start up
            setTimeout(function (self) {
              π.timer.stop("session.request");
              self.__sessionsocket = self.__createSocket('ws://' + self.__sessionserver + ":" + message.sessionPort);
              self.__sessionsocket.addEventListener("error", self.__onerror);
              self.__sessionsocket.addEventListener("open", self.__onopen);
              self.__sessionsocket.addEventListener("close", self.__onclose);
              self.__sessionsocket.addEventListener("message", self.__onmessage);
            }, 1, self );
            π.debug('OK', message);
          }
          else {
            π.debug('Not OK', message);
          }

        });

        this.__socket.addEventListener('close', function(event) {

          // handle close event
          // pi.log('Session closed. Status -> ' + this.readyState);
        });
      return true;
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
        if(self.__sessionsocket && (self.__sessionsocket.readyState === 1) ){
          self.__sessionsocket.send(JSON.stringify(obj));
          return true;
        }
        else if(self.__socket && (self.__socket.readyState === 1) ){
          self.__socket.send(JSON.stringify(obj));
          return true;
        }
        else {
          pi.log("Error: Socket not ready.");
          pi.log("__socket.readyState: " + self.__socket.readyState);
          pi.log("__sessionsocket.readyState: " + self.__sessionsocket.readyState);
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
      self.__sessionsocket.close();
      self.__sessionsocket = null;
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


