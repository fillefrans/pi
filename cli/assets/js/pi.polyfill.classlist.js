/**
 * A polyfill for classList in IE9 (and only IE9)
 * @module pi.polyfill.classList
 *
 * @author Johan Telstad, 2015
 * @copyright Views AS
 */


  if (!"classList" in document.createElement("_") && 'Element' in window) {
    // polyfill for IE 9 
    // (IE lt 9 does not support accessing the prototype of Element in this way)
    Element.prototype.classList = {
      
      /**
       * Adds a class to an element's list of classes. 
       * If class already exists in the element's list of classes, 
       * it will not add the class again.
       * 
       * @param  {string}  c  Class name to add
       * @return {void}       Nothing
       */
      add : function (c) {
        var
          idx,
          list = this.className.split(' ');
        if (list && list.length) {
          idx = list.indexOf(c);
          if (idx === -1) {
            list.push(c);
            this.className = list.join(" "); 
          }
        }
      },

      /**
       * Removes a class from an element's list of classes. 
       * If class does not exist in the element's list of classes, 
       * it will not throw an error or exception.
       * 
       * @param   {string}  c   Class name
       * @return  {void}        Returns nothing
       */
      remove : function (c) {
        var
          idx,
          list = this.className.split(' ');
        if (list && list.length) {
          idx = list.indexOf(c);
          if (idx>-1) {
            list.splice(idx);
            this.className = list.join(" "); 
          }
        }
      },

      /**
       * Toggles the existence of a class in an element's list of classes
       * @param   {string}    c   Class name
       * @param   {bool}      f   Force, how to enforce setting  
       * @return  {void|bool}     void {OR} If force is TRUE, then TRUE if class is added, and FALSE if it's removed 
       */
      toggle : function (c, f) {
        var
          f = f || false,   // if f, then class will be added but not removed
                            // if f, then class will be removed but not added
                            // if NOT f, then class will be removed but not added
          l = this.className.split(" "),
          i = l.indexOf(c)+1;

        // remove if it's there, add if it's not.
        i ? l.splice(i-1) : l.push(c);

        // update the class list
        this.className = l.join(" ");

        // return TRUE if we added class, false if we removed it
        return !i;
      },

      /**
       * Checks if an element's list of classes contains a specific class
       * @param   {string}  c         Class name
       * @return  {bool}              [description]
       */
      contains : function (c, optional) {
        return !!(this.className.split(" ").indexOf(c)+1);
      }
    } // Element.prototype.classList {}
  }
