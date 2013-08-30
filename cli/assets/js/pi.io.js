/**   π.io  
 *
 *    This is the client side of our interface to the server filesystem
 *    with upload/download and 
 *
 * @author Johan Telstad, jt@enfield.no, 2011-2013
 *
 * 
**/


  var π = π  || {};

 

  π.io = π.io || {

    /**
     * Default event handlers attached to the document body
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
    },


    __click   : function(event) {
      console.log("click");
      event.preventDefault();
    },


    __dblclick    : function(event) {
      console.log("dblclick");
      event.preventDefault();
    },


    __mousedown   : function(event) {
      console.log("mousedown");
      event.preventDefault();
    },


    __mousemove   : function(event) {
      console.log("mousemove");
      event.preventDefault();
    },


    __mouseup     : function(event) {
      console.log("mouseup");
      event.preventDefault();
    },


    __keydown     : function(event) {
      console.log("keydown");
      event.preventDefault();
    },


    __keypress    : function(event) {
      console.log("keypress");
      event.preventDefault();
    },


    __keyup   : function(event) {
      console.log("keyup");
      event.preventDefault();
    },






    /**
     * Public utility functions
     * 
     */

     dir : function(path) {

     },


     /**
      * string file_get_contents ( string $filename [, bool $use_include_path = false [, resource $context [, int $offset = -1 [, int $maxlen ]]]] )
      */

     file_get_contents : function (filename) {

     },



     file_put_contents : function (filename, data, FILE_APPEND) {
        var

          FILE_APPEND = FILE_APPEND || true;

    /**
     * I don't think we really need this function as of yet
     *
     */

     }



  };



/***   ------   INITIALIZATION    ------  
   *
   *  Code we run after having created the io object.
   *
   */


  document.body.addEventListener('touchmove',   π.io.__touchmove,   false); 
  document.body.addEventListener('touchstart',  π.io.__touchstart,  false); 
  document.body.addEventListener('touchend',    π.io.__touchend,    false); 


  document.body.addEventListener('click',       π.io.__click,       false); 
  document.body.addEventListener('dblclick',    π.io.__dblclick,    false); 

  document.body.addEventListener('mousedown',   π.io.__mousedown,   false); 
  document.body.addEventListener('mousemove',   π.io.__mousemove,   false); 
  document.body.addEventListener('mouseup',     π.io.__mouseup,     false); 


  document.body.addEventListener('keydown',     π.io.__keydown,     false); 
  document.body.addEventListener('keypress',    π.io.__keypress,    false); 
  document.body.addEventListener('keyup',       π.io.__keyup,       false); 







/*

Mouse EventsProperty    Description DOM
onclick The event occurs when the user clicks on an element 2
ondblclick  The event occurs when the user double-clicks on an element  2

onmousedown The event occurs when a user presses a mouse button over an element 2
onmousemove The event occurs when the pointer is moving while it is over an element 2
onmouseover The event occurs when the pointer is moved onto an element  2
onmouseout  The event occurs when a user moves the mouse pointer out of an element  2
onmouseup   The event occurs when a user releases a mouse button over an element    2

Keyboard EventsAttribute    Description DOM
onkeydown   The event occurs when the user is pressing a key    2
onkeypress  The event occurs when the user presses a key    2
onkeyup The event occurs when the user releases a key   2


 */




/* touch documentation .. 

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

