
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

    __sessionid      : null,

    // protected


    // public

    active    : false,
    loggedin  : false,
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
      // π.events.publish('pi.session', [packet.data]);

      if(!!packet.address) {
        pi.log("publishing packet to '" + packet.address + "' : ", packet.data);
        π.events.publish(packet.address, [packet.data]);
      }
      if(!!packet.callback) {
        pi.log("invoking callback: '" + packet.callback + "' : ", π.callback.__items[packet.callback]);
        
        π.core.callback.call(packet.callback, packet.data);
      }
      else {
        pi.log("onmessage [" + packet.address + "] : ", packet.data);
      }
    },



    //private

    __init : function (DEBUG) {
      π.timer.start("session.init");

      var 
        host        = 'ws://' + this.__sessionserver + ':' + this.__initport + this.__inituri;

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
      var
        self = π.core.session;

      pi.log("pi session started in " + π.timer.stop("pi.session") + "\nElapsed since page head eval: " + ((new Date()).getTime() - π.__sessionstart) + "ms.");
      // lists all timers in console
      pi.timer.history.list();
    },


    __onerror : function (error) {
      var
        self = π.core.session;

      self.__handleError(error, self);
      pi.log("onerror: " + error.data);
    },


    __onclose : function (event) {
      var
        self    = π.core.session;

        self.active = false;
        self.__sessionid = null;
      pi.log("onclose:" + event.data);
    },


    __onconnected : function (event) {
      var
        self    = π.core.session,
        packet  = event || false;

      if(packet.sessionid) {
        self.__sessionid = packet.sessionid;
        self.active = true;
      }
      else {
        pi.log('Error: wrong packet format in pi.session.__onconnected');
      }

      pi.log("onconnected: " + JSON.stringify(event));
      π.events.unsubscribe('pi.session.connect', self.__onconnected);
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
        pi.log('Connecting session request socket: ' + host);
        self.__socket = self.__createSocket(host);

        self.__socket.addEventListener('error', function(error) {
          self.__handleError(error, self);
        });

        self.__socket.addEventListener('open', function(event) {
          pi.log('Opened session request socket. Event: ', event);
          π.timer.stop("session.init");
          π.timer.start("session.request");
          π.events.subscribe('pi.session.connect', self.__onconnected);
          pi.log("Sending session command");
          this.send(JSON.stringify({command: 'session'}));
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
              pi.log("Connecting to " + 'ws://' + self.__sessionserver + ":" + message.sessionPort);
              self.__sessionsocket = self.__createSocket('ws://' + self.__sessionserver + ":" + message.sessionPort);
              self.__sessionsocket.addEventListener("error", self.__onerror);
              self.__sessionsocket.addEventListener("open", self.__onopen);
              self.__sessionsocket.addEventListener("close", self.__onclose);
              self.__sessionsocket.addEventListener("message", self.__onmessage);

            }, 500, self );
            π.debug('OK', message);
          }
          else {
            pi.log('Server says Not OK.', message);
            π.debug('Not OK', message);
          }

        });

        this.__socket.addEventListener('close', function(event) {

          // handle close event
          pi.log('Session closed. Status -> ' + this.readyState);
        });
      return true;
      }
      catch (ex) {
        pi.log(ex.name + ": " + ex.message, ex);
        return false;
      }
    },


    __onlogin : function (message) {
      pi.log("User logged in.", message);
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


