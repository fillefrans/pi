  
  var

    OPTIONS = {
      eventSource   : 'assets/php/kroma.pubsub.listener.sse.php'
      filterEvents  : true
    };





  var 
    DataSet = {

      __updates   : [],
      __summary   : document.getElementById('reportbody') || false,
      __sessions  : document.getElementById('sessionbody') || false,
      __rowElems  : null,
      __rows      : [],
      __rowIndex  : {},
      __colIndex  : [],
      _hasHeaders : false,


      __setHeaders : function (obj) {
        var
          headerRow = document.getElementById('sessionheaders') || false,
          tdList    = document.createDocumentFragment(),
          cell      = null;

        if(!headerRow) {
          console.log("No 'sessionheaders' row in table");
          return false;
        }

        for(var key in obj) {
          cell = document.createElement('td');
          cell.appendChild(document.createTextNode(key));
          tdList.appendChild(cell);

          // creates a parallell array with column keys, in the correct sequence
          this.__colIndex.push(key);
        }

        // append any TDs
        if(tdList.childNodes.length >0) {
          // console.log("Adding " + tdList.childNodes.length + " TDs");
          headerRow.appendChild(tdList);
          return true;
        }
        else {
          // console.log("No TDs added to headers");
          return false;
        }
      },


      __init : function (options) {

        // returns a live list of DOM elements, which updates as the DOM changes (i.e. no need to call more than once)
        this.__rowElems = this.__summary.getElementsByTagName('tr');
        return true;
      },


      updateRow : function (row) {
        var
          row     = (typeof row === "string") ? (JSON.parse(row) || false) : (row || false),
          rowElem = (this.__rowIndex["r"+row.start]) ? this.__rowIndex["r"+row.start]['element'] : false,
          cells   = null;

        if(row) {
          // console.log("Converting row to object: ", row);
          row = this.__rowToObject(row);
          // console.log("After conversion: ", row);

          rowElem = (this.__rowIndex["r"+row.start]) ? this.__rowIndex["r"+row.start]['element'] : false;
        }

        if(!rowElem) {
          console.log("NEW session: " + "r"+row.start);
          return this._addRow(row);
        }

        rowElem.classList.remove("added");
        rowElem.classList.remove("updated");

        cells = rowElem.getElementsByTagName('td');

        var
          cellCount = cells.length;

        for (var i = 0; i<cellCount; i++) {
          cells[i].innerHTML = row[this.__colIndex[i]];
          // console.log(this.__colIndex[i] + ": " + row[this.__colIndex[i]]);
        }

        rowElem.classList.add("updated");
        return true;
      },


      __rowToObject : function (row) {
        var
          result = {};

        for (var key in row) {
          if ( (typeof row[key] === "object")  && (typeof result[key] === "undefined") ) {
              var 
                deep = this.__rowToObject(row[key]);
              for(var idx in deep) {
                result[idx] = deep[idx];
              }
          }
          else {
            result[key] = row[key];
          }
        }
        return result;
      },


      _addRow : function (row) {
        var
          cell      = null,
          trow      = document.createElement('tr'),
          fragment  = document.createDocumentFragment();

        for( var idx in row ) {
          if(idx==="element") {
            continue;
          }
          cell = document.createElement('td');
          cell.appendChild(document.createTextNode(row[idx]));
          trow.appendChild(cell);
        }

        if( !this._hasHeaders && (trow.childNodes.length > 0) ) {
          // console.log("Setting headers: " + trow.childNodes.length);
          this._hasHeaders = this.__setHeaders(row);
        }


        // console.log("Setting id to: r" + row.start);
        trow.setAttribute('id', 'r'+row.start);
        fragment.appendChild(trow);
        this.__rowIndex['r'+row.start] = {element: trow};
        if(this.__sessions.childNodes.length>0) {
          // add new sessions at the top
          this.__sessions.insertBefore(fragment, this.__sessions.childNodes[0]);
        }
        else {
          this.__sessions.appendChild(fragment);
        } 

        trow.classList.add("added");
      },


      addRow  : function (row) {
        var
          row = (typeof row === "string") ? (JSON.parse(row) || false) : (row || false);



        if(row) {
          row = this.__rowToObject(row);
        }

        if( typeof row !== "object" ) {
          // console.log("row is not an object: " + row);
          return false;
        }

        if(this.__rowIndex["r" + row.start]) {
          // console.log("Row r" + row.start + " already exists, updating ...");
          return this.updateRow(row);
        }

        this.__rowIndex["r" + row.start] = this.__rows.push(row)-1;
        this._addRow(row);
      },


      deleteRow : function (idx) {
        var 
          i     = this.__rows.length,
          rows  = this.__rows;

        if(typeof idx !== "number") {
          return false;
        }

        while (i--) {
          if (rows[i] == idx) { 
            rows = rows.splice(i, 1);
            return true;
          }
        }

        return false;
      },


      findRow : function (search) {

      },


      run : function (options) {
        return this.__init(options);
      }

    };



    function connectionOpen(open) {
      var
        msg   = open ? "Active connection to server." : "Connection dropped, trying to reopen.",
        event = open ? "open" : "error",
        term  = document.getElementById('console') || false;

        // console.log("EventSource " + event + ": ", msg);
        onMessage({event: event, data: { message: msg}});

        if(!term) {
          console.log("Error: No console div");
          return;
        }

        term.innerHTML += "EventSource " + event + ": " + msg + '<br />';
    }


    function updateConnections(event) {
      connections.innerHTML = event.data;
    }


    function updateRequests(event) {
      requests.innerHTML = event.data;
    }


    function onDebugData(e) {
      var
        chunk = (typeof e.data === "object") ? e.data : JSON.parse(e.data),
        term  = document.getElementById('console') || false;

        // console.log("EventSource " + event + ": ", msg);
        if(!term) {
          console.log("Error: No console div");
          return;
        }

        term.innerHTML += "DEBUG: " + JSON.stringify(chunk) + '<br />';
    }



    function onData(e) {
      var
        chunk = (typeof e.data === "object") ? e.data : JSON.parse(e.data);
      
      for (var key in chunk.data.info) {
        console.log(key + ": " + chunk.data.info[key]);
      }
      DataSet.addRow(chunk.data);
    }


    function onMessage(e) {
      var 
        term  = document.getElementById('console') || false,
        json  = typeof e.data === "object" ? e.data : JSON.parse(e.data);

      if(!term) {
        console.log("Error: No console div");
      
        return;
      }

      if(!!json.event) {
        term.innerHTML += "[" + json.event + "] " + JSON.stringify(json.data) + '<br />';
      }
    };


    function onReportData(e) {

      if(!!TimeReport) {
        console.log("onReportData: ", e.data);
        TimeReport.refresh(e.data);
      }
      else {
        console.log("ERROR: no TimeReport object");
      }
      
    }


    function onReportSummary(e) {
      
      if(!!TimeReport) {
        console.log("onReportSummary: ", e.data);
        TimeReport.setSummary(e.data);
      }
      else {
        console.log("ERROR: no TimeReport object");
      }
    }

    function _onResponse(e) {

      if (this.readyState != 4) {
        return;
      }

      if (this.status != 200 && this.status != 304) {
          console.log('HTTP error: ' + this.status + " - " + this.statusText);
          return;
      }

      try {
        var
          response = JSON.parse(this.responseText);

        if( response.OK == 1 ) {
          if (typeof response.urls ==="object") {
            var urlList = document.getElementById('urls') || false;
            response.urls.forEach(function(el, idx, arr) {
              ////
              if(urlList) {
                var li = document.createElement('li');
                li.appendChild(document.createTextNode(el.total + " | " + el.url));
                li.addEventListener("mouseover", function(e){
                  this.classList.add('hovering');
                });
                li.addEventListener("mouseout", function(e){
                  this.classList.remove('hovering');
                });
                
                li.setAttribute("data-url", el.url);
                li.addEventListener("click", onClickUrl);

                urlList.appendChild(li);
              }
              else {
                console.log("no url list");
              }
              console.log(idx + ": ", el);
            });
          }
        } else{
            // console.log('Error-response received from logger service: ' + this.status + "; OK: " + response.OK, response);
        }
      }
      catch (e) {
        // console.log("Error: ", e);
      }
    };


    function emptyTable(name) {
      var
        table = document.getElementById(name) || false;

      if(!table) {
        console.log("No such table: " + name);
        return;
      }

      for(var i = table.rows.length-1; i>=0;i--) {
        console.log("deleting row: " + i);
        table.deleteRow(i);
      }
    }


    function onClickUrl(e){
      var
        url = this.getAttribute("data-url"),
        list = document.getElementById('urls').getElementsByTagName('li');

      if(url=='') {
        return;
      } 

      // console.log("clearing tables");
      // emptyTable('sessionbody');
      // emptyTable('reportbody');

      for(var i = 0, count = list.length; i<count; i++) {
        if(list[i]!=this) {
          list[i].classList.add("invisible");
        }
        else {
          list[i].classList.remove("invisible");
        }

      }

      run(url);
      // this.getAttribute("data-url");
      // li.setAttribute("data-url", el.url);
                
    }




    function getUrls() {
      var
        xhr   = new XMLHttpRequest();

      xhr.onload = _onResponse;
      xhr.open("GET", 'assets/php/kroma.pubsub.listener.urls.php', true);
      xhr.setRequestHeader("Accept", "application/json");
      xhr.setRequestHeader("Connection", "close");
      xhr.send();

    }




    function run(url) {

      var 
        source  = new EventSource(OPTIONS.eventSource + ((url!='') ? '?url=' + encodeURI(url) : '')),
        term    = document.getElementById('console') || false;

      source.addEventListener('open', function () { connectionOpen(true); }, false);
      source.addEventListener('error', function () { connectionOpen(false); }, false);
      source.addEventListener('connections', updateConnections, false);
      source.addEventListener('debug', onDebugData, false);
      source.addEventListener('data', onData, false);
      source.addEventListener('report', onReportData, false);
      source.addEventListener('summary', onReportSummary, false);
      source.addEventListener('message', onMessage, false);
     
    }




    // start

    getUrls();

