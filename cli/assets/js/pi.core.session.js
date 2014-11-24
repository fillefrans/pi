/**
 * @module π.session
 * 
 *  Session object. This is where we have to debug the pants off of this library
 *  This bit should be bulletproof
 *
 *  Basically, this is where our application runs. 
 *
 * 
 * @todo We should implement a feedback mechanism for js errors.
 *       That way we can monitor apps in the wild and pick up on problems quickly
 *
 * @author Johan Telstad, 2011-2014
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
    _connected  : false,


    // public
    active      : false,
    user        : null,


    // get connected() {
    //   return (this.__socket.readyState == 1);
    // },



  /**
   * Handles incoming messages on the session WebSocket
   * By far the most important function in the session object
   *
   * This is where we will spend a big part of our time.
   * There shouldn't be any blocking code in here at all
   * and no error checking, this function is only called by trusted code
   * 
   * @param {Object}  event  Pi Event Object
   * 
   */
    __onmessage : function (event) {
      var
        packet   = JSON.parse(event.data);

      // publish all incoming messages on local debug channel, for debugging
      π.events.publish('debug|pi.session.onmessage', packet);

      // packet has a callback?
      if (typeof packet.callback === "function" ) {
        pi.log("invoking callback: '" + packet.callback + "' : ", π.callback.__items[packet.callback]);

        // invoke callback, pass along packet
        π.core.callback.call(packet.callback, packet);
      }
      // packet has an address?
      else if (typeof packet.address == "string") {

        // publish to address
        π.events.publish(packet.address, packet);
      }
      // packet has neither address nor callback
      else {
        // orphan packet - no address or callback
        pi.log("onmessage [" + typeof packet + "] ('"+packet.address+"'): ", packet);
      }
    },



    // private

    __init : function (DEBUG) {

      var 
        host = 'ws://' + this.__server + ':' + this.__port + this.__uri;

      if (this.__initialized === true) {
        // something is not right
        pi.log("error: __init() called twice ");
        return false;
      }
      
      this.__initialized = this.__startSession(host);
      return this.__initialized;
    },


    __handleError  : function(msg, obj) {
      pi.log('error: ' + msg, obj);
    },


    __login : function(credentials) {
      pi.log("login: ", credentials);
      return true;
    },

    __onopen : function (event) {
      var
        self = π.core.session,
        bootstraptime = (new Date()).getTime() - π.__sessionstart;

      self._connected = true;

      π.events.trigger('pi.session.ready');

      // smallish hack, because we don't do login yet
      pi.events.trigger('pi.session.start');
      
      π.timer.stop("pi.session");

      self.addStreamListener('pi.session.1', function (data) {
        pi.log('stream chunk : ' + data, data);
      });

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

      pi.log("onclose:", event);
    },


    __createSocket : function (host) {
      try {
        if (window.WebSocket) {
          return new WebSocket(host);
        }
        else if (window.MozWebSocket) {
          return new MozWebSocket(host);
        }
        else {
          return false;
        }
      }
      catch (ex) {
        pi.log(ex.name + ": " + ex.message, ex);
      }
    },


    __startSession : function (host) {
      try {
        if (false !== (this.__socket = this.__createSocket(host))) {

          this.__socket.addEventListener('error',   this.__onerror);
          this.__socket.addEventListener('open',    this.__onopen);
          this.__socket.addEventListener('message', this.__onmessage);
          this.__socket.addEventListener('close',   this.__onclose);

          return true;
        }
      }
      catch (ex) {
        pi.log(ex.name + ": " + ex.message, ex);
        return false;
      }

      pi.events.trigger('pi.session.start');

    },

    // protected


    // public

    /**
     *
     * Listen to an address in the global namespace via session WebSocket
     * 
     * @param  {string}     address   Address in the pi namespace
     * @param  {Function}   listener  Callback for data chunks
     * 
     * @return true or false
     */

    addStreamListener : function (address, listener, onerror) {
      var
        self = π.core.session,
        commandpacket = { 
          command : "subscribe",
          address : address,
          data    : address
        };

      // create the listener
      π.events.subscribe(address, listener);

      // request the stream
      // pi.log('requesting data stream : ' + address);
      return self.send(commandpacket);
    },


    removeStreamListener : function (address, listener, onerror) {
      var
        self = π.core.session,
        commandpacket = { 
          command : "unsubscribe",
          address : address,
          data    : address
        };

      // remove the listener
      π.events.unsubscribe(address, listener);

      // stop the stream
      return self.send(commandpacket);
    },


    send : function (obj) {
      var
        self = π.core.session;

      try {
        if (self.__socket && (self.__socket.readyState === 1)) {
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

      pi.log('Closing session socket.');

      self.__socket.close();
      self.__socket = null;
    },


    start : function (DEBUG) {
      π.timer.start("pi.session");

      if (!this.__init(DEBUG)) {
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

  // do the things
  π.session._loaded = π.session.start();