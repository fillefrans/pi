    /*
      core support modules
    */


    π.tasks = {

      _pi     : {},
      __items : {},


      start : function(taskaddress, parameters, onprogress) {

        var

                            // replace '.' with '_'
          id              = taskid.replace(/\./g,'_'),

          tasks           = π.tasks.__items,
          self            = π.tasks.__items[id] || false,
          events          = π.events            || false,
          onprogress      = onprogress          || false,
          updateinterval  = updateinterval      || 1000,
          progressid      = false;


        if(self) {
          pi.log("Warning: starting task " + taskid + " for a second time. Results unpredictable.");
        }

        if(typeof onprogress === "function") {
          progressid = setInterval(onprogress, updateinterval);
        }


        tasks[id] = { 

          id          : taskid, 
          start       : (new Date()).getTime(), 
          progressid  : progressid 
        };


        if(events.publish) {
          events.publish("pi.tasks." + taskid + ".start", {event: "start", data: tasks[id]});
        }
      },


      stop : function(taskid) {
        var
          tasks   = π.tasks.__items,
          history = π.tasks.history,
          self    = π.tasks.__items[taskid.replace(/\./g,'_')] || false;

        if(!self) {
          π.events.publish("pi.task.items." + taskid, "Warning: stopping non-existent task \"" + taskid + "\". Results unpredictable.");
          pi.log("Warning: stopping non-existent task " + taskid + ". Results unpredictable.");
          return false;
        }

        // is there an attached progress handler ?
        if(self.progressid) {
          // if yes, clear progress interval
          clearInterval(self.progressid);
          self.progressid = false;
          self.onprogress = null;
        }
        self.stop = (new Date()).getTime();

        self.time = self.stop - self.start;

        var 
          result = self.time;
        history.add(self);

        // return task value
        return result;
      },

      history : {
        
        log   : [],

        add : function (obj) {
          π.tasks.history.log.push(obj);
          π.events.publish("pi.tasks.on", ["add", obj]);
        },

        list  : function (callback){
          var
            log       = π.tasks.history.log,
            callback  = callback || pi.log;


          if(callback instanceof Function) {
            log.forEach(function(value, index) {
              callback.call(index, value);
            });
          }
        },

        clear : function () {
          var
            log = π.tasks.history.log;

          π.events.publish("pi.tasks.history.on", ["clear"]);

          // clear log array, this is actually the fastest way
          while(log.pop()){
            // nop
          }
        }
      } // end of history object
    };

