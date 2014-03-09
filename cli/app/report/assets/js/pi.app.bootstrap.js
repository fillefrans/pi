

  /** 
   *  π.app.bootstrap
   * 
   *  App bootstrapper
   * 
   */


   π.app = π.app || {};


  π.app.__init = function (DBG) {
    pi.log("ready to run: ", this);
    return true;
  };




  
  // whatever you need to do to start things up
  π.app.run = function (DBG) {
    var
      self = π.app;

    if (!self.__init(DBG)) {
      return false;
    }
    pi.log("ready to run: ", this);

  };



  // var data = crossfilter([
  //     {date: "2011-11-14T16:17:54Z", quantity: 2, total: 190, tip: 100, type: "tab"},
  //     {date: "2011-11-14T16:20:19Z", quantity: 2, total: NaN, tip: 100, type: "tab"},
  //     {date: "2011-11-14T16:28:54Z", quantity: 1, total: 300, tip: 200, type: "visa"},
  //     {date: "2011-11-14T16:30:43Z", quantity: 2, total: 90, tip: 0, type: "tab"},
  //     {date: "2011-11-14T16:48:46Z", quantity: 2, total: 90, tip: 0, type: "tab"},
  //     {date: "2011-11-14T16:53:41Z", quantity: 2, total: 90, tip: 0, type: "tab"},
  //     {date: "2011-11-14T16:54:06Z", quantity: 1, total: NaN, tip: null, type: "cash"},
  //     {date: "2011-11-14T17:02:03Z", quantity: 2, total: 90, tip: 0, type: "tab"},
  //     {date: "2011-11-14T17:07:21Z", quantity: 2, total: 90, tip: 0, type: "tab"},
  //     {date: "2011-11-14T17:22:59Z", quantity: 2, total: 90, tip: 0, type: "tab"},
  //     {date: "2011-11-14T17:25:45Z", quantity: 2, total: 200, tip: null, type: "cash"},
  //     {date: "2011-11-14T17:29:52Z", quantity: 1, total: 200, tip: 100, type: "visa"}
  //     ]);

  //   try {
  //   var total_dimension = data.dimension(function(d) { return d.total; });
  //   var na_records = total_dimension.filter(90).top(Infinity);
  //   var elRecords = document.body.createElement("ul");
  //   // elRecords.empty();
  //   for (var i = 0; i < na_records.length; i++) {
  //       $('<li>', { text : na_records[i].total}).appendTo(elRecords);
  //   }
  // } catch (e) {
  //   console.log(e);
  // }



  // run app
  π.app.run();
