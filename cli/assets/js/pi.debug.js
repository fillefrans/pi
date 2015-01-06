/**
 *
 * π.debug
 *
 * @author Johan Telstad, jt@viewshq.no, 2011-2014
 *
 */


  // This declaration is so we can load before pi.core.js if needed.
  // Otherwise, we would omit declaration of π and provoke an error
  // if pi.core.js is not loaded, which is the behaviour we want.
 var
   π = π || {};


  π.debug = π.debug || {
    _loaded : false,


    printf : function() {

    },

    phonehome : function(host, port, ns, app, id, msg, obj) {
      var
        data = obj || false;



    }

  }


  π.debug._loaded = true;
