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

  π.ad = π.ad || {};

  if (!π.ad) {
    pi.require("ad");
  }

  π.ad.video = π.ad.video || {

    // reference to video object
    intro           : null,
    video           : null,
    container       : document.getElementById("pi_ad_container"),
    videoController : {},
    loadedMetaData  : false,
    CLICKED         : false,



    onAdClick : function (e) {
      var
        self = π.ad.video;

      if (self.CLICKED) {
        // pi.log("you already clicked!")
        TweenLite.to(self.container, 0.2, {height:160, ease:Cubic.easeOut});
        // self.CLICKED = true;
      }
      else {
        // pi.log("Tweeeening !")
        self.CLICKED = true;
        // self.intro.stop();
        self.intro.style.display = "none";
        TweenLite.to(self.container, 0.4, {height:550, ease:Cubic.easeOut, onComplete: self.showVideo});
      }
    },


    showVideo : function () {
      var
        self = π.ad.video;

      pi.log("showing video now!");
      self.video.style.display = "block";
      self.videoController.play();
      TypeWriter.hide();
      // self.videoController.showControls();
    },

    onEvent : function (e) {
      var
        event = e.event || false;

      if(!!event && event == "video_play") {
      }
      pi.log("onEvent : " + e.event);
    },


    onProgress : function () {
      var
        t, end, n,
        self = π.ad.video;

      // put callback invocation at the top, to ensure 
      // smooth animation in the fallback scenario (setTimeOut)
      window.raf(self.onProgress);

      // pi.log("AnimationFrame!");
      t   = self.intro.currentTime;
      end = self.intro.duration;

      self.previous = t;

      if(t >= 3) {
        n = 4 - t;
        if (n > 0) {
          self.intro.style.opacity = 4 - t;
          // pi.log("setting opacity to : " + (4 - t));
        }
        else {
          if (self.intro.style.opacity != 1) {
            pi.log("t : " + t +  ", oh my! resetting opacity to 1");
            self.intro.style.opacity = 1;
          }
        }
      }
      else {
        if (self.intro.style.opacity != 1) {
          pi.log("t : " + t +  ", resetting opacity to 1");
          self.intro.style.opacity = 1;
        }
      }

    },

    __init : function () {
      var
        self = π.ad.video;

      try {

        self.video.addEventListener("loadedmetadata", function() {
            pi.log("loadedmetadata");
            self.loadedMetaData = true;
          }, false);
        self.video.addEventListener("error", function(error) {
            pi.log("error : ", error);
          }, false);
        self.intro.addEventListener("play", function(e) {
          pi.log("starting animation");
          window.raf(self.onProgress);
          self.onEvent({event: "video_play"});
          }, false);
        self.video.addEventListener("pause", function(e) {
          self.onEvent({event: "video_pause"});
          }, false);
        self.video.addEventListener("ended", function(e) {
          self.onEvent({event: "video_finish"});
          }, false);

        self.video.addEventListener("timeupdate", function(e) {
          // pi.log("timeupdate", e);
          // pi.log("currentTime : "  + self.video.currentTime);
          }, false);

        self.video.addEventListener("progress", function(e) {
          // pi.log("timeupdate", e);
          pi.log("progress! currentTime : "  + self.video.currentTime);
          }, false);

        self.videoController.element = self.video;
        self.videoController.play = function(resume) {
            self.video.play();
        };
        self.videoController.pause = function() {
            self.video.pause();
        };
        self.videoController.stop = function() {
            if(self.video.playing) {
              self.video.pause();
            }
        };
        self.videoController.togglePause = function() {
          if (self.video.paused) {
            self.video.play();
          } else {
            self.video.pause();
          }
        };
        self.videoController.skip = function(value) {
          self.video.currentTime += value;
        };
        self.videoController.reset = function () {
          if(self.loadedMetaData) {
            self.video.currentTime = 0;
          }
        };
        self.videoController.restart = function() {
          if(self.loadedMetaData) {
            self.video.currentTime = 0;
            self.onEvent({event: 'video_restart'});
          }
        };
        self.videoController.toggleControls = function() {
          if (self.video.hasAttribute("controls")) {
            this.hideControls();
          } else {
            this.showControls();
          }
        };
        self.videoController.showControls = function(){
          self.video.setAttribute("controls", "controls");   
        };
        self.videoController.hideControls = function(){
          self.video.removeAttribute("controls")   
        };
        self.videoController.setPoster = function(img) {
          self.video.setAttribute("poster", img);   
        };

      }
      catch(e) {
        self.videoController = null;
        throw(e);
      }

    }, // function __init()


    _scanForVideo :  function () {
      var
        result = false;

      result = document.getElementsByTagName("video");

      if (!!result && result.length > 0) {
        for (var i = 0; i < result.length; i++) {
          if (result[i].id == "intro") {
            this.intro = result[i];
            this.intro.addEventListener("click", this.onAdClick);
          }
          else {
            this.video = result[i];
          }
        }
      }

      return result;
    },


    run : function() {
      if (!this._scanForVideo()) {
        pi.log("No video object found");
      }
      else {
        this.__init();
      }
    }


  } // object pi.ad.video


  π.ad.video.run();
