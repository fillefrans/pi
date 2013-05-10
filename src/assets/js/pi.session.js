// ensure we have a global app object


  π.app = {

    __socket         : null,
    __initsocket     : null,
    __initialized    : false,
    __sessionserver  : window.location.hostname, //default values, override by passing options object to start() function
    __initport       : 8000,
    __inituri        : '/session',
    __sessionport    : 8101,
    __sessionuri     : '',
    that             : this,
    self             : this,


    __init : function (DEBUG) {
      var host = 'ws://' + this.__sessionserver + ':' + this.__initport + this.__inituri,
          DBG = DEBUG || false;

      if(this.__initialized === true){
        //something is not right
        return false;
      }

      if(DBG){
        // go straight to session
//        host = 'ws://' + this.__sessionserver + ':' + this.__initport + this.__sessionuri;
        return this.__startSession(host);
      }
      try {
        this.__initsocket = this.__createSocket(host);
        self.log('WebSocket - status ' + this.__initsocket.readyState);

        this.__initsocket.addEventListener('open', function(event) {
          // handle open event
          var init__command = {command: 'session'};

          self.log('Opened WebSocket. This: ' + this + ', that: ' + that);
          self.send(JSON.stringify(init__command));
        });

        this.__initsocket.addEventListener('message', function(event) {
          // handle message event
          self.log('Received (' + event.data.length + ' bytes): ' + event.data);
          self.__startSession();
          });

        this.__initsocket.addEventListener('close', function(event) {
          // handle close event
          self.log('Disconnected. Status -> ' + this.readyState);
        });

        this.__initsocket.addEventListener('error', function(event) {
          // handle close event
          self.log('ERROR! -> ', event);
        });

      this.__initialized = true;
      return true;
      }
      catch (ex) {
        console.log(ex);
        this.log(ex);
        return false;
      }
    },

    __handleError  : function(msg){
  //    alert('__handleError: ' + msg);
      console.log('error: ' + msg);
    },

    send : function (obj) {
      try {
        var msg = JSON.stringify(obj);
        this.__socket.send(msg);
        console.log('Sent (' + msg.length + ' bytes): ' + msg);
        this.log('Sent (' + msg.length + ' bytes): ' + msg);
      }
      catch (ex) {
        this.log(ex);
      }
    },

    quit : function () {
      this.log('Goodbye!');
      this.__socket.close();
      this.__socket = null;
      this.__initsocket.close();
      this.__initsocket = null;
    },

    log : function (msg,obj) {
      if (typeof obj!=="undefined") {
        console.log(msg, obj);
      }
      else {
        console.log(msg);
      }
    },

    __createSocket : function (host) {
      if (window.WebSocket){
        return new WebSocket(host);
      }
      else if (window.MozWebSocket){
        return new MozWebSocket(host);
      }
      else{
        return false;
      }
    },


    __startSession : function (host) {
      var that = this;
      try {
        this.log('Connecting: ' + host);
        this.__sessionsocket = this.__createSocket(host);
        this.log('SessionSocket - status ' + this.__sessionsocket.readyState);
        this.__sessionsocket.addEventListener('open', function(event) {
          console.log('Opened SessionSocket. Event: ', event);
          that.send(JSON.stringify({command: 'sessionstart'}));
        });
        this.__sessionsocket.addEventListener('message', function(event) {

          // handle message event
          console.log('Received (' + event.data.length + ' bytes): ' + event.data);
          });
        this.__sessionsocket.addEventListener('close', function(event) {

          // handle close event
          console.log('Session closed. Status -> ' + this.readyState);
        });
      return true;
      }
      catch (ex) {
        console.log(ex);
        this.log(ex);
        return false;
      }
    },


    run : function (DEBUG) {
      if( !this.__init(DEBUG) ) {
        console.log('__init() returned false, aborting...');
      }
    }
  };


π.app.run(true);