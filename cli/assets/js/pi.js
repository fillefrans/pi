/**
 *
 * π
 *
 * @author @copyright Johan Telstad, jt@kroma.no, 2013
 *
 */

  var 
      π = π || {},
      pi = pi || π;


  /*  ----  Our top level namespaces  ----  */
    π.events      = π.events      || { _self: this, _loaded: false, _parent: π, _ns: 'pi.events' };
    π.srv         = π.srv         || { _self: this, _loaded: false, _parent: π, _ns: 'pi.srv' };
    π.app         = π.app         || { _self: this, _loaded: false, _parent: π, _ns: 'pi.app' };
    π.pcl         = π.pcl         || { _self: this, _loaded: false, _parent: π, _ns: 'pi.pcl' };
    π.session     = π.session     || { _self: this, _loaded: false, _parent: π, _ns: 'pi.session' };
    π.system      = π.system      || { _self: this, _loaded: false, _parent: π, _ns: 'pi.system' };
    π.debug       = π.debug       || { _self: this, _loaded: false, _parent: π, _ns: 'pi.debug' };

    π.util        = π.util        || { _self: this, _loaded: false, _parent: π, _ns: 'pi.util' };
    π.plugins     = π.plugins     || { _self: this, _loaded: false, _parent: π, _ns: 'pi.plugins' };
    π.math        = π.math        || { _self: this, _loaded: false, _parent: π, _ns: 'pi.math' };
    π.statistics  = π.statistics  || { _self: this, _loaded: false, _parent: π, _ns: 'pi.statistics' };

    π.maverick    = π.maverick    || { _self: this, _loaded: false, _parent: π, _ns: 'pi.maverick' };



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
