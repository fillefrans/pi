  /**
   *
   * π.dom
   *
   * @author Johan Telstad, jt@kroma.no, 2013
   *
   */


  var 
    π = π || {};

  π.dom = π.dom || {


    __init : function (DBG) {
      window.addEventListener("resize", this.__onresize, false);
    },

    __onresize : function (event) {
      
    }

  };
  