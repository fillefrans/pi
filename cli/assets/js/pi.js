/**
 *
 * π
 *
 * @author @copyright Johan Telstad, jt@kroma.no, 2013
 *
 */

  var 
      π = π || {};


  /*  ----  Our top level namespaces  ----  */
    π.core        = π.core        || {loaded: false};
    π.events      = π.events      || {loaded: false};
    π.srv         = π.srv         || {loaded: false};
    π.app         = π.app         || {loaded: false};
    π.session     = π.session     || {loaded: false};
    π.system      = π.system      || {loaded: false};
    π.debug       = π.debug       || {loaded: false};

    π.util        = π.util        || {loaded: false};
    π.plugins     = π.plugins     || {loaded: false};
    π.math        = π.math        || {loaded: false};
    π.statistics  = π.statistics  || {loaded: false};

    π.maverick    = π.maverick    || {loaded: false};


    π.core.__init = function(){
      console.log('init');
    };


    π.needs = function(module){
    
    var 
      argc = this.arguments.length,
      argv = this.arguments,

      cursor  = document.getElementsByTagName ("head")[0] || document.documentElement,
      path    = '../../assets/js/pi.',
      script  = document.createElement('script');

      script.async  = false;
      script.defer  = true;
      script.src    = path + module + '.js';
      script.self   = script;

      for( var i=0; i<argc, i++; ) {
        console.log( 'needs: ' + argv[i] );
      }

      script.onload = function (event) {
        console.log('script.onload: ', this.src);
        console.log('this.self: ', this.self);
      };

      script.onerror = function (error) {
        console.error('script.onerror: ', this.src);
        console.error(error);
      };


      // this might not always work. maybe.
      var node = cursor.insertBefore(script, cursor.firstChild); 

      console.log("Node: ", node);
      return node;
    };



/***   ------   INITIALIZATION    ------
   *
   *  Code we run after having created the π object.
   *
   */



  window.addEventListener('load', function(e) {
      setTimeout(function() { window.scrollTo(0, 1); }, 1);
    }, false);
