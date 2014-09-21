/**
 * Minimal app bootstrapper for internal purposes
 *
 * Merges existing app object with new
 *
 */


    π.TMP = π.app;

    π.app = {

      PI_ROOT   : π.PI_ROOT,
      LIB_ROOT  : π.LIB_ROOT,
      IMG_ROOT  : π.IMG_ROOT,
      CSS_ROOT  : π.CSS_ROOT,

      self      : this,


      __init : function() {
        for(var key in pi.TMP) {
          this[key] = pi.TMP[key]
        }
        pi.TMP = null;
      }
    };

    π.app.__init();
    π.app._loaded = true;



    var
      frm, ctrl;



    π.require("html", false, false, function(msg) {

        frm = {
          div : document.getElementById("content"),
          form : document.createElement("form"),

          __init : function() {
            this.div.appendChild(this.form);
          },

          run : function () {
            this.__init();

          }


        };


        ctrl = {
          div : document.getElementById("control"),
          form : document.createElement("form"),
          repl : document.createElement("input"),
          out : null,

          // initial value is ourselves as controller
          controller : this,

          __init : function() {
            this.div.appendChild(this.form);
            this.form.appendChild(this.repl);
            this.repl.placeholder = "~:$";
            this.repl.label = "~:$";
            this.repl.autofocus = "autofocus";
            this.out = this.repl.appendChild(document.createElement("dl"));
            // this.repl.addEventListener("change", pi.log, true);
            this.form.onsubmit = this.submit;
            this.controller = this;
            this.fullscreen = document.createElement("button");
            if(pi.browser.isIe()) {
              this.fullscreen.innerText = "fullscreen";
            }
            else {
              this.fullscreen.innerHTML = "fullscreen";
            }

            this.fullscreen.addEventListener("click", π.html.fullscreen.request);
            this.div.appendChild(this.fullscreen);

          },

          run : function () {
            this.__init();

          },

          shell : {
            iscontroller : true,
            // a controller
            ls : function () {
              pi.log ("pi.shell.ls " + Array.prototype.slice.call(arguments, 1));
            }
          },

          submit : function () {
            var
              self = ctrl,
              arg,
              cmd;

            cmd = self.repl.value;

            pi.log("repl : " + self.repl.value);

            arg = cmd.split(" ");

            if (arg.length === 0) {
              pi.log("No arguments submitted");
              return false;
            }

            if (arg.length === 1) { // one argument
              pi.log("1 argument");
              if (self.controller[arg[0]] && (self.controller[arg[0]].iscontroller === true)) {
                // set controller to arg0
              pi.log("argument is a controller");
                self.controller = self.controller[arg[0]];
              }
              else {
                pi.log("argument is not a controller");
                if (typeof self.controller[arg[0]] === "function") {
                  alert("argument is not a controller ... but it is a function, so we're calling it:");
                  self.controller[arg[0]].apply(self.controller, arguments.slice(1));
                  // self.controller[arg[0]]();
                }
              }
            }
            else { // more than one argument
              pi.log("more than 1 argument : " + arg.length);
              if (self.controller[arg[0]] && (self.controller[arg[0]].iscontroller === true)) {
                // set controller to arg0
              pi.log("argument 0 is a controller");
                self.controller = self.controller[arg[0]];
              }
              else {
                pi.log("argument 0 is not a controller");
                if (typeof self.controller[arg[0]] === "function") {
                  alert("argument 0 is not a controller ... but it is a function, so we're calling it:");
                  self.controller[arg[0]].apply(self.controller, arguments.slice(1));
                  return false;
                }
              }

            }

            self.repl.value = null;
            return false;
          },

          list : function(data) {
            // π.send();

          }


        };


        frm.run();
        ctrl.run();

    });

