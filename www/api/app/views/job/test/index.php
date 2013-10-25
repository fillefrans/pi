<!DOCTYPE html>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<style>
  body{font-family: 'Segoe UI'; font-size: large;}
  .content{ width: 700px; margin-left: auto; margin-right: auto;}

</style>
<meta charset="utf-8">
</head>
<body>

  <div class="content">
    <h1></h1>
    <form id="form">
      <label for="phone" >Phone:</label>
      <input id="phone" type="tel" name="phone" placeholder="Phone No." />
      <br />
      <label for="job" >Job No.:</label>
      <input id="job" type="number" name="job" step="1" value="1" placeholder="Job no." />
      <br />
      <label for="apikey" >API KEY:</label>
      <input id="apikey" type="text" name="apikey" placeholder="API KEY" />  
      <br />
      <!--label for="debug" >Return debug information:</label>
      <input id="debug" type="checkbox" name="debug" value="off" checked="on" />
      <br /-->
      <input id="input" type="submit" name="submit" value="Send" />
    </form>
  <div id="result"></div>
    
    <script>
      $.fn.formToJSON = function() {
        var objectGraph = {};
 
        function add(objectGraph, name, value) {
          if(name.length == 1) {
            //if the array is now one element long, we're done
            objectGraph[name[0]] = value;
          }
          else {
            //else we've still got more than a single element of depth
            if(objectGraph[name[0]] == null) {
              //create the node if it doesn't yet exist
              objectGraph[name[0]] = {};
            }
          //recurse, chopping off the first array element
            add(objectGraph[name[0]], name.slice(1), value);
          }
        };
        //loop through all of the input/textarea elements of the form
        //this.find('input, textarea').each(function() {
        $(this).children('input, textarea').each(function() {
          //ignore the submit button
          if($(this).attr('name') != 'submit') {
            //split the dot notated names into arrays and pass along with the value
            add(objectGraph, $(this).attr('name').split('.'), $(this).val());
          }
        });
        return JSON.stringify(objectGraph);
      };
 
      $.ajaxSetup({
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        type:"POST"
      });
 
      $(document).ready(function(){
        $('#input').click(function() {
          var send = $("#form").formToJSON();
          $('#result').html('Sending: <br /><pre>' + send + '</pre>');
          $.ajax({
            url: "/api/app/views/job/",
            type: "POST",
            data: send,
            error: function(xhr, error) {
              $('#result').append( '<br />AJAX ERROR! <br />Status = ' + xhr.status + ' <br />Message = ' + error + ' <br />Response = ' + JSON.stringify(xhr)+'</div>' );

            },
            success: function(data) {
              /*
              //have you service return the created object
              var items = [];
              items.push('<table cellpadding="4" cellspacing="4">');
              items.push('<tr><td>ID</td><td>' + data.id + '</td></tr>');
              items.push('<tr><td>Meh Feh</td><td>' + data.meh.feh + '</td></tr>');
              items.push('<tr><td>Meh Peh</td><td>' + data.meh.peh + '</td></tr>');
              //etc
              items.push('</table>');  
              */
              $('#result').append('<br />Reply<br /><pre>' + JSON.stringify(data, undefined, 2) + '</pre>');
            }
          });
          return false; 
        });
      });
    </script>
  </div>
  </body>
</html>