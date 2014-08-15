Raphael.fn.ageChart = function (options, values, labels, stroke) {
    var
      paper = this,
      chart = this.set(),

      min   = options.range.min || 0,
      max   = options.range.max || 100,
      range = max - min,

      margins = 2,
      spacer  = 2,
      width   = options.width,
      height  = options.height,

      fill    = "#f8f8f2",

      barh    = 1,
      // barh    = options.bars.height,


      colors = ['#303030', '#FF00DC', '#FF006E', '#FF0000', '#FF6e00', '#FFD800', '#107010', '#ADF200', '#C5EAEA', '#0094FF', '#BE3DFF', '#999999'],

      bars = [],
      composition = [],


      update = function(agevalues) {

        var
          zeromark  = 198,
          lines     = bars,
          thismax   = 0,
          unit      = 0;



        // console.log(agevalues, agevalues);
        // pi.log("Update : " + typeof agevalues + " received (" + agevalues + "), test : " + typeof test + test, agevalues, test);

        for(var i = 1; i < 100; i++) {
          if(agevalues[i] > thismax) {
            thismax = agevalues[i];
            // pi.log("new max : " + thismax);
          }
        }

        unit = zeromark/thismax;


        lines.forEach(function(p,i) {
          if(i==0) return;
          // pi.log("thismax : " + thismax + ", zeromark =  " + zeromark + ", unit : " + unit + ", agevalues[i] : " + agevalues[i]);
          p.animate({ x : zeromark - (agevalues[i] * unit), width : (agevalues[i] * unit) + 1}, 800, "elastic");
        });
 
        return true;
      },


      init = function () {
        var
          zeromark    = 198,
          spacermark  = spacer+barh,
          colorindex  = 0,
          color       = "",
          groupstops  = [12, 18, 25, 40, 65, 100];

          for(var i = 0; i < 100; i++) {
            // pi.log(zeromark + ", " + (i * spacermark) + ", " + barh + ", " + barh);
            color = colors[colorindex];
            if(i>=groupstops[colorindex]) {
              colorindex++;
              if(colorindex>=(colors.length-1)) {
                colorindex = colors.length-1;
              }
            }

            bars.push(paper.rect(zeromark, i * spacermark, barh, barh).attr({fill : color, stroke : color, strokeWidth : 0}));
            // bars[](paper.rect( width+margins-values[idx], idx*(spacer+height), height, values[idx++]).attr("fill", "#fff"));
          }
        if(pi.events) {
          pi.events.subscribe("pi.app.views.agechart.update", update);
        }
      },


      total = 0,
      start = 0,


      process = function (j) {
        //console.log(params.fill);
        var 
          top     = 0,
          left    = 0,
          width   = values[j],
          height  = barh;
          
          // p = bar(),
          // chart.push(p);

          start += .1;
      };


    var
      count = values.length;    

    for (var i = count;i--;) {
      // calculate total items
      total += values[i];
    }


    init();


    // layer elements in reverse order on top of the chart
    composition.forEach(function(p){
      if(typeof p.toFront == "function"){
        p.toFront();
      }
    });

    return chart;
};