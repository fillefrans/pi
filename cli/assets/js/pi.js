/**
 *
 * π
 *
 * @author @copyright Johan Telstad, jt@kroma.no, 2013
 *
 */


  var 
      π  = π  || {},
      pi = pi || π;


  /*  ----  Our top level namespaces  ----  */
    π.events      = π.events      || { _self: this, _loaded: false, _ns: 'events' };
    π.srv         = π.srv         || { _self: this, _loaded: false, _ns: 'srv' };
    π.app         = π.app         || { _self: this, _loaded: false, _ns: 'app' };
    π.pcl         = π.pcl         || { _self: this, _loaded: false, _ns: 'pcl' };
    π.session     = π.session     || { _self: this, _loaded: false, _ns: 'session' };
    π.system      = π.system      || { _self: this, _loaded: false, _ns: 'system' };
    π.debug       = π.debug       || { _self: this, _loaded: false, _ns: 'debug' };

    π.util        = π.util        || { _self: this, _loaded: false, _ns: 'util' };
    π.plugins     = π.plugins     || { _self: this, _loaded: false, _ns: 'plugins' };
    π.math        = π.math        || { _self: this, _loaded: false, _ns: 'math' };
    π.statistics  = π.statistics  || { _self: this, _loaded: false, _ns: 'statistics' };

    π.maverick    = π.maverick    || { _self: this, _loaded: false, _ns: 'maverick' };


    π.APP_ROOT    = "assets/js/";
    π.LIB_ROOT    = "../../assets/js/";
    π.SRV_ROOT    = "../../../srv/";
    π.PHP_ROOT    = π.SRV_ROOT + "php/";
    π.FPC_ROOT    = π.SRV_ROOT + "fpc/";
    

    // switches pi.log and pi.debug
    π.DEBUG = true;

    //keeps a running list over which modules are loaded
    π.loaded = [];


    /*
      global support functions
    */

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
            pi.forEachObj(obj, recurse);
          }
        };

      pi.forEachObj(pi, recurse);
    };

    π.debug = function(msg, obj) {

      var 
        caller = this;

      console.log("look at ", this);

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


    pi.inject = function (src, elem) {
      var 
        element   = elem || document.body,
        fragment  = document.createDocumentFragment(),
        container = document.createElement("div");
       
      container.innerHTML = src;
      fragment.appendChild(container);
      element.appendChild(fragment);
    };


    π.require = function(module, async, defer){
    
      pi.debug("loaded[" + module + "] : ", π.loaded[module]);

      if (π.loaded[module]) {
        return true;
      }
  
      var 
        cursor  = document.getElementsByTagName ("head")[0] || document.documentElement,
        path    = '../../assets/js/pi.',
        script  = document.createElement('script'),
        mod     = module;


      pi.log('loading module (' + (!!async ? "async" : "sync") + '): pi.' + module);

      script.async  = async || true;
      script.defer  = defer || true;
      script.src    = path + module + '.js';
      script.self   = script;
      script.module = module;


      script.onload = function (event) {
        pi.log('loaded:', this.module);
      };

      script.onerror = function (error) {
        pi.log('error loading module: ' + this.module, error);
      };

      var
        node = cursor.insertBefore(script, cursor.firstChild);
      
      if(!!node) {
        pi.loaded.push(module);
      }
      return !!node; 
    };



/***   ------   INITIALIZATION    ------
   *
   *  Code we run after having created the π object.
   *
   */


  // this is a simple array holding the names of modules we have loaded
  π.loaded = [];

  // look for pcl components
  π.app.components = document.getElementsByClassName("pcl");

  if(π.app.components.length>0) {
    // we have components, so it's an app
    var suffix = (π.app.components.length == 1) ? "" : "s";
    pi.log("found " +  π.app.components.length + " pcl component" + suffix + " on page");
    // load modules for a web app with session support
    π.require("app");
    π.require("app.session");
    π.require("pcl");
  }


  window.addEventListener('load', function(e) {
      setTimeout(function() { window.scrollTo(0, 1); }, 1);
    }, false);
