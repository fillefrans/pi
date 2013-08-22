/**   π.events.input  
 *
 *    This module handles user input, mouse, keyboard, touch, &c.
 *    It will handle data/message passing, events, and pubsub. In a hurry.
 *
 * @author Johan Telstad, jt@enfield.no, 2011-2013
 *
 * 
 * @uses     PubSub.js  -  https://github.com/Groxx/PubSub 
 *           @copyright 2011 by Steven Littiebrant
**/


  var π = π  || {};

 

  π.events = π.events || {

    /**
     * Event handlers
     * 
     */
     event : {
      touchmove : function(event) {
        console.log("touchmove");
        event.preventDefault();
      },

      touchstart : function(event) {
        console.log("touchstart");
        event.preventDefault();
      },

      touchend : function(event) {
        console.log("touchend");
        event.preventDefault();
      }
    }
  };


/***   ------   INITIALIZATION    ------  
   *
   *  Code we run after having created the event object.
   *
   */







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

