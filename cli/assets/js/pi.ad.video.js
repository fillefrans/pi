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
    intro           : document.getElementById("intro"),
    video           : document.getElementById("video"),
    container       : document.getElementById("apDiv2"),
    videoController : {},
    loadedMetaData  : false,
    CLICKED         : false,



    onAdClick : function (e) {
      var
        self = π.ad.video,
        closebtn = document.getElementById("closebtn"),
        logo = document.getElementById("ks_logo");

      if (self.CLICKED === true) {
        pi.log("you already clicked!")
        TweenLite.to(self.container, 0.2, {height:160, ease:Cubic.easeOut, onComplete: self.hideVideo});
        TweenLite.to(logo, 0.4, {opacity:1, ease:Cubic.easeOut});
        TweenLite.to(closebtn, 0.4, {top:-100, ease:Cubic.easeOut});
        TypeWriter.start();
        self.intro.style.display = "block";
        self.intro.play();
        self.CLICKED = false;
      }
      else {
        pi.log("Tweeeening !")
        self.CLICKED = true;
        closebtn.addEventListener("click", self.onAdClick);
        TweenLite.to(closebtn, 0.4, {top:0, ease:Cubic.easeOut});
        TweenLite.to(logo, 0.4, {opacity:0, ease:Cubic.easeOut});

        // self.intro.stop();
        self.intro.style.display = "none";
        TweenLite.to(self.container, 0.4, {height:550, ease:Cubic.easeOut, onComplete: self.showVideo});

      }
    },


    showVideo : function () {
      var
        self  = π.ad.video,
        tl    = new TimelineMax({delay:0.5, repeat:3, repeatDelay:2});

      pi.log("showing video now!");
      self.video.style.display = "block";
      self.videoController.play();
      TypeWriter.hide();


      // TweenMax.staggerTo(".socialbtn", 0.5, {opacity:0, y:+100, ease:Cubic.easeOut}, 0.1);

      // self.videoController.showControls();
    },

    hideVideo : function () {
      var
        self  = π.ad.video;

      pi.log("showing video now!");
      self.videoController.stop();
      self.videoController.reset();
      TypeWriter.show();
      self.video.style.display = "none";


      // TweenMax.staggerTo(".socialbtn", 0.5, {opacity:0, y:+100, ease:Cubic.easeOut}, 0.1);

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
      window.requestAnimationFrame(self.onProgress);

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
          if (self.intro.style.opacity !== 1) {
            pi.log("t : " + t +  ", oh my! resetting opacity to 1");
            self.intro.style.opacity = 1;
          }
        }
      }
      else {
        if (self.intro.style.opacity !== 1) {
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
          window.requestAnimationFrame(self.onProgress);
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
          pi.log("currentTime : "  + self.video.currentTime);
          }, false);

        self.video.addEventListener("progress", function(e) {
          // pi.log("timeupdate", e);
          pi.log("progress : "  + self.video.currentTime);
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


    start : function () {
      var  
        self  = π.ad.video,
        tl    = new TimelineMax({delay:0.5, repeat:-1, repeatDelay:2, yoyo: true, onComplete:restart});

      TweenMax.to(targ, 0.5, {alpha:0.3, repeat:-1, yoyo:true,ease:Linear.easeNone});


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
