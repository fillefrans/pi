<?php
  $starttime = microtime(true);
?>
<html lang="no">
  <head>
    <meta charset="utf-8">

    <!-- <link rel="stylesheet" type="text/css" href="assets/fonts/titillium.css"> -->
    <style type="text/css">


      body {
        margin      : 12px;
        color       : #272822;
        background  : #f8f8f2;
        font-family : sans-serif;
      }


      pre {
        color   : #888;
      }

      .debug {
        /*display: none;*/
      }

      .sourcecode {
        width   : 1024px;
        display : none;
      }

      .horizontal {
        width   : 100%;
        margin  : 12px;
      }

      .horizontal ul {
        width   : 100%;
        display : inline;

        list-style-type : none; 
        padding-right   : 20px;
      }

      .horizontal li {
        display           : inline;
        list-style-type   : none;
        padding-right     : 20px;
      }

    </style>
    
    <script src="/pi/assets/js/lib/ace/ace.js" type="text/javascript" charset="utf-8"></script>
  </head>
  <body>




    <div class="horizontal">
      <ul id="toc"></ul>
    </div>

    <script>

      function toggleVisible(element) {
        var
          ref = document.getElementById(element);
        if(ref) {
          ref.style.display === "none" ? ref.style.display = "block" : ref.style.display = "none";
        }
      }


      function showDebugInfo(){
        var debuginfos = document.querySelectorAll('.debug');

        console.log("show debug innfo : " + debuginfos.length);

        if(debuginfos.length>0) {
          for(var i = 0; i < debuginfos.length; ++i) {
            var el = debuginfos[i];
            el.style.display = el.style.display === "none" ? el.style.display = "block" : el.style.display = "none";
          }

        }
      }


      function toggleEditMode () {
        var
          editableElements, curr_node, removing = false;

        // early escape
        if (!'contentEditable' in document.body) {
          return;
        }

        editableElements = document.querySelectorAll("div .EAad");
        if(editableElements.length>0) {
          /// why are we doing this, again?
          curr_node = editableElements[0]; 
          removing = curr_node.hasAttribute('contenteditable') && curr_node.getAttribute('contenteditable');
        }

        console.log("REMOVING : " + removing);

        for(var i = 0; i < editableElements.length; ++i) {

          // print the tag name of the node (DIV, SPAN, etc.)
          /// because we're overwriting it here?
          curr_node = editableElements[i];
          console.log(curr_node.tagName);

          if(removing === true) {
            console.log("removing editable");
            curr_node.removeAttribute('contenteditable');
          }
          else {
            if (curr_node.innerHTML.indexOf("<img ") === -1) {
              curr_node.setAttribute('contenteditable', "true");
            }
          }


          // show all the attributes of the node (id, class, etc.)
          // for(var j = 0; j < curr_node.attributes.length; ++j) {
          //     var curr_attr = curr_node.attributes[j];
          //     console.log(curr_attr.name, curr_attr.value);
          // }
        }

       // contenteditable="true" onresizestart="return false;"
      }


      window.addEventListener('load', function() {

        toggleEditMode();

        /*  support functions  */
        function insertAfter(referenceNode, newNode) {
            referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
        }


        function onHashChange () {
          var
            currentId       = window.location.hash.substr(1),
            editorId        = currentId.replace("-source", "-editor"),
            currentElement  = document.getElementById(currentId),
            editorElement   = document.getElementById(editorId);

          // console.log("hashchange     : " + window.location.hash);
          // console.log("currentId      : " + currentId);

          // console.log("currentElement : " + currentElement.tagName);

          if(currentElement  && ( currentElement.tagName && currentElement.tagName === "TEXTAREA")) {
            // currentElement.style.display = "block";
            var
              editor = ace.edit(editorId);

            editor.setOptions({
              maxLines        : Infinity,
              showPrintMargin : false,
              useSoftTabs     : true,
              fontSize        : 16
            });
            // alert(currentElement.value);
            // editor.setTheme("ace/theme/monokai");
            editor.setTheme("ace/theme/chrome");
            editor.getSession().setMode("ace/mode/html");
            editor.getSession().setUseWrapMode(true);
            // editor.getSession().setUseSoftTabs(true);
            // editor.setReadOnly(true);
            // editor.commands.bindKey("Tab", null);
            editor.getSession().setValue(currentElement.value + "\n");
          }

        } // onHashChange


        function init () {
          var
            i, li, newAnchor, ad,
            toc = document.getElementById("toc"),
            ads = document.getElementsByClassName("EAad");


          if ("onhashchange" in window) {
            window.addEventListener("hashchange", onHashChange);
          }


          for (var i = ads.length-1; i >= 0; i--) {
            if (null !== (ad = ads.item(i)) ) {
              ad.addEventListener("mouseover", function(e) {
                // console.log("ad element: ", this);

              });
            }
          }


          for (i = 0; i < document.anchors.length; i++) {

            // skip the source anchors
            if (document.anchors[i].name.indexOf("source") > -1) {
              continue;
            }

            // create nav link to each ad
            li = document.createElement("li");
            newAnchor = document.createElement('a');
            newAnchor.href = "#" + document.anchors[i].name;
            newAnchor.innerHTML = document.anchors[i].text;
            li.appendChild(newAnchor);
            toc.appendChild(li);
          }

        } // init ()



        /*  Initialisation code  */

        init();

      }); // onload event listener

    </script>

    <?php

      require_once('templatelist.class.php');


      // set some example data for previewing purposes
      $defaults = array(
        "custom4"     => "assets/images/test.jpg", 
        "custom9"     => "Smakfull enplansvilla mot natur och strövområde", 
        "shouttitle"  => "UPPSALA – LUTHAGEN, GÖTGATAN 13",
        "shouttext"   => "Nyrenoverad enplansvilla om 74 välplanerade kvadrat! Stor södervänd tomt med bl a skog och allmänning som granne"
        );

      $templates = new TemplateList(".");

      $templates->render($defaults, true); // second param is whether to show source code

      // $templates->showTimers();

      // // then render all the templates with example data
      // foreach ($templates->items as $key => $template) {
      //   print("<p /><a name='$key' href='#$key'>$key</a>\n");

      //   $template->render($defaults);
      //   $template->showSource();
      // }

      $time = microtime(true) - $starttime;
      print("<p />total time : " . ceil($time * 1000));
    ?>



  </body>
</html>