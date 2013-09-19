
// ensure we have a global views object
var views = views || { widgetprefix : "views-widget-gauge-", charts : {} };

$(document).ready(function () {

        var colors = Highcharts.getOptions().colors,
            categories = ['Seniorer'],
            name = 'Seniorer',
            data = [{
                    y: 55.11,
                    color: "#ff9933",
                    drilldown: {
                        name: 'MSIE versions',
                        categories: ['Andel', ''],
                        data: [24, 100-24],
                        color: colors[0]
                    }
                }];
    
    
        // Build the data arrays
        var browserData = [];
        var versionsData = [];
        for (var i = 0; i < data.length; i++) {
    
            // add browser data
            browserData.push({
                name: categories[i],
                y: data[i].y,
                color: "#ffffff"
            });
    
            // add version data
            for (var j = 0; j < data[i].drilldown.data.length; j++) {
                var brightness = 0.2 - (j / data[i].drilldown.data.length) / 5 ;
                versionsData.push({
                    name: data[i].drilldown.categories[j],
                    y: data[i].drilldown.data[j],
                    color: Highcharts.Color(data[i].color).brighten(brightness).get()
                });
            }
        }
    

    	var options =  {
            chart: {
                renderTo: 'views-widget-gauge-01',
                type: 'pie',
                margin: [0, 0, 0, 0],
                spacingTop: 0,
                spacingBottom: 0,
                spacingRight: 0,
                spacingLeft: 0,
                backgroundColor:'rgba(255, 255, 255, 0)'
            },
            title: null,
            yAxis: {
                title: null
            },
            credits : {
            	enabled : false
            },
            plotOptions: {
                pie: {
                    shadow: false
                }
            },
            tooltip: {},
            series: [{
                name: 'Browsers',
                data: browserData,
                size: '60%',
                dataLabels: {
                    formatter: function() {
                        return this.y > 5 ? this.point.name : null;
                    },
                    color: 'black',
                    distance: -30
                }
            }, {
                name: 'Versions',
                data: versionsData,
                innerSize: '60%',
                dataLabels: {
                    formatter: function() {
                        // display only if larger than 1
                        return this.y > 1 ? '<b>'+ this.point.name +':</b> '+ this.y +'%'  : null;
                    }
                }
            }]
        }
        // Create the chart
        chart1 = new Highcharts.Chart(options);
        options.chart.renderTo = "views-widget-gauge-02";
        chart2 = new Highcharts.Chart(options);
        options.chart.renderTo = "views-widget-gauge-03";
        chart3 = new Highcharts.Chart(options);
        options.chart.renderTo = "views-widget-gauge-04";
        chart4 = new Highcharts.Chart(options);
        options.chart.renderTo = "views-widget-gauge-05";
   //     chart5 = new Highcharts.Chart(options);
    });

