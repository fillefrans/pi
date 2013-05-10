/**
 *
 * π.core
 *
 * @author Johan Telstad, jt@kroma.no, 2013
 *
 */

  var 
      π = π || {};

 
  /*  ----  Our top level namespaces  ----  */
    π.core        : {loaded: false};
    π.srv         : {loaded: false};
    π.app         : {loaded: false};
    π.system      : {loaded: false};
    π.debug       : {loaded: false};

    π.util        : {loaded: false};
    π.plugins     : {loaded: false};
    π.math        : {loaded: false};
    π.statistics  : {loaded: false};

    π.maverick    : {loaded: false};


    π.core.__init = function(){
      console.log('init');
    }

  };




/***   ------   INITIALIZATION    ------
   *
   *  Code we run after having created the π object.
   *
   */



  window.addEventListener('load', function(e) {
    setTimeout(function() { window.scrollTo(0, 1); }, 1);
  }, false);

