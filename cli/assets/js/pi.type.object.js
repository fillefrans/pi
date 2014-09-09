/**
 *
 * π.TYPE.object
 *
 * Implements Pi Type Library in JS
 * Also provides utility memory handling functions
 * and some binary memory operations
 *
 * @requires    HTML5, or typed array polyfill
 * @author Johan Telstad, jt@viewshq.no, 2011-2014
 */



 // we don't define π, since we want to fail immediately if π is not loaded



  function PiObject {

    this.self = this;





  }



  PiObject.prototype = {

    // define the pi object here
    type : null,
    SIZE : null,

    signed : null,
    nonnull : null,

    read : function () {
      pi.log("read");
    },

    toString : function() {
      return "pi";
    }

  };


/*

  example

function MyObject(arg1, arg2) {
    // this refers to the new instance
    this.arg1 = arg1;
    this.arg2 = arg2;

    // you can also call methods
    this.funca(arg1);
}

MyObject.prototype = {
 funca : function() {
  // can access `this.arg1`, `this.arg2`
 },

 funcb : function() {
  // can access `this.arg1`, `this.arg2`
 }
};

*/

