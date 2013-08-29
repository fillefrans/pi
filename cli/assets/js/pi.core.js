  /**
   *
   * π v0.3
   *
   * @author @copyright Johan Telstad, jt@enfield.no, 2011-2013
   *
   */


  var 
      π  = π  || {};



  /*  ----  Our top level namespaces  ----  */
    π.events      = π.events      || { _loaded: false, _ns: 'events' };
    π.srv         = π.srv         || { _loaded: false, _ns: 'srv' };
    π.app         = π.app         || { _loaded: false, _ns: 'app' };
    π.pcl         = π.pcl         || { _loaded: false, _ns: 'pcl' };
    π.session     = π.session     || { _loaded: false, _ns: 'session' };
    π.system      = π.system      || { _loaded: false, _ns: 'system' };
    π.debug       = π.debug       || { _loaded: false, _ns: 'debug' };
    π.io          = π.io          || { _loaded: false, _ns: 'io' };

    π.util        = π.util        || { _loaded: false, _ns: 'util' };

    // your plugins here, like so:   pi.plugins.yourcompany.yourplugin[.whatever] = { # your plugin object };
    π.plugins     = π.plugins     || { _loaded: false, _ns: 'plugins' };

    π.maverick    = π.maverick    || { _loaded: false, _ns: 'maverick' };


    π.PI_ROOT    = "assets/js/";
    π.LIB_ROOT    = "../../assets/js/";
    π.SRV_ROOT    = "../../../srv/";
    

    //will keep an updated list over which modules are loaded
    π.loaded = {};






    π.forEachObj = function(object, callback) {
          for (var index in object) {
              callback.call(pi, index, object[index]);
          }
      };


    // recurse over the global namespace
    π.updateNS = function() {
      var 
        recurse = function(idx, obj) {
          if(typeof obj==="object") {
            console.log('found: ', obj);
            π.forEachObj(obj, recurse);
          }
        };

      π.forEachObj(π, recurse);
    };


    π.debug = function(msg, obj) {

      var 
        caller = this;

      if(!!obj){
        console.log(msg, obj);
      }
      else {
        console.log(msg);
      }

    };


    π.log = function(msg, obj) {

      if(!!obj){
        console.log(msg, obj);
      }
      else {
        console.log(msg);
      }
    };


    π.inject = function (src, elem) {
      var 
        element   = elem || document.body,
        fragment  = document.createDocumentFragment(),
        container = document.createElement("div");
       
      container.innerHTML = src;
      fragment.appendChild(container);
      element.appendChild(fragment);
    };



    π.copy = function (obj) {
      return JSON.parse(JSON.stringify(obj));
    };



    /** π.listen
     *
     * Listen to an address in the namespace via EventSource/SSE
     * 
     * @param  {string}     address   Address in the pi namespace to listen to
     * @param  {Function}   onerror   Callback on error
     * @param  {Function}   callback  Callback for each message
     * @return {void}
     */


    π.listen = function (address, callback, onerror) {
      var 
        source  = new EventSource('/pi/pi.io.sse.monitor.php' + ((address!='') ? '?address=' + encodeURI(address) : ''));

      source.addEventListener('error',    onerror,  false);
      source.addEventListener('message',  callback, false);
    };




    /** π.await
     *
     * Wait for named event, then trigger a given callback
     * If the event has not occurred within the given timeout, 
     * try to read the value directly. 
     * 
     * @param  {string}     address   Address in the pi namespace to wait for
     * @param  {Function}   onerror   Callback on error
     * @param  {Function}   onresult  Callback when return value available
     * @return {boolean}              Should always return true
     */

    π.await = function(address, onresult, timeout){
    
      // await named event
      π.events.subscribe(address, onresult);
      //request an update from the server
      π._send("await", address);
    };




    /** π.read
     *
     * Read a remote value
     * 
     * @param  {string}     address   Address in the pi namespace to read from
     * @param  {Function}   onerror   Callback on error
     * @param  {Function}   callback  Callback when return value available
     * @return {boolean}              Result if success, false if failure
     */

    π.read = function(address, onresult){
    
      // TBC

      π._send("read", address, null, onresult);
  
    };

    /** π.write
     *
     * Write a value to a remote variable location
     * 
     * @param  {string}     address   Address in the pi namespace to write to
     * @param  {Function}   onerror   Callback on error
     * @param  {Function}   callback  Callback when return value available
     * @return {boolean}              Old value if success, false if failure
     */

    π.write = function(address, value, onresult){
      // TBC
      π._send("write", address, value, onresult);
    };



    /** π.send
     *
     * Handle app request for sending a message to an address in the pi namespace
     * Conform to pi packet specification
     *
     * 
     * @param  {string}     address   Address in the pi namespace to read from
     * @param  {Function}   callback  Callback when return value available
     * @return {boolean}              Result if success, false if failure
     */

    π._send = function(command, address, data){
      var
        packet = {command: command, address: address, data: data};

      // TBC
        if(!!π.app.session._loaded) {
          π.app.session.send(packet);
        }

    };


    /** π.__send
     *
     * Does the actual sending of a message to an address in the pi namespace
     * 
     * @param  {string}     address   Address in the pi namespace to read from
     * @param  {Function}   onerror   Callback on error
     * @param  {Function}   callback  Callback when return value available
     * @return {boolean}              Result if success, false if failure
     */

    π.__send = function(address, data, callback, onerror){
      
      // TBC

  
    };




    /** π.readfile
     *
     * Read a remote (text) file
     * 
     * @param  {string}     fileaddress   File address in the pi namespace
     * @param  {string}     filetype      The file extension
     * @param  {Function}   onerror       Callback on error
     * @param  {Function}   callback      Callback for each return value available
     * @return {boolean}                  File contents on success, false on failure
     */

    π.readfile = function(fileaddress, filetype, callback, onerror){
    
      // TBC

  
    };




    /** π.call
     *
     * Call a remote procedure
     * 
     * @param  {string}     module    Name of the pi module to be loaded
     * @param  {boolean}    func      Remote procedure to call
     * @param  {Function}   onerror   Callback on error
     * @param  {Function}   callback  Callback when return value available
     * @return {boolean}              True for success, false for failure
     */

    π.call = function(module, func, callback, onerror){
    
      // TBC
  
    };



    /** π.require
     *
     * A simple dependency management system
     * 
     * @param  {string}     module    Name of the pi module to be loaded
     * @param  {boolean}    async     Load script asynchronously
     * @param  {boolean}    defer     Use deferred script loading
     * @param  {Function}   callback  Callback on loaded
     * @return {boolean}              True for success, false for failure
     */

    π.require = function(module, async, defer, callback){
    
      if (π.loaded[module.replace(/\./g,'_')]) {
        if(typeof callback==="function") {
          this.callback.call("loaded");
        }
        return true;
      }
  
      var 
        cursor  = document.getElementsByTagName ("head")[0] || document.documentElement,
        path    = '../../assets/js/pi.',
        script  = document.createElement('script');


      script.async      = async || true;
      script.defer      = defer || true;
      script.src        = path + module + '.js';
      script.self       = script;
      script.module     = module;
      script.modname    = module.replace(/\./g,'_');
      script.callback   = callback || false;

      pi.timer.start(module);

      script.onload = function (event) {
        var
          loadtime = π.timer.stop(this.modname);

        π.loaded[this.modname] = { time: (new Date()).getTime(), loadtime: loadtime };
        if(this.callback) {
          this.callback.call(event);
        }
      };

      script.onerror = function (error) {
        pi.log('error loading module: ' + this.module, error);
        if(this.callback) {
          this.callback.call(error);
        }
      };

      var
        node = cursor.insertBefore(script, cursor.firstChild);
      
      return !!node; 
    };





    /*
      core support modules
    */


    π.timer = {
      
      timers : {},


      start : function(timerid) {
        // replace . with _
        id = timerid.replace(/\./g,'_');
        var
          timers  = π.timer.timers,
          self    = π.timer.timers[id] || false,
          events  = π.events || false;

        if(self) {
          if(events.publish) {
            events.publish("pi.timer.warning", ["Warning: starting timer " + timerid + " for a second time. Results unpredictable."]);
          }
          pi.log("Warning: starting timer " + timerid + " for a second time. Results unpredictable.");
        }
        timers[id] = { id : timerid, start : (new Date()).getTime() };

        if(events.publish) {
          events.publish("pi.timer.on", ["start", timers[id]]);
        }
      },


      stop : function(timerid) {
        var
          timers  = π.timer.timers,
          history = π.timer.history,
          self    = π.timer.timers[timerid.replace(/\./g,'_')] || false;

        if(!self) {
          π.events.publish("pi.timer." + timerid, "Warning: stopping non-existent timer " + timerid + ". Results unpredictable.");
          pi.log("Warning: stopping non-existent timer " + timerid + ". Results unpredictable.");
          return false;
        }

        self.stop = (new Date()).getTime();

        self.time = self.stop - self.start;
        var 
          result = self.time;
        history.add(self);

        // return timer value
        return result;
      },

      history : {
        
        log   : [],

        add : function (obj) {
          π.timer.history.log.push(obj);
          π.events.publish("pi.timer.on", ["add", obj]);
        },

        list  : function (callback){
          var
            log = π.timer.history.log;


          log.forEach(function(value, index) {
            if(callback) {
              callback.call(index, value);
            }
            pi.log(value.id + ": " + value.time + "ms.");
          });
        },

        clear : function () {
          var
            log = π.timer.history.log;

          π.events.publish("pi.timer.history.on", ["clear"]);

          // clear log, this is actually the fastest way
          while(log.pop()){
            // nop
          }
        }
      } // end of history object
    };




  /***   ------   INITIALIZATION    ------
     *
     *  Code we run after having created the base π object.
     *
     */


  π.log("Pi bootstrapped in " + ((new Date()).getTime() - π.__sessionstart) + " ms. Initializing...");

  // start a timer for the platform initialization
  π.timer.start("pi.initialization");

  pi.log("Loading modules...");

  π.require("events", false, false, function (module) {
    pi.log("loaded: events", module);
  });

  π.require("app", false, false, function (module) {
    pi.log("loaded: app", module);
  });

  π.require("app.session", false, false, function (module) {
    pi.log("loaded: app.session", module);
  });
  
  π.require("pcl", false, false, function (module) {
    pi.log("loaded: pcl", module);
  });


  π.log("Pi initialized in " + π.timer.stop("pi.initialization") + " ms.");
  


  /* a safari bug-fix, supposedly. under heavy suspicion of being completely useless */
  window.addEventListener('load', function(e) {
      setTimeout(function() { window.scrollTo(0, 1); }, 1);
    }, false);
