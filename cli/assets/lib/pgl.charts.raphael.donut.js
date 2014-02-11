


        $.getJSON('query_externalservice.php',views.charts.sex.request, function(data) {

        var 
          amounts     = [], 
          names       = [],
          totalsum    = 0,
          result      = data;

        data.forEach(function(p,i, arr)(key, val) {
          if(p.key == "U"){
            return;
          }
          var
            amount = parseInt(p.value, 10);
          
          amounts.push(amount);
          totalsum += amount;

          names.push({name: views.direktmedia.sex[gender].name, percentage: "%"});

        });

        names.forEach( function(p, idx, arr){
          p.value.percentage = ((100*amounts[idx])/totalsum).toFixed(0) + "%";
        });
        
        var 

            colors = ['#404040', '#FF00DC', '#FF006E', '#FF0000', '#FF6A00', '#FFD800', '#ADF200', '#C5EAEA', '#0094FF', '#BE3DFF', '#999999'];

                    //$("#views-widget-lifephase-0"+idx).html(obj.name);
        var 
            r = Raphael("views-widget-sex", 400, 400);
            
            r.genderChart(200, 200, 140, 70, amounts, names, "#fff");
        });

//1st: #252D2E               
                //var colors = ['#F44165', '#FA3A49', '#FD502B', '#FFBF20', '#9FC808', '#DEE1E8', '#D1D3DA', '#FF6A00', '#6D0074', '#6AF9C4'];


});



