Raphael.fn.donutChart = function (cx, cy, r, rin, values, labels, stroke) {
    var paper = this,
        rad = Math.PI / 180,
        chart = this.set(),
        percentageText = null,
        percentageLayer = null,
        overlayR = rin + Math.round((r-rin)/2),
       colors = ['#303030', '#FF00DC', '#FF006E', '#FF0000', '#FF6A00', '#FFD800', '#ADF200', '#C5EAEA', '#0094FF', '#BE3DFF', '#999999'];

    function sector(cx, cy, r, startAngle, endAngle, params) {
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
        
    }

    function overlay(cx, cy, overlayR, startAngle, endAngle, params) {
        //console.log(params.fill);
         var 
            x1 = cx + overlayR * Math.cos(-startAngle * rad),
            x2 = cx + overlayR * Math.cos(-endAngle * rad),
            y1 = cy + overlayR * Math.sin(-startAngle * rad),
            y2 = cy + overlayR * Math.sin(-endAngle * rad),
            xx1 = cx + rin * Math.cos(-startAngle * rad),
            xx2 = cx + rin * Math.cos(-endAngle * rad),
            yy1 = cy + rin * Math.sin(-startAngle * rad),
            yy2 = cy + rin * Math.sin(-endAngle * rad);
        
        return paper.path(["M", xx1, yy1,
                           "L", x1, y1, 
                           "A", overlayR, overlayR, 0, +(360 - 0 > 180), 0, x2, y2, 
                           "L", xx2, yy2, 
                           "A", rin, rin, 0, +(360 - 0 > 180), 1, xx1, yy1, "z"]
                         ).attr(params);
        
    }

    var angle = 0,
        total = 0,
        start = 0,
        process = function (j) {
            var value = values[j],
                angleplus = 360 * value / total,
                popangle = angle + (angleplus / 2),
                color = colors[j],
                ms = 500,
                delta = 12,
                bcolor = colors[j],
                p = sector(cx, cy, r, angle+90, angle + angleplus+90, {fill: "90-" + bcolor + "-" + color, stroke: stroke, "stroke-width": 2}),
                txt = paper.print(cx - 15 + (r + delta) * Math.cos((-popangle-90) * rad), cy + (r + delta + 20) * Math.sin((-popangle-90) * rad), labels[j].name.toUpperCase(), paper.getFont("Ubuntu Condensed",400),18).attr({fill: bcolor, stroke:bcolor, "stroke-width" : 1});
                percentageText = paper.print(cx - 22 + (overlayR) * Math.cos((-popangle-90) * rad), cy + (overlayR) * Math.sin((-popangle-90) * rad), labels[j].percentage, paper.getFont("Passion One",400),30).attr({fill: "#fff", stroke: bcolor, "stroke-width": 0.6});
                //txt.stop().animate({opacity: 1}, ms, "elastic");
         
            p.mouseover(function () {
                //p.stop().animate({transform: "s1.1 1.1 " + cx + " " + cy}, ms, "elastic");
                //txt.stop().animate({opacity: 1}, ms, "elastic");
            }).mouseout(function () {
                //p.stop().animate({transform: ""}, ms, "elastic");
                //txt.stop().animate({opacity: 0.5}, ms);
            });

            angle += angleplus;
            chart.push(p);
            chart.push(txt);
            start += .1;
        };
    for (var i = 0, ii = values.length; i < ii; i++) {
        total += values[i];
    }
    for (i = 0; i < ii; i++) {
        process(i);
    }
    overlay(cx, cy, overlayR, 90,360+89,{opacity: 0.18, fill: "#000"});
//    paper.circle(100,100,70).attr({opacity: 0.15, fill: "#000"})
		//percentageText.toFront();					
    return chart;
};

