  /**
   *
   * π, µ
   *
   * @author @copyright Johan Telstad, jt@kroma.no, 2013
   *
   */


  var 
      π  = π  || {},
      pi = pi || π;


  /*  ----  Our top level namespaces  ----  */
    π.events      = π.events      || { _loaded: false, _ns: 'events' };
    π.srv         = π.srv         || { _loaded: false, _ns: 'srv' };
    π.app         = π.app         || { _loaded: false, _ns: 'app' };
    π.pcl         = π.pcl         || { _loaded: false, _ns: 'pcl' };
    π.session     = π.session     || { _loaded: false, _ns: 'session' };
    π.system      = π.system      || { _loaded: false, _ns: 'system' };
    π.debug       = π.debug       || { _loaded: false, _ns: 'debug' };

    π.util        = π.util        || { _loaded: false, _ns: 'util' };
    π.math        = π.math        || { _loaded: false, _ns: 'math' };
    π.statistics  = π.statistics  || { _loaded: false, _ns: 'statistics' };

    // your plugins here, like so:   pi.plugins.yourcompany.yourplugin.[whatever] = { # your plugin code };
    π.plugins     = π.plugins     || { _loaded: false, _ns: 'plugins' };

    // for all you crazy cowboys, your exclusive playground
    π.maverick    = π.maverick    || { _loaded: false, _ns: 'maverick' };


    π.APP_ROOT    = "assets/js/";
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
          π.events.publish("pi.timer.on", ["add", obj]);
        },

        list  : function (callback){
          var
            log = π.timer.history.log;


          log.forEach(function(index, value) {
            if(callback) {
              callback.call(index, value);
            }
            pi.log(index + "\t| " + value.id + ":\tstart = " + value.start + "\tstop = " + value.stop);
          });
        },

        clear : function () {
          var
            log = π.timer.history.log;

          // clear log
          while(log.pop()){
            // nop
          }
          π.events.publish("pi.timer.on", ["clear"]);
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
          events.publish("pi.timer.on", ["start", timers[id]]);
        }
      },

      stop : function(timerid) {
        var
          timers  = π.timer.timers,
          history = π.timer.history,
          self    = π.timer.timers[timerid.replace(/\./g,'_')] || false;

        if(!self) {
          π.events.publish("pi.timer.warning", "Warning: stopping non-existent timer " + timerid + ". Results unpredictable.");
          pi.log("Warning: stopping non-existent timer " + timerid + ". Results unpredictable.");
          return false;
        }

        self.stop = (new Date()).getTime();

        self.time = self.stop - self.start;
        var 
          result = self.time;
        history.add(self);

        // clear timer, this shouldn't delete the object, i think
        timers[timerid] = false;

        // return timer value
        return result;
      }
    };



    pi.forEachObj = function(object, callback) {
          for (var index in object) {
              callback.call(pi, index, object[index]);
          }
      };

    // refreshes the global namespace
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


    π.require = function(module, async, defer, callback){

    
      if (π.loaded[module.replace(/\./g,'_')]) {
        if(callback) {
          this.callback.call("loaded");
        }
        return true;
      }
  
      var 
        cursor  = document.getElementsByTagName ("head")[0] || document.documentElement,
        path    = '../../assets/js/pi.',
        script  = document.createElement('script');


      // pi.log('loading module (' + (!!async ? "async" : "sync") + '): pi.' + module);

      script.async      = async || true;
      script.defer      = defer || true;
      script.src        = path + module + '.js';
      script.self       = script;
      script.module     = module;
      script.callback   = callback || false;
      script.modname    = module.replace(/\./g,'_');

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



  /***   ------   INITIALIZATION    ------
     *
     *  Code we run after having created the base π object.
     *
     */


  π.log("Pi bootstrapped in " + ((new Date()).getTime() - π.__sessionstart) + " ms. Loading modules...");

  // start a timer for the application bootstrap
  π.timer.start("bootstrap");

  π.require("events", false, false, function (e) {
    pi.log("...loaded: events", e);
  });

  π.require("app", false, false, function (e) {
    pi.log("...loaded: app", e);
  });

  π.require("app.session", false, false, function (e) {
    pi.log("...loaded: app.session", e);
  });
  
  π.require("pcl", false, false, function (e) {
    pi.log("...loaded: pcl", e);
  });



  /* a safari bug-fix. under suspicion of being useless */
  window.addEventListener('load', function(e) {
      setTimeout(function() { window.scrollTo(0, 1); }, 1);
    }, false);
