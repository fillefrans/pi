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



/***   ------   INITIALIZATION    ------
   *
   *  Code we run after having created the π object.
   *
   */



  window.addEventListener('load', function(e) {
    setTimeout(function() { window.scrollTo(0, 1); }, 1);
  }, false);

