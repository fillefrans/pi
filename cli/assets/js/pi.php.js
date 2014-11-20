    /**
     * π.php
     * contains useful js versions of php functions
     *
     * find more at http://phpjs.org
     *
     */


    π.php = π.php || {

      is_array : function(obj) {
        return (Object.prototype.toString.call(obj) == "[object Array]");
      },

      str_pad : function(str, padto, padstr, padleft) {
        var
          padstr  = padstr  || " ",
          padto   = padto   || 0,
          padleft = padleft || false, // default is to pad on the right
          count   = 0,
          result  = str;

        count = padto - str.length;

        if(count <= 0 || !padto) {
          return str;
        }

        if(padleft) {
          for(;count--;) {
            result = padstr + result;
            }
        }
        else {
          for(;count--;) {
            result += padstr;
          }
        }

        return result;
      },


      basename : function (filename, ext) {
        var
          filename  = filename || null,
          ext       = ext || "",
          token     = "",
          slashpos  = -1;


        if(typeof filename != "string") { return false }
        if(filename.lastIndexOf("/") == filename.length-1) {
          if(filename.length) {
            return π.php.basename(filename.substring(0, filename.length-1));
          }
          else {
            // that's an error
            return;
          }
        }

        slashpos = filename.lastIndexOf("/");
        if (slashpos > -1) {
          token = filename.substring(slashpos+1);
        }
        else {
          token = filename;
        }

        if(ext && typeof ext === "string") {
          var strlen = token.length;
          if(token.lastIndexOf(ext) == (strlen - ext.length)) {
            token = token.substring(0, token.lastIndexOf(ext));
          }
          else{
          }
        }
        return token;
      }

    }


    /*    PHP function aliases   */

    π.str_pad   = π.php.str_pad;
    π.is_array  = π.php.is_array;
    π.basename  = π.php.basename;

