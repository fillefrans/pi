  /**
   *
   * π
   *
   * @author @copyright Johan Telstad, jt@enfield.no, 2011-2013
   *
   * @version 0.2
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


    π.PHP_ROOT    = "assets/js/";
    π.LIB_ROOT    = "../../assets/js/";
    π.SRV_ROOT    = "../../../srv/";
    // π.PHP_ROOT    = π.SRV_ROOT + "php/";
    // π.FPC_ROOT    = π.SRV_ROOT + "fpc/";
    

    //will keep an updated list over which modules are loaded
    π.loaded = {};



    /*
      global support functions
    */


    π.timer = {
      
      history : {
        
        log   : [],

        add : function (obj) {
          π.timer.history.log.push(obj);
          π.events.publish("pi.timer.history.on.add", obj);
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

          π.events.publish("pi.timer.history.on.clear", true);

          // clear log, this is actually the fastest way
          while(log.pop()){
            // nop
          }
        }
      }, // end of history object


      // the timer object proper

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
          events.publish("pi.timer.on.start", timers[id]);
          // events.publish("pi.timer.on", ["start", timers[id]]);
        }
      },


      stop : function(timerid) {
        var
          timers  = π.timer.timers,
          history = π.timer.history,
          self    = π.timer.timers[timerid.replace(/\./g,'_')] || false;

        if(!self) {
          π.events.publish("pi.timer.items." + timerid, "Warning: stopping non-existent timer " + timerid + ". Results unpredictable.");
          pi.log("Warning: stopping non-existent timer " + timerid + ". Results unpredictable.");
          return false;
        }

        self.stop = (new Date()).getTime();

        self.time = self.stop - self.start;

        π.events.publish("pi.timer.on.stop", self);


        var 
          result = self.time;
        history.add(self);

        // return timer value
        return result;
      }
    };



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


    π.listen = function (address, callback, onerror) {
      var 
        source  = new EventSource('/api/pi.io.sse.monitor.php' + ((address!='') ? '?address=' + encodeURI(address) : ''));

      source.addEventListener('error',    onerror,  false);
      source.addEventListener('message',  callback, false);
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



    /** π.await
     *
     * Wait for specified events, then invoke callback
     * 
     * @param  {Array}      events      Name(s) of the pi events to wait for
     * @param  {Function}   callback    Callback when all events have occurred
     * @param  {int}        timeout     How long to wait before giving up
     * @return {boolean}                True for success, false for failure
     */

    π.await = function(eventParam, callback, progress, timeout){
      var
        eventlist = {},
        progress = progress || false,
        timeout  = timeout  || 30;

      if(typeof callback!=="function") {
        return false;
      }

      if(typeof eventParam==="string") {
        // subscribe to this event
        pi.log("Awaiting: " + eventParam);
        // π.subscribe(eventParam, )
      }

      if(eventParam.length) {
        for(var i = 0, count = eventParam.length; i < count; i++) {
          // call ourselves for each individual entry
          π.await(eventParam[i]);
        }

      }
    };
  



  /***   ------   INITIALIZATION    ------
     *
     *  Code we run after having created the base π object.
     *
     */



  var
    // create 'pi' as an alias for π 
    pi = π;

  π.log("Pi bootstrapped in " + ((new Date()).getTime() - π.__sessionstart) + " ms. Initializing...");

  // start a timer for the platform initialization
  π.timer.start("pi.initialization");

  pi.log("Loading modules...");

  π.require("core.events", false, false, function (e) {
    pi.log("loaded: events", e);
  });

  π.require("app", false, false, function (e) {
    pi.log("loaded: app", e);
  });

  π.require("core.session", false, false, function (e) {
    pi.log("loaded: session", e);
  });
  
  π.require("pcl", false, false, function (e) {
    pi.log("loaded: pcl", e);
  });


  π.log("Pi initialized in " + π.timer.stop("pi.initialization") + " ms.");
  


  /* a safari bug-fix, supposedly */
  window.addEventListener('load', function(e) {
      setTimeout(function() { window.scrollTo(0, 1); }, 1);
    }, false);
