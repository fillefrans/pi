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


    // These are the core modules
    π.core        = π.core        || { _loaded: false, _ns: 'core' };
    π.callback    = π.callback    || { _loaded: false, _ns: 'callback' };
    π.session     = π.session     || { _loaded: false, _ns: 'session' };
    π.events      = π.events      || { _loaded: false, _ns: 'events' };
    π.tasks       = π.tasks       || { _loaded: false, _ns: 'tasks' };
    π.timer       = π.timer       || { _loaded: false, _ns: 'timer' };


    // These are our built-in libraries
    π.srv         = π.srv         || { _loaded: false, _ns: 'srv' };
    π.app         = π.app         || { _loaded: false, _ns: 'app' };
    π.pcl         = π.pcl         || { _loaded: false, _ns: 'pcl' };
    π.system      = π.system      || { _loaded: false, _ns: 'system' };
    π.debug       = π.debug       || { _loaded: false, _ns: 'debug' };
    π.io          = π.io          || { _loaded: false, _ns: 'io' };
    π.file        = π.file        || { _loaded: false, _ns: 'file' };



    // These are for extending the platform
    π.lib         = π.lib         || { _loaded: false, _ns: 'lib' };
    π.util        = π.util        || { _loaded: false, _ns: 'util' };
    π.plugins     = π.plugins     || { _loaded: false, _ns: 'plugins' };
    π.maverick    = π.maverick    || { _loaded: false, _ns: 'maverick' };


    π.PHP_ROOT     = "assets/js/";
    π.LIB_ROOT    = "../../assets/js/";
    π.SRV_ROOT    = "../../../srv/";
    

    //will keep an updated list over which modules are loaded
    π.loaded = {};


    // create pi as an alias for π
    var 
      pi  = π;




    π.__onload = function (event) {
      π.__loadtime = new Date().getTime() - π.__sessionstart;
      pi.log("Page loaded in " + π.__loadtime + "milliseconds.", event);
    };



 

    /*    begin core modules     */



        /**   π.core.callback
          *
          *   Store references to local callback functions
          *   Call remote procedure and create a listener for the result
          *   Invoke local callback when result arrives
          * 
          *   @author Johan Telstad, jt@enfield.no, 2011-2013
          */


          var π = π  || {};

          π.core.callback = π.core.callback || {

            /**
             * Manages callback handlers
             *
             * Issues replyaddresses, and invokes related
             * callback when response is received from server
             * 
             */

            __id      : 0,
            __prefix  : "___callback",
            __items   : {},

            

            //public

            // insert callback and return name of newly inserted item
            add : function (callback) {

              // check input
              if(typeof callback !== "function") {
                return false;
              }

              var
                self  = π.core.callback;
              var
                id    = self.__prefix + (self.__id++).toString(16);


              self.__items[id] = { callback : callback, timestamp: (new Date().getTime()) };

              return id;
            },


            call : function (id, data) {
              var 
                item    = π.core.callback.__items[id],
                result  = false;

              if(item && (typeof item.callback === "function")) {        

                pi.log("invoking " + id + " after " + ( (new Date().getTime()) - item.timestamp ) + "ms");
                result = item.callback.call(this, data);
                // clear callback item
                item = null;

                return result;
              }
              else {
                pi.log("Error invoking callback: " + id, item);
              }
            }

          };


        π.callback = π.core.callback;
        π.callback._loaded = true;



        /**   π.events  
         *
         *   This is where we optimize. Absolutely no blocking code allowed.
         *
         *    This is the client side hub of our messaging system.
         *    It will handle data/message passing, events, and pubsub. In a hurry.
         *
         * @author Johan Telstad, jt@enfield.no, 2011-2013
         *
         * 
         * @uses     PubSub.js  -  https://github.com/Groxx/PubSub 
         *           @copyright 2011 by Steven Littiebrant
        **/


          var π = π  || {};

         

          π.events = π.events || {

            /**
             * Event handlers
             * 
             */

            __touchmove : function(event) {
              console.log("touchmove");
              event.preventDefault();
            },

            __touchstart : function(event) {
              console.log("touchstart");
              event.preventDefault();
            },

            __touchend : function(event) {
              console.log("touchend");
              event.preventDefault();
            }

          };


        /** PubSub.js 
          Copyright 2011 by Steven Littiebrant - changes and communication about use 
          appreciated but not required: https://github.com/Groxx/PubSub

          Permission is hereby granted, free of charge, to any person obtaining a copy
          of this software and associated documentation files (the "Software"), to deal
          in the Software without restriction, including without limitation the rights
          to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
          copies of the Software, and to permit persons to whom the Software is
          furnished to do so, subject to the following conditions:

          The above copyright notice and this permission notice shall be included in
          all copies or substantial portions of the Software.  Minified files may 
          reduce this license information to the first two lines at the top of the 
          LICENSE file.

          THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
          IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
          FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
          AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
          LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
          OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
          THE SOFTWARE.
           **/


          /**
           *  PubSub 
           *
           * Creates a new subscription object.  If passed, attaches to the passed object; otherwise, assumes it was called with "new" and binds to `this`.
           * If passed, unique defines the internal unique subscription list's storage name.  By default it is "_sub".
           * This should be relatively safe to call even without "new" - it just makes the global object an event system.
           * If true, alsoPassPath will result in the publishing-path being pushed to the end of the arguments passed to subscribers.
           * 
           */

            var PubSub = function PubSub(obj_or_alsoPassPath_or_unique, alsoPassPath_or_unique, uniqueName) {
              var unique = "_sub",
              passPath = false,
              bindTo = this;
              
              if (typeof(uniqueName) === "string") {
                unique = uniqueName;
              } else if (typeof(alsoPassPath_or_unique) === "string") {
                unique = alsoPassPath_or_unique;
              } else if (typeof(obj_or_alsoPassPath_or_unique) === "string") {
                unique = obj_or_alsoPassPath_or_unique;
              }

              if (typeof(alsoPassPath_or_unique) === "boolean") {
                passPath = alsoPassPath_or_unique;
              } else if (typeof(obj_or_alsoPassPath_or_unique) === "boolean") {
                passPath = obj_or_alsoPassPath_or_unique;
              }
              
              if (typeof(obj_or_alsoPassPath_or_unique) === "object" || typeof(obj_or_alsoPassPath_or_unique) === "function") {
                bindTo = obj_or_alsoPassPath_or_unique;
              }
              
              // all subscriptions, nested.
              var subscriptions = {};
              subscriptions[unique] = [];
              
              // Removes all instances of handler from the passed subscription chunk.
              var _unsubscribe = function(cache, handler) {
                for(var i = 0; i < cache[unique].length; i++) {
                  if(handler === undefined || handler === null || cache[unique][i] === handler) {
                    cache[unique].splice(i, 1);
                    i--;
                  }
                }
              };
              
              // Recursively removes all instances of handler from the passed subscription chunk.
              var _deepUnsubscribe = function(cache, handler) {
                for(sub in cache) {
                  if(typeof(cache[sub]) !== "object" || sub === unique || !cache.hasOwnProperty(sub)) continue;
                  _deepUnsubscribe(cache[sub], handler);
                }
                _unsubscribe(cache, handler);
              };
              
              // Calls all handlers on the path to the passed subscription.
              // ie, "a.b.c" would call "c", then "b", then "a".
              // If any handler returns false, the event does not bubble up (all handlers at that level are still called)
              bindTo.publish = function(sub, callback_args) {
                var args=null;

                if (arguments.length > 2) {
                  // If passing args as a set of args instead of an array, grab all but the first.
                  args = Array.prototype.slice.apply(arguments, [1]); 
                } else if (Array.isArray(callback_args)) {
                  args = callback_args;
                } else {
                  args = [];
                } 
                if (args.length === undefined) {
                  args = [args];
                }
                
                var cache = subscriptions;
                var stack = [];
                sub = sub || "";
                var s = sub.split(".");
                if (passPath) args.push(s);
                stack.push(cache);
                for(var i = 0; i < s.length && s[i] !== ""; i++) {
                  if(cache[s[i]] === undefined) break;
                  cache = cache[s[i]];
                  stack.push(cache);
                }
                var c;
                var exit = false;
                while((c = stack.pop())) {
                  for(var j = 0; j < c[unique].length; j++) {
                    if(c[unique][j].apply(this,args) === false) exit = true;
                  }
                  if (exit) break;
                }
                return bindTo;
              };

              
              bindTo.subscribe = function(sub, handler) {
                var cache = subscriptions;
                sub = sub || "";
                var s = sub.split(".");
                for(var i = 0; i < s.length && s[i] !== ""; i++) {
                  if (!cache[s[i]]) {
                    cache[s[i]] = {};
                    cache[s[i]][unique] = [];
                  }
                  cache = cache[s[i]];
                }
                cache[unique].push(handler);
                return bindTo;
              };
              
              
              // Removes _all_ identical handlers from the subscription.  
              // If no handler is passed, all are removed.
              // If deep, recursively removes handlers beyond the passed sub.
              bindTo.unsubscribe = function(sub, handler, deep) {
                var cache = subscriptions;
                sub = sub || "";
                if (sub != "") {
                  var s = sub.split(".");
                  for(var i = 0; i < s.length && s[i] !== ""; i++) {
                    if(cache[s[i]] === undefined) return;
                    cache = cache[s[i]];
                  }
                }
                if (typeof(handler) === "boolean") {
                  deep = handler;
                  handler = null;
                }
                
                if (deep) {
                  _deepUnsubscribe(cache, handler);
                } else {
                  _unsubscribe(cache, handler);
                }
                return bindTo;
              };
            };


          /*
          End of PubSub.js code

          PubSub.js 
          Copyright 2011 by Steven Littiebrant - changes and communication about use 
          appreciated but not required: https://github.com/Groxx/PubSub
        */

          PubSub(π.events, true);

          // create π.subscribe as an alias for π.events.subscribe
          π.subscribe = π.events.subscribe;

          π.events._loaded = true;




    /*    end of core modules     */






    π.debug = function(msg, obj) {

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
     * Listen to an address in the global namespace via EventSource/SSE
     * 
     * @param  {string}     address   Address in the pi namespace to listen to
     * @param  {Function}   onerror   Callback on error
     * @param  {Function}   callback  Callback for each message
     * @return {void}
     */


    π.listen = function (address, callback, onerror) {

      if( typeof callback !== "function" ) {
        return false; 
      }

      var
        source  = new EventSource('/api/pi.io.sse.monitor.php' + ((address!='') ? '?address=' + encodeURI(address) : ''));

      source.addEventListener('message',  callback, false);
      source.onmessage = callback;


      if( typeof onerror === "function" ) {
        source.addEventListener('error',    onerror,  false);
      }

      return source;
    };




    /** π.await
     *
     * Wait for named event, then trigger a given callback
     * If the event has not occurred within the given timeout, 
     * try to read the value directly. 
     * 
     * @param  {string}     eventaddress  Address in the pi namespace to wait for
     * @param  {Function}   onerror       Callback on error
     * @param  {Function}   onresult      Callback when return value available
     * @return {boolean}                  Should always return true
     */

    π.await = function(eventaddress, onresult, timeout){
    
      if(eventaddress.substring(0,7)==='pi.app.') {
        // await named event locally
        π.events.subscribe(eventaddress, onresult);
      }
      else {
        // request a named event from the server
        π._send("await", eventaddress, timeout, onresult);
      }
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
    
      return π._send("read", address, null, onresult);
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

      return π._send("write", address, value, onresult);
    };



    /** π._send
     *
     * Handle app request for sending a message to an address in the pi namespace
     * Conform to pi packet specification
     *
     * 
     * @param  {string}     command   pi command to issue
     * @param  {string}     address   Address in the pi namespace to read from
     * @param  {object}     data      The data to send
     * @param  {Function}   onresult  Callback when return value available
     * @return {boolean}              Result if success, false if failure
     */

    π._send = function(command, address, data, onresult){
      var
        packet = {
          command   : command, 
          address   : address, 
          data      : data, 
          callback  : π.core.callback.add(onresult)
        };

        if(!!π.session._loaded) {
          pi.log("Sending packet:", packet);
          // will return true or false
          return π.session.send(packet);
        }
        else {
          pi.log("pi.session not loaded! Packet:", packet);
          return false;
        }
    };


    /** π.readfile
     *
     * Read a remote (text) file
     * 
     * @param  {string}     fileaddress   File address in the pi namespace
     * @param  {string}     filetype      The file extension
     * @param  {Function}   callback      Callback for each return value available
     * @return {boolean}                  File contents on success, false on failure
     */

    π.readfile = function(fileaddress, filetype, onresult){

      var
        parameter = { fileaddress: fileaddress, filetype: filetype };
    
      // TBC
      return π._send("file.read", fileaddress, parameter, onresult);
    };


    /** π.require
     *
     * A simple dependency management system
     * 
     * @param  {string}     module    Name of the pi module to be loaded
     * @param  {boolean}    async     Load script asynchronously
     * @param  {boolean}    defer     Use deferred script loading
     * @param  {function}   callback  Callback on loaded
     * @param  {function}   __onerror Callback on error
     * @return {variant}              Result of insert operation
     */

    π.require = function(module, async, defer, callback, __onerror){


      // If this module has been loaded already, then
      // immediately invoke the callback and return true
      if (π.loaded[module.replace(/\./g,'_')]) {
        if(typeof callback==="function") {
          this.callback.call();
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
      script.π = {
          module    : module,
          modname   : module.replace(/\./g,'_'),
          callback  : callback  || false,
          __onerror : __onerror || false
        };

      pi.timer.start(module);

      script.onload = function (event) {
        var
          loadtime = π.timer.stop(this.π.modname);

        π.loaded[this.π.modname] = { time: (new Date()).getTime(), loadtime: loadtime };
        if(this.π.callback) {
          this.π.callback.call(event);
        }
      };

      script.onerror = function (error) {
        pi.log('error loading module: ' + this.π.module, error);
        if(this.π.__onerror) {
          this.π.__onerror.call(error);
        }
      };
      
      return cursor.insertBefore(script, cursor.firstChild); 
    };





    /*
      core support modules
    */


    π.timer = {
      
      timers : {},


      start : function(timerid, ontick, interval) {

        var

          // replace . with _
          id          = timerid.replace(/\./g,'_'),
          timers      = π.timer.timers,
          self        = π.timer.timers[id]  || false,
          events      = π.events            || false,
          ontick      = ontick              || false,
          interval    = interval            || 1000,
          tickid      = false;


        if(self) {
          pi.log("Warning: starting timer " + timerid + " for a second time. Results unpredictable.");
        }

        if(typeof ontick === "function") {
          tickid = setInterval(ontick, interval);
        }


        timers[id] = { id : timerid, start : (new Date()).getTime(), tickid : tickid };

        if(events.publish) {
          events.publish("pi.timer." + timerid, {event: "start", data: timers[id]});
        }
      },


      check : function(timerid) {
        var
          // replace . with _
          timer = π.timer.__items[timerid.replace(/\./g,'_')] || false;

        if(timer.start) {
          return (new Date()).getTime() - timer.start; 
        }
        return false;
      },

      stop : function(timerid) {
        var
          timers  = π.timer.timers,
          history = π.timer.history,
          self    = π.timer.timers[timerid.replace(/\./g,'_')] || false;

        if(!self) {
          π.events.publish("pi.timer.items." + timerid, "Warning: stopping non-existent timer \"" + timerid + "\". Results unpredictable.");
          pi.log("Warning: stopping non-existent timer " + timerid + ". Results unpredictable.");
          return false;
        }

        // is there an attached tick handler ?
        if(self.tickid) {
          // if yes, clear tick interval
          clearInterval(self.tickid);
          self.tickid = false;
          self.ontick = null;
        }
        self.stop = (new Date()).getTime();

        self.time = self.stop - self.start;

        history.add(self);

        // return timer value
        return self.time;
      },

      history : {
        
        log   : [],

        add : function (obj) {
          π.timer.history.log.push(obj);
          π.events.publish("event.pi.timer.add", obj);
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

          π.events.publish("event.pi.timer.history.clear", "");

          // clear log array, this is actually the fastest way
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


  π.log("Page loaded in " + ((new Date()).getTime() - π.__sessionstart) + " ms. Initializing pi...");

  // start a timer for the platform initialization
  π.timer.start("pi.initialization");


  pi.log("Loading core modules...");

  π.require("core.session", false, false, function (module) {
    // pi.log("loaded: core.session", module);
  });

  π.require("core.tasks", false, false, function (module) {
    // pi.log("loaded: core.session", module);
  });

  pi.log("Loading app modules...");

  π.require("app", false, false, function (module) {
    // pi.log("loaded: app", module);
  });

  π.require("pcl", false, false, function (module) {
    // pi.log("loaded: pcl", module);
  });



  π.log("Pi initialized in " + π.timer.stop("pi.initialization") + " ms.");
  

  window.addEventListener('load', function(e) {

      π.__onload();

      setTimeout(function() { window.scrollTo(0, 1); }, 1); /* a safari bug-fix, supposedly.*/

    }, false);
