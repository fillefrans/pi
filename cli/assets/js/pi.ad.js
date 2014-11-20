  /**
   *
   * @file π.ad.video
   * Specialised video object for ads
   *
   * @author Johan Telstad, jt@viewshq.no, 2011-2014
   *
   */


  var 
    π = π || {};


  π.ad = π.ad || {

    // reference to video object

    __init : function () {
      var
        self = π.ad;

    }, // function __init()

    run : function() {
      if (!this._scanForVideo()) {
        pi.log("No video object found");
      }
    }


  } // object pi.ad.video


  π.ad.run();
