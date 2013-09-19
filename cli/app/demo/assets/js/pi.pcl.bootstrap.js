

  /** 
   *  π.pcl.bootstrap
   * 
   *  PCL bootstrapper
   * 
   */


  π.pcl = π.pcl || {};


  π.pcl.__init = function (DBG) {
    pi.log("ready to run: ", this);
    return true;
  };


  π.pcl.__scan = function (DBG) {
    pi.log("ready to run: ", this);
    return true;
  };


  
  // whatever you need to do to start things up
  π.pcl.run = function (DBG) {
    var
      self = π.pcl;

    if (!self.__init(DBG)) {
      return false;
    }
    pi.log("ready to run: ", this);

  };





  // run pcl
  π.pcl.run();
