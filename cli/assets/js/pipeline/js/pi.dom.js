  /**
   *
   * π.dom
   *
   * @author Johan Telstad, jt@enfield.no, 2011-2013
   *
   */


  var 
    π = π || {};

  π.dom = π.dom || {


    __init : function (DBG) {
      window.addEventListener("focus", this.__onfocus, false);
      window.addEventListener("blur", this.__onblur, false);
    },

    __onfocus : function (event) {
      
    },

    __onblur : function (event) {
      
    },

    beginUpdate() {
      // like Lazarus
    },

    endUpdate() {
      // and again

    }

  };
  