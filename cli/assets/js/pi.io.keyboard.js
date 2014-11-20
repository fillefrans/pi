    /**
     *  π.io.keyboard
     *
     * Support library for handling keyboard events
     *
     * Part of the core pi libraries
     * 
     * @author Johan Telstad
     * 
     * @copyright Johan Telstad, 2011-20144 
     * 
     */

    var
        π = π || {};



    π.io = π.io || {};

    π.io.keyboard = π.io.keyboard || {
      keys : {
        BACKSPACE: 8,
        TAB: 9,
        ENTER: 10,
        RETURN: 13,
        ESC: 27,
        DELETE: 127,
        CODED: 65535,
        SHIFT: 16,
        CONTROL: 17,
        ALT: 18,
        CAPSLK: 20,
        PGUP: 33,
        PGDN: 34,
        END: 35,
        HOME: 36,
        LEFT: 37,
        UP: 38,
        RIGHT: 39,
        DOWN: 40,
        F1: 112,
        F2: 113,
        F3: 114,
        F4: 115,
        F5: 116,
        F6: 117,
        F7: 118,
        F8: 119,
        F9: 120,
        F10: 121,
        F11: 122,
        F12: 123,
        NUMLK: 144,
        META: 157,
        INSERT: 155,
        ARROW: "default",
        CROSS: "crosshair",
        HAND: "pointer",
        MOVE: "move",
        TEXT: "text",
        WAIT: "wait"
      },


      /**
       * Bind a function to a key
       * 
       * @param  {int|str}  key   The key or met-key to listen for
       * @param  {Function} f     The event handler
       * 
       * @return {void|boolean}     Boolean FALSE on error, otherwise void
       */
      bind : function (key, f) {
        var
          self  = π.io.keyboard,
          key   = key || false,
          f     = f   || null;


      }

    };


