  /**
   *
   * @file π.ad.video
   * Specialised video object for ads
   *
   * @author Johan Telstad, jt@viewshq.no, 2011-2014
   *
   */


  var 
    π = π || { log : window.console.log || function() {}};

  var pi = pi || π;

  π.ad = π.ad || {};


  π.ad.video = π.ad.video || {

    // reference to video object
    video           : document.getElementById("video"),
    container       : document.getElementById("apDiv2"),
    slideshow       : document.getElementById("slideshow"),
    videoController : {},
    loadedMetaData  : false,
    CLICKED         : false,



    onAdClick : function (e) {
      var
        self = π.ad.video,
        closebtn = document.getElementById("closebtn"),
        form = document.getElementById("form"),
        logo = document.getElementById("ks_logo");

      if (self.CLICKED === true) {
        // alert("you already clicked!");
        self.hideVideo();
        // TweenLite.to(self.container, 0.2, {height:160, ease:Cubic.easeOut, onComplete: self.hideVideo});
        logo.style.opacity = 1;
        closebtn.style.display = "none";
        // TweenLite.to(form, 1, {left:"1280px", ease:Cubic.easeOut});
        TextEffect.start();
        // self.intro.style.display = "block";
        // self.intro.play();
        self.CLICKED = false;
      }
      else {
        // pi.log("Tweeeening !");
        self.CLICKED = true;
        $(closebtn).bind("click", self.onAdClick);
        closebtn.style.top = 0;
        closebtn.style.display = "block";
        logo.style.opacity = 0;

        TextEffect.hide();
        // self.intro.stop();
        // TweenLite.to(self.container, 0.4, {height:550, ease:Cubic.easeOut, onComplete: self.showVideo});
        self.showVideo();

      }
      e.preventDefault();
    },


    showVideo : function () {
      var
        self  = π.ad.video;

      // alert("showing video now!"+ this.id);
      self.video.style.display = "block";
      self.videoController.play();
      TextEffect.hide();


      // TweenMax.staggerTo(".socialbtn", 0.5, {opacity:0, y:+100, ease:Cubic.easeOut}, 0.1);

      // self.videoController.showControls();
    },

    hideVideo : function () {
      var
        self  = π.ad.video;

      // alert("hiding video now!"+ this.id);
      // self.videoController.reset();
      // self.videoController.stop();
      TextEffect.show();
      self.video.pause();
      self.videoController.reset();
      self.video.style.display = "none";


      // TweenMax.staggerTo(".socialbtn", 0.5, {opacity:0, y:+100, ease:Cubic.easeOut}, 0.1);

      // self.videoController.showControls();
    },

    onEvent : function (e) {
      var
        event = e.event || false;

      if(!!event && event == "video_play") {
      }
      // pi.log("onEvent : " + e.event);
    },


    onProgress : function () {
      var
        t, end, n,
        self = π.ad.video;

      // put callback invocation at the top, to ensure 
      // smooth animation in the fallback scenario (setTimeOut)
      window.requestAnimationFrame(self.onProgress);

      // pi.log("AnimationFrame!");
      // t   = self.intro.currentTime;
      // end = self.intro.duration;


    },

    __init : function () {
      var
        self = π.ad.video;

      // document.body.addEventListener("click", self.onAdClick);

      try {

        self.video.addEventListener("loadedmetadata", function() {
            // pi.log("loadedmetadata");
            self.loadedMetaData = true;
          }, false);
        self.video.addEventListener("error", function(error) {
            // pi.log("error : ", error);
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
          // pi.log("progress : "  + self.video.currentTime);
          }, false);

        self.videoController.element = self.video;
        self.videoController.play = function(resume) {
            self.video.play();
        };
        self.videoController.pause = function() {
            self.video.pause();
        };
        self.videoController.stop = function() {
              self.video.pause();
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

      if (!!result && result.length) {
        for (var i = 0; i < result.length; i++) {
          if (result[i].id == "intro") {
            this.intro = result[i];
            // this.intro.addEventListener("click", this.onAdClick);
          }
          else {
            this.video = result[i];
            return true;
          }
        }
      } else {
        // alert("No video found!");
      }

    },


    run : function() {
      if (!this._scanForVideo()) {
        alert("No video object found");
      }
      else {
        if (!!self.slideshow && typeof self.slideshow.addEventListener == "function") {
          self.slideshow.addEventListener("click", this.onAdClick);
          this.__init();
        }
        else {
          alert("NO VIDEO");
        }
      }
    }


  } // object pi.ad.video

  $(document).ready(function() {
    pi.ad.video.run();
  });
