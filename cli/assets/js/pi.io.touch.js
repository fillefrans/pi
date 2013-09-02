  /**  π.io.touch  
   *
   *   Touch event handlers and utilities related to touch interfaces
   *
   * @author Johan Telstad, jt@enfield.no, 2011-2013
   *
  **/


  var π = π  || {};

  π.require("io");
 

  π.io.touch = π.io.touch || {

    /**
     * Touch event handlers
     * 
     */

    __touchmove : function(event) {
      console.log("touchmove");
      event.preventDefault();
    },

    __touchstart : function(event) {
      console.log("touchstart");
      event.preventDefault();
    },

    __touchend : function(event) {
      console.log("touchend");
      event.preventDefault();
    }

  };



/***   ------   INITIALIZATION    ------  
   *
   *  Code we run after having created the π.io.touch object.
   *
   */


  document.body.addEventListener('touchmove',   π.io.touch.__touchmove,   false); 
  document.body.addEventListener('touchstart',  π.io.touch.__touchstart,  false); 
  document.body.addEventListener('touchend',    π.io.touch.__touchend,    false); 



/* Touch events documentation .. 

  touchstart: a finger is placed on a DOM element.
  touchmove: a finger is dragged along a DOM element.
  touchend: a finger is removed from a DOM element.

  Each touch event includes three lists of touches:
    touches         : A list of all fingers currently on the screen.
    targetTouches   : A list of fingers on the current DOM element.
    changedTouches  : A list of fingers involved in the current event. 
                      For example, in a touchend event, this will be the finger that was removed.

  These lists consist of objects that contain touch information: 

    identifier        : a number that uniquely identifies the current finger in the touch session.
    target            : the DOM element that was the target of the action.
    cli/screen coords : where on the screen the action happened.
    radius
      coords and 
      rotationAngle   : describe the ellipse that approximates finger shape.
  */

