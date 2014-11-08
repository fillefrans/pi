  /**
   *
   * π v0.5.2.1
   *
   * Pi is an html5-based distributed client-server application platform.
   * 
   * This is the client part.
   *
   * @author Johan Telstad, jt@viewshq.no
   * 
   * @copyright Johan Telstad, 2011-2014
   * @copyright Views AS, 2014-
   * 
   * @uses     PubSub.js  -  https://github.com/Groxx/PubSub 
   *           @copyright 2011 by Steven Littiebrant
   *
   */


  var 
      π  = π  || {};


  /*  ----  Our top level namespaces  ----  */


    // The core modules
    π.core        = π.core        || { _loaded: false, _ns: 'core'      };
    π.callback    = π.callback    || { _loaded: false, _ns: 'callback'  };
    π.session     = π.session     || { _loaded: false, _ns: 'session'   };
    π.events      = π.events      || { _loaded: false, _ns: 'events'    };
    π.tasks       = π.tasks       || { _loaded: false, _ns: 'tasks'     };
    π.timer       = π.timer       || { _loaded: false, _ns: 'timer'     };


    // The built-in libraries
    π.srv         = π.srv         || { _loaded: false, _ns: 'srv'       };
    π.app         = π.app         || { _loaded: false, _ns: 'app'       };
    π.pcl         = π.pcl         || { _loaded: false, _ns: 'pcl'       };
    π.system      = π.system      || { _loaded: false, _ns: 'system'    };
    π.debug       = π.debug       || { _loaded: false, _ns: 'debug'     };
    π.io          = π.io          || { _loaded: false, _ns: 'io'        };
    π.file        = π.file        || { _loaded: false, _ns: 'file'      };



    // For extending the platform
    π.lib         = π.lib         || { _loaded: false, _ns: 'lib'       };
    π.util        = π.util        || { _loaded: false, _ns: 'util'      };
    π.plugins     = π.plugins     || { _loaded: false, _ns: 'plugins'   };
    π.maverick    = π.maverick    || { _loaded: false, _ns: 'maverick'  };



    π._const = π._const || {

      // paths
      PI_ROOT     : "assets/js/",
      LIB_ROOT    : "../../assets/js/",
      API_ROOT    : "/api/",
      SRV_ROOT    : "../../../srv/",

      LOG_URL     : "/api/log/",

      // platform constants
      TWEEN_TIME      : 0.2,
      DEFAULT_TIMEOUT : 30
    };



    //will keep an updated list over which modules are loaded
    π.loaded = π.loaded || {};


    // create pi as an alias for π
    var 
      pi = π;


    /*    begin core modules     */



       /**
        * 
        * @module π.core.callback
        *
        *   Store references to local callback functions
        *   Call remote procedure and create a listener for the result
        *   Invoke local callback when result arrives
        * 
        */

          π.core.callback = π.core.callback || {

            /**
             * @description  Manages callback handlers
             *
             * Issues reply addresses, and invokes related
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
              if (typeof callback != "function") {
                pi.log("Error: Tried to add non-function as callback:", callback);
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

              if (item && (typeof item.callback == "function")) {        

                // pi.log("invoking callback " + id + " after " + ( (new Date().getTime()) - item.timestamp ) + "ms");
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





        /**
         * This is where we optimize. Absolutely no blocking code allowed.
         *
         * This is the client side hub of our messaging system.
         * It handles data routing, message passing and events independent of
         * the DOM, using a pubsub approach to implement the Observer Model.
         * 
         * This is 10-100x faster than using DOM events, or the jQuery approach.
         *
         * @author Johan Telstad, jt@viewshq.no, 2011-2014
         * 
         * @uses     PubSub.js  -  https://github.com/Groxx/PubSub 
         *           @copyright 2011 by Steven Littiebrant
        **/


          π.events = π.events || {};


        /** 
          PubSub.js
         
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
              
              if ( typeof uniqueName == "string" ) {
                unique = uniqueName;
              } 
              else if ( typeof alsoPassPath_or_unique == "string" ) {
                unique = alsoPassPath_or_unique;
              } 
              else if ( typeof obj_or_alsoPassPath_or_unique == "string" ) {
                unique = obj_or_alsoPassPath_or_unique;
              }

              if ( typeof alsoPassPath_or_unique == "boolean" ) {
                passPath = alsoPassPath_or_unique;
              } else if (typeof obj_or_alsoPassPath_or_unique == "boolean") {
                passPath = obj_or_alsoPassPath_or_unique;
              }
              
              if (typeof obj_or_alsoPassPath_or_unique == "object" || typeof obj_or_alsoPassPath_or_unique == "function") {
                bindTo = obj_or_alsoPassPath_or_unique;
              }
              
              // all subscriptions, nested.
              var subscriptions = {};
              subscriptions[unique] = [];
              
              // Removes all instances of handler from the passed subscription chunk.
              var _unsubscribe = function(cache, handler) {
                for(var i = 0; i < cache[unique].length; i++) {
                  if (handler === undefined || handler === null || cache[unique][i] === handler) {
                    cache[unique].splice(i, 1);
                    i--;
                  }
                }
              };
              
              // Recursively removes all instances of handler from the passed subscription chunk.
              var _deepUnsubscribe = function(cache, handler) {
                for(sub in cache) {
                  if (typeof cache[sub] != "object" || sub === unique || !cache.hasOwnProperty(sub)) continue;
                  _deepUnsubscribe(cache[sub], handler);
                }
                _unsubscribe(cache, handler);
              };
              
              // Calls all handlers on the path to the passed subscription.
              // ie, "a.b.c" would call "c", then "b", then "a".
              // If any handler returns false, the event does not bubble up (all handlers at that level are still called)
              bindTo.publish = function(sub, callback_args) {
                var 
                  args = null;

                if (arguments.length > 2) {
                  pi.log("grabbing arguments array");
                  // If passing args as a set of args instead of an array, grab all but the first.
                  args = Array.prototype.slice.apply(arguments, [1]);
                } else {
                  args = [callback_args];
                  // pi.log("args is not an array : " + JSON.stringify(callback_args), callback_args);
                } 
                
                var 
                  cache = subscriptions,
                  stack = [];

                sub = sub || "";
                var s = sub.split(".");
                if (passPath) args.push(s);
                stack.push(cache);
                for(var i = 0; i < s.length && s[i] !== ""; i++) {
                  if (cache[s[i]] === undefined) break;
                  cache = cache[s[i]];
                  stack.push(cache);
                }
                var c;
                var exit = false;
                while((c = stack.pop())) {
                  for(var j = 0; j < c[unique].length; j++) {
                    if (c[unique][j].apply(this,args) === false) exit = true;
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
                    if (cache[s[i]] === undefined) return;
                    cache = cache[s[i]];
                  }
                }
                if (typeof handler == "boolean") {
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

          // attach it to pi.events
          PubSub(π.events, false);


          // public functions


          /**
           * Wrapper for custom event triggering with
           * browser's internal event system
           *
           * @param  {String}       eventName   The event name
           * @param  {Object}       eventData   Optional data object
           * @param  {HTMLElement}  eventElem   Optional DOM element to use as dispatcher
           * 
           * @return {bool}                     Boolean FALSE on failure, or result from dispatchEvent()
           */
          
          π.events.trigger = function ( eventName, eventData, eventElem ) {
            var
              eventName   = eventName || false,
              eventData   = eventData || null,
              dispatcher  = eventElem || window,
              customEvt   = null;

            // early escape
            if (!eventName) {
              return false;
            }


            // are we handicapped ?
            if (!window.CustomEvent) {
              try {
                customEvt = document.createEvent("CustomEvent");
                if (eventData) {
                  customEvt.initCustomEvent(eventName, false, false, eventData);
                }
                else {
                  customEvt.initCustomEvent(eventName, false, false, {});
                }
                dispatcher.dispatchEvent(customEvt);
              }
              catch(e) {
                pi.log('Exception : ', e);
              }

            }
            else {
              // we are not handicapped

              if (eventData) {
                // event has a payload
                dispatcher.dispatchEvent(new CustomEvent( eventName, { detail : eventData } ));
              } else {
                // event is only named
                dispatcher.dispatchEvent(new CustomEvent(eventName));
              }
            }

          };



          // set up aliases for the trigger function
          π.events.emit     = π.events.trigger;
          π.events.dispatch = π.events.trigger;


          π.events._loaded = true;


    /*    end of core modules     */



    π.browser = π.browser || {};

    /**
     * Regex test for Internet Explorer
     * 
     * @param  {int}  v     Version to check for
     * 
     * @return {Boolean}    Returns true if browser is IE
     */
    π.browser.isIe = function (v) {
      return RegExp('msie' + (!isNaN(v) ? ('\\s' + v) : ''), 'i').test(navigator.userAgent);
    };

    /**
     * Regex test for handheld devices (smartphones + tablets)
     * 
     * @return {Boolean} True if device is a handheld, false otherwise
     */
    π.browser.isMobile = function(){
      return /ip(hone|od|ad)|android|blackberry.*applewebkit|bb1\d.*mobile/i.test(navigator.userAgent);
    }



    /*    JS versions of PHP utility functions. (PHP under_score => JS camelCase)    */

    /**
     * JS version of PHP's is_array()
     * 
     * @param  {Object}  obj Object reference
     * 
     * @return {Boolean}     Boolean TRUE if obj is a native JS array
     */
    π.isArray = function(obj) {
      return (Object.prototype.toString.call(obj) == "[object Array]");
    }


    /**
     * JS version of PHP's str_pad()
     * 
     * @param  {string} str       The string to pad
     * @param  {int}    padto     Desired length
     * @param  {string} padstr    Pad with this string
     * @param  {bool}   padleft   Flag to pad string from the left (default i pad from right)
     * 
     * @return {string}     The padded string
     */
    π.strPad = function(str, padto, padstr, padleft) {
      var
        padstr  = padstr  || "&nbsp;",
        padto   = padto   || false,
        padleft = padleft || false, // default is to pad on the right
        count   = 0,
        result  = str;

      count = padto - str.length;

      if (count <= 0 || padto === false) {
        return str;
      }

      for(;count--;) {
        if (padleft) {
          result = padstr + result;
        }
        else {
          result += padstr;
        }
      }

      return result;
    };


    /**
     * JS version of PHP's basename()
     * 
     * @param  {string} filename The full path and filename
     * @param  {string} ext      The extension to trim
     * 
     * @return {string}          The full path without the filename part
     */
    π.basename = function (filename, ext) {
      var
        filename  = filename || null,
        ext       = ext || "",
        token     = "",
        slashpos  = -1;


      if (filename.lastIndexOf("/") == filename.length-1) {
        filename.length--;
      }

      slashpos = filename.lastIndexOf("/");
      if (slashpos > -1) {
        token = filename.substring(slashpos+1);
        // pi.log("new token : " + token);
      }
      else {
        token = filename;
        // pi.log("token : " + token);
      }

      if (ext && typeof ext === "string") {
        var strlen = token.length;
        if (token.lastIndexOf(ext) == (strlen - ext.length)) {
          // pi.log("That's our baby : " + token.lastIndexOf(ext));
          token = token.substring(0, token.lastIndexOf(ext));
        }
        else {
          // pi.log("not our baby : " + token.lastIndexOf(ext));
        }
      }

      // pi.log("returning : " + token);
      return token;

    };



    // π utility functions


    π.logArray = function (array) {
      var
        i = array.length;

      while(i--) {
        // π.strPad = function(str, padto, padstr) {
        pi.log(pi.strPad(i, 4, " ") + " : " + array[i]);
      }
    };


    π.logObject = function (obj) {
        pi.log("Object : ", obj);
    };


    π.log = function(msg, obj) {

      if (!!obj) {
        console.log(msg, obj);
      }
      else {
        if (π.isArray(msg)) {
          pi.logArray(msg);
        }
        else if (typeof msg === "object") {
          π.logObject(msg);
        }
        else {
          console.log(msg);
        }
      }
    };



    // π.search = function (token, obj, where, exact, multiple) {
    //   var
    //     result    = null,
    //     multiple  = multiple  || false,
    //     token     = token     || null,
    //     obj       = obj       || null,
    //     exact     = exact     || 1, // 1 => match exactly, 0 => match any occurrence
    //     where     = where     || 0; // 0 => search both keys and values, 1 => keys, 2 => values

    //   if ( !obj || !token ) {
    //     pi.log("no obj");
    //     return false;
    //   }

    //   for (var item in obj) {

    //     if (!obj.hasOwnProperty(item)) {
    //       pi.log("skipping : " + item.substring(0, 64));
    //       continue;
    //     }

    //     if (where === 1 || where === 0) {
    //       if ( exact === 1 && item == token ) {
    //         pi.log("exact: " + item.substring(0, 64));
    //         pi.log("returning : ", obj);
    //           result = obj;
    //         return obj;
    //       }
    //       else if ( exact === 0 && item.indexOf(token) != -1) {
    //         pi.log("yes: " + item.substring(0, 64));
    //         pi.log("returning : ", obj);
    //           result = obj;
    //         return obj;
    //       }
    //       else {
    //         // pi.log("no: " + item.substring(0, 64));
    //         result = false;
    //       }
    //     }

    //    if ( typeof obj[item] == "object" ) {

    //       pi.log("searching object " + item + " for " + token);
    //       result = π.search(token, obj[item], where, exact);
    //       pi.log("result : " + result);

    //     }
    //     else {

    //       if (where === 2 || where === 0) {

    //         if ( exact == 1 && obj[item] == token ) {
    //           pi.log("exact: " + obj[item].toString().substring(0, 64));
    //           pi.log("returning : ", obj);
    //           result = obj;
    //           return obj;
    //         }
    //         else if ( exact == 0 && obj[item].toString().indexOf(token) != -1) {
    //           pi.log("yes: " + obj[item].toString().substring(0, 64));
    //           pi.log("returning : ", obj);
    //           result = obj;
    //           return obj;
    //         }
    //         else {
    //           // pi.log("no: " + obj[item].toString().substring(0, 64));
    //           result = false;
    //         }
    //       }

    //     }


    //   } // for var item in obj


    //   return result;

    // };



    /**
     * Search an object and its children
     * 
     * @param  {string} token     Search string
     * @param  {Object} obj       The object to search
     * @param  {int}    where     Flag: 0 => search both keys and values, 1 => keys, 2 => values
     * @param  {int}    exact     Flag: 1 => match exactly, 0 => match any occurrence
     * @param  {bool}   multiple  Flag: Return multiple matches
     * 
     * @return {Object|bool}      Mathing Object(s), or boolean FALSE if not found.
     */
    π.search = function (token, obj, where, exact, multiple) {
      var
        result    = null,
        multiple  = multiple  || false,
        token     = token     || null,
        obj       = obj       || null,
        exact     = exact     || 1, // 1 => match exactly, 0 => match any occurrence
        where     = where     || 0; // 0 => search both keys and values, 1 => keys, 2 => values

      if ( !obj || !token ) {
        pi.log("no obj");
        return false;
      }

      for (var item in obj) {

        if (where === 0 || where === 1) {
          if (exact) {
            if (item == token) {
              return obj[item];
            }
          }
          else {
            if (item.indexOf(token)>-1) {
              return obj[item];
            }
          }
        }

        // recursion part
        if (typeof obj[item] == "object") {

          result = pi.search(token, obj[item], where, exact, multiple);
          if (!result) {
            continue;
          }
          else {
            return result;
          }
        }

        if (!obj.hasOwnProperty(item)) {
          pi.log("skipping : " + item.substring(0, 64));
          continue;
        }

        if (where === 2 || where === 0) {

          if ( exact == 1 && obj[item].toString() == token ) {
            pi.log("exact: " + obj[item].toString().substring(0, 64));
            pi.log("returning : ", obj);
            result = obj;
            return obj;
          }
          else if ( exact == 0 && obj[item].toString().indexOf(token) != -1) {
            pi.log("yes: " + obj[item].toString().substring(0, 64));
            pi.log("returning : ", obj);
            result = obj;
            return obj;
          }
          else {
            // pi.log("no: " + obj[item].toString().substring(0, 64));
            result = false;
          }
        }

      } // for var item in obj

      return result;

    };





    /* DOM-related functions  */


    /**
     * Returns TRUE if obj is a DOM Node
     * 
     * @param  {DOMElement}  obj    The DOM reference to check
     * 
     * @return {bool}               Boolean TRUE if param is a DOM Node, FALSE otherwise.
     */
    π.isNode = function(obj){
      return (
        typeof Node === "object" ? obj instanceof Node : 
        obj && typeof obj === "object" && typeof obj.nodeType === "number" && typeof obj.nodeName==="string"
      );
    };



    /**
     * Returns true if obj is a DOM element
     * 
     * @param  {HTMLElement}  obj The element to check
     * 
     * @return {bool}     Returns true if it is a DOM element
     */
    π.isElement = function(obj){
      return (
        typeof HTMLElement === "object" ? obj instanceof HTMLElement : //DOM2
        obj && typeof obj === "object" && obj !== null && obj.nodeType === 1 && typeof obj.nodeName==="string" 
      );
    };





    /**
     *  Dynamically polyfill missing features
     *  
     * @param {string}      feature   The feature to polyfill
     * 
     * @param {DomElement}  elem      An optional DomElement to use for attaching. If this
     *                                variable is not present, 'window' will be used instead.
     *
     * @return {Boolean|DomElement}   False on failure, or new DomElement reference on success
     */
    π.polyfill = function (feature, elem) {
      var 
        feature = feature || null,
        elem    = elem    || window;

      if (feature in elem) {
        return;
      }

      pi.require('polyfill.' + feature.toLowerCase());

      return ;
    };




    /**
     *  Inject html source into the DOM
     *
     * @param {string} src The source to inject
     * 
     * Optional
     * @param {DomElement} elem An optional DomElement to use for injection. If
     * this variable is not present, document.body will be used instead.
     *
     * @return {Boolean|DomElement} False on failure, or new DomElement reference on success
     *  
     */

    π.inject = function (src, elem) {
      var 
        element   = elem || document.body,
        fragment  = document.createDocumentFragment(),
        container = document.createElement("div");

      // we do it this way to render the markup before we add it to the DOM
      container.innerHTML = src;
      fragment.appendChild(container);

      if ( elem && (elem != document.body) ) {
        π.clear(elem);
      }

      return element.appendChild(fragment);
    };



    /**
     * Remove any children from element
     * 
     * @param   {Object}  elem    The element to clear
     * 
     * @return  {bool|integer}    Number of children removed, or boolean FALSE on error
     */

    π.clear = function (elem) {
      var 
        element   = elem || null,
        removed   = 0;

      if (!π.isElement(elem)) {
        return false;
      }
       
      // clear element
      while (elem.firstChild) {
        elem.removeChild(elem.firstChild);
        removed++;
      }

      return removed;
    };



    /**
     * Bog-standard JS clone() function
     * 
     * @param  {Object}   obj   The object to clone
     * 
     * @return {Object}         Returns cloned object
     * @requires HTML5 [Array.forEach]
     */
    
    π.clone = function (obj){
      var
        clone = Object.create(Object.getPrototypeOf(obj)),
        props = Object.getOwnPropertyNames(obj);

      props.forEach(function(name){
        Object.defineProperty(clone, name, Object.getOwnPropertyDescriptor(obj, name));
      });

      return clone;
    };


    /**
     * Bog-standard JS create() function
     * 
     * @param  {Object}   obj   The object to create instance if
     * 
     * @return {Object}         Returns new instance of Object
     */
    
    π.create = function (obj){
      var 
        f = function() {};
      f.prototype = obj;
      return new f();
    };



    /**
     * [copy description]
     * @param  {[type]} obj        [description]
     * @param  {[type]} exceptions [description]
     * @return {[type]}            [description]
     */
    π.copy = function (obj, exceptions) {
      var
        obj         = obj         || false,
        exceptions  = exceptions  || false,
        newobj      = null;


      if (typeof obj == "string") {
        try {
          obj = JSON.parse(obj);
        }
        catch(e) {
          pi.log('Error in JSON.parse("' + obj + '")', e);
          return null;
        }
      }

      newobj = {};
      for (var i in obj) {
        if ( (i % 1 === 0) ) {
          // skip numerical indices
          // continue;
        }
        if (exceptions !== false) {
          if (exceptions.indexOf(i) >- 1) {
            continue;
          }
        }
        newobj[i] = obj[i];
      }
      return newobj;

    };




    /** 
     * Listen to an address in the global namespace via EventSource/SSE
     * 
     * @param  {string}     address   Address in the pi namespace to listen to
     * @param  {Function}   onerror   Callback on error
     * @param  {Function}   callback  Callback for each message
     * 
     * @return {Null|EventSource} New EventSource object on success, null on failure.
     */

    π.listen = function (address, callback, onerror) {

        // early escape
        if (!!address) {
          if (typeof callback != "function") {
            return false;
          }
        } 
        else {
          return false;
        }

      var
        source  = new EventSource( π._const.API_ROOT + 'pi.io.sse.monitor.php?address=' + encodeURI(address) );

      source.addEventListener('message',  callback, false);

      if (typeof onerror == "function") {
        source.addEventListener('error',    onerror,  false);
      }

      return source;
    };




    /** 
     * Listen to a data stream in the global namespace
     * 
     * @param  {string}     address   Address in the pi namespace to listen to
     * @param  {function}   onerror   Callback on error
     * @param  {function}   listener  Callback for stream data
     * 
     * @return {boolean}    Result of operation
     */

    π.readstream = function (address, listener, onerror) {
      if (!π.session._connected) {
        if (typeof onerror == "function") {
          onerror.call(this, "Error: No session in readstream().");
          return false;
        }
      }

      if (typeof listener == "function") {
        return π.session.addStreamListener(address, listener, onerror);
      }
      else {
        if (typeof onerror == "function") {
          onerror.call(this, "Error: Argument #2 is not a function in readstream().");
        }
        return false;
      }

    };




    /** 
     * Receive a data queue from the global namespace
     * 
     * @param  {string}     address   Address in the pi namespace to receive from
     * @param  {Function}   onerror   Callback on error
     * @param  {Function}   listener  Callback for data chunks
     * 
     * @return {Boolean}              Result of operation
     */

    π.readqueue = function (address, listener, onerror) {
      if (!π.session._connected) {
        if (typeof onerror == "function") {
          onerror.call(this, "Error: No session in readqueue().");
          return false;
        }
      }

      if (typeof listener == "function") {
        return π.session.addStreamListener(address, listener, onerror);
      }
      else {
        if (typeof onerror == "function") {
          onerror.call(this, "Error: No listener in readstream().");
        }
        return false;
      }

    };




  /**
   * Pi shorthand function, wraps window.addEventListener
   * 
   * @param  {string}   eventaddress The Pi Event Address to attach to
   * @param  {Function} callback     The event handler
   * @param  {bool}     capture      Capture events, or pass along to next handler in the event chain
   * 
   * @return {bool}     Result from addEventListener() 
   */
  
    π.on = function(eventaddress, callback, capture) {

      // if object, attach all functions by name
      if ( typeof eventaddress == "object" ) {
        var count = 0;
        for (var func in eventaddress) {
          if ( eventaddress.hasOwnProperty(func) && (typeof eventaddress[func] == "function") ) {
            count++;
            π.on(func, eventaddress[func], callback, capture || false);
          }
        }
        return count;
      }

      if (eventaddress.indexOf('pi.') !== 0) {
        eventaddress = "pi." + eventaddress;
      }

      return window.addEventListener(eventaddress, callback, capture);
    };


    // ALIAS
    π.bind = π.on;


    /** 
     * Wait for single named event
     * 
     * @param  {string}     eventaddress  Address in the pi namespace to wait for
     * @param  {Function}   callback      Callback when return value available
     * @return {boolean}                  Should always return true
     */

    π.await = function(eventaddress, callback, timeout) {
      var
        eventaddress  = eventaddress  || false,
        timeout       = timeout       || π._const.DEFAULT_TIMEOUT;
      
      if ( typeof eventaddress != "string" ) {
        return false;
      }

      if ( eventaddress.substring(0,7) == 'pi.app.' ) {
        // await named event locally
        return π.events.subscribe(eventaddress, callback);
      }
      else {
        // request a named event from the server
        return π._send("await", eventaddress, timeout, callback);
      }
    };




    /** 
     * Read a remote value
     *
     * @param  {string}     address   Address in the pi namespace to read from
     * @param  {Function}   onerror   Callback on error
     * @param  {Function}   callback  Callback when return value available
     * 
     * @return {boolean}              Result if success, false if failure
     */

    π.read = function(address, callback, onerror) {
    
      return π._send("read", address, null, callback || false);
    };



    /** 
     * Write a value to a remote variable location
     * 
     * @param  {string}     address   Address in the pi namespace to write to
     * @param  {Function}   onerror   Callback on error
     * @param  {Function}   callback  Callback when return value available
     * 
     * @return {boolean}              Old value if success, false if failure
     */

    π.write = function(address, value, callback) {

      return π._send("write", address, value, callback);
    };



    /**
     * Handle app request for sending a message to an address in the pi namespace
     * Conform to pi packet specification
     * 
     * @param  {string}     command   pi command to issue
     * @param  {string}     address   Address in the pi namespace to read from
     * @param  {object}     data      The data to send
     * @param  {Function}   callback  Callback when return value available
     * 
     * @return {boolean}              Result if success, false if failure
     */

    π._send = function(command, address, data, callback, onerror) {
      var
        packet = {
          command   : command, 
          address   : address, 
          data      : data
        };

        if (typeof callback == "function") {
          packet.callback = π.core.callback.add(callback);
        }

        if (π.session.connected) {
          // will return true or false
          return π.session.send(packet);
        }
        else {
          pi.log("pi.session not connected! Packet:", packet);
          return false;
        }
    };



    /** 
     *  List contents of remote address
     *
     * @param  {string}     address       [channel[:id]|]address
     * @param  {string}     filetype      The file extension
     * @param  {Function}   callback      Callback for each return value available
     * 
     * @return {string|boolean}           Data set on success, false on failure
     * 
     */

    π.readlist = function(address, callback, onerror) {

      var
        parameters = { address: address };

      if (typeof callback != "function") {
        pi.log("Error : callback is not a function in readlist().");
        if (typeof onerror == "function") {
          onerror.call(this, "callback is not a function in readlist().");
        }
        return false;
      }
    
      // TBC
      return π._send("list", address, parameters, callback, onerror);
    };




    /** 
     *  Read a remote data set (mysql|file)
     *
     * @param  {string}     address       Data address in the pi namespace
     * @param  {string}     filetype      The file extension
     * @param  {Function}   callback      Callback for each return value available
     * 
     * @return {string|boolean}           Data set on success, false on failure
     * 
     */

    π.readdata = function(address, callback, onerror) {

      var
        parameters = { address: address };

      if (typeof callback != "function") {
        pi.log("Error : callback is not a function in readdata().");
        if (typeof onerror == "function") {
          onerror.call(this, "callback is not a function in readdata().");
        }
        return false;
      }
    
      // TBC
      return π._send("data.list", address, parameters, callback, onerror);
    };



    /** 
     *  Read a remote (text) file
     * 
     * @param  {string}     fileaddress   File address in the pi namespace
     * @param  {string}     filetype      The file extension
     * @param  {Function}   callback      Callback for each return value available
     * @return {string|boolean}                  File contents on success, false on failure
     */

    π.readfile = function(fileaddress, filetype, callback) {

      var
        parameters = { fileaddress: fileaddress, filetype: filetype };
    
      // TBC
      return π._send("file.read", address, parameters, callback);
    };



    /** 
     *  Basic dependency management
     * 
     * @param  {string}     module    Name of the pi module to be loaded
     * @param  {boolean}    async     Load script asynchronously
     * @param  {boolean}    defer     Use deferred script loading
     * @param  {Function}   callback  Callback on loaded
     * @return {boolean}              True for success, false for failure
     */

    π.require = function(module, async, defer, callback, onerror) {
      var
        result = true;

          // handle multiple modules given on the form "module1 module2 ..."
          if (module.indexOf(" ") >=1) {
            var 
              modules = module.split(" ");
            for (var i=0; i<modules.length; i++) { result &= π.require(modules[i], async, defer, null, onerror); }
            if ( result && typeof callback === "function" ) { callback.call(this); }
            return true;
          }
          // already loaded => early escape
          if (π.loaded[module.replace(/\./g,'_')]) {
            if ( typeof callback === "function" ) { callback.call(this); } 
            return true;
          }

      var 
        cursor  = document.getElementsByTagName ("head")[0] || document.documentElement,
        path    = '../../assets/js/pi.',
        script  = document.createElement('script');


      script.async    = async || false;
      script.defer    = defer || false;
      script.src      = path + module + '.js';

      script.modname  = module.replace(/\./g,'_');
      script.callback = callback  || false;
      script.onerror  = onerror   || π.log;


      π.timer.start(module);

      script.onload = function () {
        π.loaded[this.modname] = { time: (new Date()).getTime(), loadtime: π.timer.stop(this.modname) };
        if (typeof this.callback === "function") {
          this.callback.call(this, this.modname);
        }
      };

      return !!cursor.insertBefore(script, cursor.firstChild); 
    };




    /*
      core support modules
    */

    /**
     *  Utility object for timing purposes
     * @author Johan Telstad
     */


    π.timer = {
      
      timers : {},


      start : function(timerid, ontick, interval) {

        var

          id          = timerid.replace(/\./g,'_'),
          timers      = π.timer.timers,
          self        = π.timer.timers[id]  || false,
          events      = π.events            || false,
          ontick      = ontick              || false,
          interval    = interval            || 1000,
          tickid      = false;


        if (self) {
          pi.log("Warning: starting timer " + timerid + " for a second time. Results unpredictable.");
        }

        if ( typeof ontick == "function" ) {
          tickid = setInterval(ontick, interval);
        }


        timers[id] = { id : timerid, start : (new Date()).getTime(), tickid : tickid };

        if (typeof events.publish == "function") {
          events.publish("pi.timer." + timerid + ".start", {event: "start", data: timers[id]});
        }
        if (typeof console.time == "function"){
          /// console.time(id);
        }
      },


      check : function(timerid) {

        var
          id          = timerid.replace(/\./g,'_'),
          timers      = π.timer.__items,
          self        = π.timer.__items[id] || false,
          events      = π.events            || false,
          ontick      = ontick              || false,
          interval    = interval            || 1000,
          tickid      = false;


        if (self) {
          pi.log("Warning: starting timer " + timerid + " for a second time. Results unpredictable.");
        }

        if (typeof ontick == "function") {
          tickid = setInterval(ontick, interval);
        }


        timers[id] = { id : timerid, start : (new Date()).getTime(), tickid : tickid };

        if (typeof events.publish == "function") {
          events.publish("pi.timer." + timerid + ".tick", {event: "tick", data: timers[id]});
        }
      },


      stop : function(timerid) {

        var
          timers  = π.timer.timers,
          history = π.timer.history,
          self    = π.timer.timers[timerid.replace(/\./g,'_')] || false;

        // if (typeof console.timeEnd == "function"){
        //   /// console.timeEnd(timerid);
        // }

        if (!self) {
          // π.events.publish("pi.timer.items." + timerid, "Warning: stopping non-existent timer \"" + timerid + "\". Results unpredictable.");
          pi.log("Warning: stopping non-existent timer " + timerid + ". Results unpredictable.");
          return false;
        }

        // is there an attached tick handler ?
        if (self.tickid) {
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
        },


        list  : function (callback) {
          var
            log = π.timer.history.log;


          log.forEach(function(value, index) {
            if (callback) {
              callback.call(index, value);
            }
            // pi.log("timer[" + value.id + "] : " + value.time + "ms.");
          });
        },


        clear : function () {
          var
            log = π.timer.history.log;

          π.events.publish("pi.timer.history.on", ["clear"]);

          // clear log array, this is actually the fastest way
          // NB! Array MUST NOT contain any falsy values, since that 
          // would break the loop before the array is cleared
          while (log.pop()) {};
        }
      } // end of history object

    }; // end of timer module




  /**
   * BOOTSTRAPPING THE DOM
   *
   * Search for ["data-src", "data-pi"] attributes starting with "pi."
   * Set classes based on found elements' pi address. ["pi.video" => addClass("pi video")]
   * 
   */


  π.__BOOT = {
    strapped : [],
 
    /**
     * Check if element has already been strapped
     * 
     * @param  {HTMLElement} e The element to check
     * 
     * @return {bool|int}   Boolean FALSE if already strapped, index of newly inserted cache item if not
     */
    checkIt : function (e) {
      var
        self = π.__BOOT;
      if (self.strapped.indexOf(e) > -1) {
        // elem was already strapped
        return false;
      }
      else {
        // return new length of strapped array
        return self.strapped.push(e);
      }
    },

    /**
     * Strap elements with classes from the data-src attribute, if set
     * 
     * @param  {HTMLElement} e The element to strap
     * 
     * @return {void}
     */
    strapIt : function (e) {
      var
        ref = e.getAttribute("data-src");

      if (typeof ref == "string" && ref.indexOf("pi.") === 0) {
        pi.log("adding classes : " + ref.replace(".", " "));
        e.className += ( e.className ? " " + ref.replace(".", " ") : ref.replace(".", " ") );
      }
    }
  };


  /**
   * Bootstrapper for DOM elements
   *
   * @return {void}
   * 
   * @todo Move this to a DOM-related module, the core should be DOM-independent.
   */
  π._bootstrap = function () {
    π.timer.start("_bootstrap");
    var
      elements,
      body = document.documentElement;

    elements = document.getElementsByClassName("pi");
    pi.log("BOOTSTRAP : " + elements.length);

    for (var i = 0; i < elements.length; i++) {
      // pi.log("STRAP IT: " + e, e);
      if (π.__BOOT.checkIt(elements.item(i))) {
        π.__BOOT.strapIt(elements.item(i));
      }
    }
    π.timer.stop("_bootstrap");

  };

  document.addEventListener('DOMContentLoaded',π._bootstrap);

  /***   ------   INITIALIZATION    ------
     *
     */


  /*    PHP aliases   */

  π.str_pad   = π.strPad;
  π.is_array  = π.isArray;

  π.require("core.session", false, false);
  // π.require("core.tasks",   false, false);

  π.require("app", false, false);
  // π.require("pcl", false, false);

  pi.events.trigger('pi', new Date().getTime());

  /* a safari bug-fix for iPhone, supposedly. */
  window.addEventListener('load', function(e) {
      setTimeout(function() { window.scrollTo(0, 1); }, 1);
    }, false);
