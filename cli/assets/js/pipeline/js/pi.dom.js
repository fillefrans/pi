  /**
   *
   * π.dom
   *
   * DOM utilities for the Pi client
   *
   * @author Johan Telstad, jt@enfield.no, 2012-2014
   *
   * @copyright Johan Telstad, jt@enfield.no, 2012-2014
   * @copyright Views AS, 2014
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

    insertAfter : function (referenceNode, newNode) {
      referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
    },

    beginUpdate : function () {
      // like Lazarus
    },

    endUpdate : function () {
      // and again

    }

  };
  