


  π.app.cache = {

    self : this,

    __applicationCache : window.applicationCache,


    getStatusText : function() {
      var
        status  = new Array('UNCACHED', 'IDLE', 'CHECKING', 'DOWNLOADING', 'UPDATEREADY', 'OBSOLETE'),
        idx     = Math.abs(self.__applicationCache.status); 

      if (idx >= status.length) {
        return 'UNKNOWN';
      }
      else {
        return status[idx];
      }
    }
  }
