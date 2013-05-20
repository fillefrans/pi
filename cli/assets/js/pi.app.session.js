
/**
 *  Session object. This is where we have to debug the pants off of this library
 *  This bit should be bulletproof
 *
 *  We need to implement a feedback mechanism for js errors that we can trap
 *  (and possibly for exceptions)
 *  gif beacon
 *
 *  That way we can monitor apps in the wild and pick up on problems quickly
 * 
 */



  π.require('app');
  π.require('events', false);    


  if (!π.app) {
    pi.log("pi.app is undefined!");
  }


  π.app.session = {

  /**
   * onmessage
   * By far the most important function in the session object
   *
   * This is where we will spend a big part of our time.
   * There shouldn't be any blocking code in here at all
   * and no error checking, this function is only called by trusted code
   * 
   */
    __onmessage : function (event) {
      var
        events = π.events || false,
        json   = JSON.parse(event.data);

      if(!events){
        return;
      }

      events.publish('pi.app.session', json.message);
      pi.log("onmessage [" + typeof(json.message) + "] : ", json.message);
    },



    __socket         : null,
    __sessionsocket  : null,
    __initialized    : false,

    __sessionserver  : window.location.hostname,
    __protocol       : 'ws://',
    __initport       : 8000,
    __inituri        : '/session',
    __sessionport    : 8101,
    __sessionuri     : '',


    active    : false,
    user      : null,


    __init : function (DEBUG) {

      var 
        host        = 'ws://' + this.__sessionserver + ':' + this.__initport + this.__inituri,
        sessionhost = 'ws://' + this.__sessionserver + ':' + this.__sessionport;

      if(!π.events) {
        π.require('events');
      }


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
        self = π.app.session,
        bootstraptime = (new Date()).getTime() - π.__sessionstart;


      pi.log("pi.app bootstrapped in " + bootstraptime + " millisecs");

    },

    __onerror : function (error) {
      var
        self = π.app.session;

      self.__handleError(error, self);
      pi.log("onerror: " + event.data);
    },

    __onclose : function (event) {
      var
        self    = π.app.session;

      pi.log("onclose:" + event.data);
    },

    __startSession : function (host) {
      var 
        self = this;

      try {
        pi.log('Connecting session request socket: ' + host);
        this.__socket = this.__createSocket(host);

        this.__socket.addEventListener('error', function(error) {
          self.__handleError(error, self);
        });

        this.__socket.addEventListener('open', function(event) {
          pi.log('Opened session request socket. Event: ', event);
          this.send(JSON.stringify({command: 'session'}));
        });

        this.__socket.addEventListener('message', function(event) {

          var
            json = JSON.parse(event.data);
          var
            message = json.content;

          pi.log('Received (' + event.data.length + ' bytes): ' + event.data);
          // handle message event
          if(message.OK) {
            // it seems we have to wait a few millis for the session to come up

              setTimeout(function (self) {
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
          pi.log('Session closed. Status -> ' + this.readyState);
        });
      return true;
      }
      catch (ex) {
        pi.log(ex);
        return false;
      }
    },

    send : function (obj) {
      try {
        var msg = JSON.stringify(obj);
        console.log(this);
        this.__socket.send(msg);
        pi.log('Sent (' + msg.length + ' bytes): ' + msg);
        // pi.log('Sent (' + msg.length + ' bytes): ' + msg);
      }
      catch (ex) {
        pi.log(ex);
      }
    },

    quit : function () {
      var
        self = π.app.session;

      pi.log('Goodbye!');

      self.__socket.close();
      self.__socket = null;
      self.__initsocket.close();
      self.__initsocket = null;
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
        pi.log(ex.name + ": " + ex.message);
      }
    },


    start : function (DEBUG) {
      if( !this.__init(DEBUG) ) {
        pi.log('__init() returned false, aborting...');
      }
    }
  };


  π.app.session.start();
  π.app.session._loaded = true;
