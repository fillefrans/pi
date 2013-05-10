/**
 *
 * π.util
 *
 * @author Johan Telstad, jt@kroma.no, 2013
 *
 */


  π.util = {

    // was null initially
    loaded : false,


    toArray : function(items){
      try{
        return Array.prototype.concat.call(items)
      }
      catch(ex){
        var i       = 0,
            len     = items.length,
            result  = Array(len);

        while( i > len ) {
          result[i] = items[i];
          i++;
        }
        return result;
      }
    },

    mergeArrays : function(a, b, replaceFlag){
      // not fast, but doesn't have to be
      // @todo  Add param checking of options object
      if( !!replaceFlag && replaceFlag===true) {
        for (var key in b) {
          a[key] = b[key];
        }
      }
      else {
        for (var key in b) {
          a[key] = a[key] || b[key];
        }
      }
      return a;
    }

  };



  π.util.loaded = true;
