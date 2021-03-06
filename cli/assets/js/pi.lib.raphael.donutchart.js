Raphael.fn.donutChart = function (cx, cy, r, rin, values, labels, stroke) {
    var 
      paper = this,
      rad   = Math.PI / 180,
      chart = this.set(),
      percentageText  = null,
      percentageLayer = null,
      ontop = [],
      overlayR  = rin + Math.round((r-rin)/2),
      colors    = ['#303030', '#FF00DC', '#FF006E', '#FF0000', '#FF6A00', '#FFD800', '#ADF200', '#C5EAEA', '#0094FF', '#BE3DFF', '#999999'],

      sector = function(cx, cy, r, startAngle, endAngle, params) {
        //console.log(params.fill);
        var x1 = cx + r * Math.cos(-startAngle * rad),
            x2 = cx + r * Math.cos(-endAngle * rad),
            y1 = cy + r * Math.sin(-startAngle * rad),
            y2 = cy + r * Math.sin(-endAngle * rad),
            xx1 = cx + rin * Math.cos(-startAngle * rad),
            xx2 = cx + rin * Math.cos(-endAngle * rad),
            yy1 = cy + rin * Math.sin(-startAngle * rad),
            yy2 = cy + rin * Math.sin(-endAngle * rad);
        
        return paper.path(["M", xx1, yy1,
                           "L", x1, y1, 
                           "A", r, r, 0, +(endAngle - startAngle > 180), 0, x2, y2, 
                           "L", xx2, yy2, 
                           "A", rin, rin, 0, +(endAngle - startAngle > 180), 1, xx1, yy1, "z"]
                         ).attr(params);
      },

      overlay = function(cx, cy, overlayR, startAngle, endAngle, params) {
        var 
          x1  = cx + overlayR * Math.cos(rad * -startAngle),
          x2  = cx + overlayR * Math.cos(rad * -endAngle),
          y1  = cy + overlayR * Math.sin(rad * -startAngle),
          y2  = cy + overlayR * Math.sin(rad * -endAngle),

          xx1 = cx + rin * Math.cos(rad * -startAngle),
          xx2 = cx + rin * Math.cos(rad * -endAngle),
          yy1 = cy + rin * Math.sin(rad * -startAngle),
          yy2 = cy + rin * Math.sin(rad * -endAngle);
      
        return paper.path(["M", xx1, yy1,
                           "L", x1, y1, 
                           "A", overlayR, overlayR, 0, +(360 - 0 > 180), 0, x2, y2, 
                           "L", xx2, yy2, 
                           "A", rin, rin, 0, +(360 - 0 > 180), 1, xx1, yy1, "z"]
                         ).attr(params);
          
      },

      angle = 0,
      total = 0,
      start = 0,

      process = function (j) {
        var 
          value     = values[j],
          angleplus = 360 * value / total,
          popangle  = angle + (angleplus / 2),
          color     = colors[j],
          ms        = 500,
          delta     = 20,
          bcolor    = colors[j],
          
          p   = sector(cx, cy, r, angle+90, angle + angleplus+90, {fill: "90-" + bcolor + "-" + color, stroke: stroke, "stroke-width": 0}),
          txt = paper.print(cx - 15 + (r + delta) * Math.cos((-popangle-15) * rad), cy + (r + delta) * Math.sin((-popangle-15) * rad), labels[j].name.toUpperCase(), paper.getFont("Ubuntu Condensed",400),18).attr({fill: bcolor, stroke:bcolor, "stroke-width" : 1});
          
          percentageText = paper.print(cx - 22 + (overlayR) * Math.cos((-popangle-90) * rad), cy + (overlayR) * Math.sin((-popangle-90) * rad), labels[j].percentage, paper.getFont("Passion One",400),30).attr({fill: "#fff", stroke: bcolor, "stroke-width": 0.6});
            //txt.stop().animate({opacity: 1}, ms, "elastic");
          ontop.push(percentageText);
          ontop.push(txt);

          var
            clickFilterValues = ['M', 'K'];

          p.mouseover(function () {
              // p.stop().animate({transform: "s1.1 1.1 " + cx + " " + cy}, ms, "elastic", function() {
              // });
              // txt.stop().animate({opacity: 1}, ms, "elastic");
          }).mouseout(function () {
              // p.stop().animate({transform: ""}, ms, "elastic", function() {
              //   // if(typeof pi.events.publish == "function") {
              //   //   pi.events.publish("pi.app.views.sex.filter", { key : "sex", value : null});
              //   // }
              // });
              // txt.stop().animate({opacity: 0.5}, ms);
          }).click(function() {
              if(typeof pi.events.publish == "function") {
                pi.events.publish("pi.app.views.sex.filter", { key : "sex", value : clickFilterValues[j%2]});
              }
          });

          angle += angleplus;
          chart.push(p);
          chart.push(txt);
          start += .1;
      };


    var
      count = values.length;    

    for (var i = count;i--;) {
      // calculate total items
      total += values[i];
    }
    for (i = 0; i < count; i++) {
      // draw the pie slices
      process(i);
    }

    overlay(cx, cy, overlayR, 90,360+89,{opacity: 0.18, fill: "#000"});
//    paper.circle(100,100,70).attr({opacity: 0.15, fill: "#000"})
    
    ontop.forEach(function(p){
      if(typeof p.toFront == "function"){
        p.toFront();
      }
    });

    return chart;
};