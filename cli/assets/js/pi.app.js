/**
 * Minimal app bootstrapper for internal purposes
 *
 * Merges existing app object with new
 *
 */


    π.TMP = π.app;

    π.app = {

      PHP_ROOT   : π.PHP_ROOT,
      LIB_ROOT  : π.LIB_ROOT,
      IMG_ROOT  : π.IMG_ROOT,
      CSS_ROOT  : π.CSS_ROOT,

      self      : this,


      __init : function() {
        for(var key in pi.TMP) {
          this[key] = pi.TMP[key]
        }
        pi.TMP = null;
      },
    };

    π.app.__init();
    π.app._loaded = true;
