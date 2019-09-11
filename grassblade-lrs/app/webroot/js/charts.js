function characteristic_curve(objectid) {
    var url = ajaxurl + "Reports/characteristic_curve?objectid="+objectid;        
    if(characteristic_curve_data)
    {
        jQuery("#dashboard_chart_portlet .tools .expand.minimize").click();
        characteristic_curve_plot(characteristic_curve_data);
        jQuery("#dashboard_characteristic_curve_legend tr").css("cursor", "pointer");

        var activity_index = 1;
        jQuery.each(characteristic_curve_data, function(i, v) { 
            var line = new Array();
            jQuery.each(v, function(j, w) {line[j] = w[1];});
            console.log(line);
            jQuery("#report_table .statement-activity-" + activity_index + " .characteristic_curve div").sparkline(line, {type: 'line'});
            activity_index++;
        });

    }
}
function characteristic_curve_plot(dataset) {
       var plotdata = new Array();       
        var i = 0;
        $.each(dataset, function(key, val) { 
            plotdata[i] = { data:val, label:key};
            i++;
             });        
        var plot = $.plot("#dashboard_characteristic_curve", plotdata, {
            series: {
               lines: {
                        show: true,
                        lineWidth: 2,
                        fill: false,
                        fillColor: {
                            colors: [{
                                    opacity: 0.05
                                }, {
                                    opacity: 0.01
                                }
                            ]
                        }
                    },
                    points: {
                        show: true,
                        radius: 3,
                        lineWidth: 1
                    },
                   /* shadowSize: 2*/
            },            
            grid: {
              hoverable: true,
                    clickable: true,
                    tickColor: "#eee",
                    borderColor: "#eee",
                    borderWidth: 1
            },
            legend: { container: "#dashboard_characteristic_curve_legend" },
            xaxis: {
                min: 0,
                max: 100
            },
            yaxis: {                
                min: 0.0,
                max: 1.2
            }
        });

         function showTooltip(x, y, contents) { 
            $('<div id="tooltip">' + contents + '</div>').css({
                    position: 'absolute',
                    display: 'none',
                    top: y + 5,
                    left: x + 15,
                    border: '1px solid #333',
                    padding: '4px',
                    color: '#fff',
                    'border-radius': '3px',
                    'background-color': '#333',
                    opacity: 0.80
                }).appendTo("body").fadeIn(200);
        reposition_tooltip(x,y,300);
        }

        var previousPoint = null;
        $("#dashboard_characteristic_curve").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));

            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = item.datapoint[0],
                        y = item.datapoint[1];
                     //   xx = format_date(new Date(x));
                     // console.log(x + ":" + xx);
                    showTooltip(item.pageX, item.pageY, build_tool_tip(item.series.label, x, y));
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;
            }

            //var seriesIndex = (item)? item.seriesIndex:null;
            //highlight_plot(seriesIndex);
        });
        $("#dashboard_characteristic_curve").bind("plotclick", function (event, pos, item) {
            var seriesIndex = (item)? item.seriesIndex:null;
            highlight_plot(seriesIndex);
        });
        $("#dashboard_characteristic_curve_legend tr, #report_table tr").click(function (event) {
            //if(event.type == "mouseenter") 
            {
                if(row_no = jQuery(this).data('row-no'))
                var seriesIndex = row_no - 1;
                else     
                var seriesIndex = jQuery(this).prevAll().length; 
             //   console.log("mouseenter seriesIdx: " + seriesIndex);

                if(seriesIndex == -1)
                    seriesIndex = 0;
                highlight_plot(seriesIndex);            
            }
        }); 
        var plot_highlighted = false;
        function highlight_plot(seriesIndex) {
            var re = re = /\(([0-9]+,[0-9]+,[0-9]+)/;
            var opacity = 1;
            var seriesIdx = -1;
            // note which item is clicked on
            //console.log("seriesIndex: " + seriesIndex);
            //if (seriesIndex != null) 
            {
                seriesIdx = seriesIndex;
                opacity = 0.1;
            }

            if(plot_highlighted == seriesIdx && seriesIdx != -1)
                return;

            if(seriesIdx == -1)
            {
                if(!plot_highlighted)
                    return;
                else
                    plot_highlighted = false;
            }
            else
            plot_highlighted = seriesIdx;

            // loop all the series and adjust the opacity 
            var modSeries = 
            $.map(plot.getData(),function(series,idx){
                //console.log("Idx: ");console.log(idx);
                //console.log("seriesIdx:") console.log(seriesIdx);
                if(seriesIdx == -1 || seriesIdx == null) {
                    series.lines.lineWidth = 2;
                    series.lines.show = true;
                    series.points.show = true;

                }
                else if (idx == seriesIdx){
                   //series.color = 'rgba(' + re.exec(series.color )[1] + ',' + 1 + ')';
                    series.lines.lineWidth = 5;
                    series.lines.show = true; //lineWidth = 5;
                    series.points.show = true;
                } else {
                    //series.color = 'rgba(' + re.exec(series.color )[1] + ',' + opacity + ')';
                    series.lines.show = false;//lineWidth = 0;
                    series.points.show = false;
                }
               return series;
            });
            // reload the series and redraw
            plot.setData(modSeries);
            plot.draw();            
        }
        function build_tool_tip(label, x, y) {
        var percentage = "0%";
        if(x > 0)
        percentage = (x-10) + "-" + x + "%";
            return "<div class='item_analysis_tooltip'><div class='tooltip_value'>" + (y * 100).toFixed(2) + "% of students</div>" + "<br>scoring " + percentage + " answered this quections correctly: <br><br>" + label + "</div>";// + "<br>";//<br><div class='tooltip_value'>" + y + "</div>";
        }
}
function reposition_tooltip(mousex,mousey, tooltip_width) {
   if(typeof tooltip_width == "undefined")
    tooltip_width = 100;
   if((mousex+tooltip_width+100)>$(window).width())
   {
      $('#tooltip')
    .css({ top: mousey ,left: mousex-tooltip_width-100})

   }
}
    function dashboard_chart_plot(dataset) {
        var position = "left";
        var plotdata = new Array();
        for (var i = 0; i < dataset.length; i++) {
           plotdata[i] = {  data: dataset[i].data, 
                            label: dataset[i].label,
                            lines: {
                                lineWidth: 1,
                            },
                            shadowSize: 5,
                            yaxis: i + 1 
                         };
        };
        function format_date(d) {
            return d.getMonth() + "/" + d.getDate();
        }
        function build_tool_tip(label, x, y) {
            return label + " on " + moment(x).format("MMM DD, YYYY") + "<br><div class='tooltip_value'>" + y + "</div>";
        }
        var plot = $.plot($("#dashboard_chart"), plotdata, {
                series: {
                    lines: {
                        show: true,
                        lineWidth: 2,
                        fill: true,
                        fillColor: {
                            colors: [{
                                    opacity: 0.05
                                }, {
                                    opacity: 0.01
                                }
                            ]
                        }
                    },
                    points: {
                        show: true,
                        radius: 3,
                        lineWidth: 1
                    },
                    shadowSize: 2
                },
                grid: {
                    hoverable: true,
                    clickable: true,
                    tickColor: "#eee",
                    borderColor: "#eee",
                    borderWidth: 1
                },
                legend: { position: "nw" },
                colors: ["#d12610", "#37b7f3", "#52e136"],
                xaxes: [ { mode: "time" } ],
                yaxes: [ { min: 0, minTickSize: 1, tickDecimals: 0 }, {
                        // align if we are to the right
                        alignTicksWithAxis: position == "right" ? 1 : null,
                        position: position,
                        min: 0,
                        minTickSize: 1,
                        tickDecimals: 0
                        /*tickFormatter: euroFormatter*/
                    } ]
            });


        function showTooltip(x, y, contents) {
            $('<div id="tooltip">' + contents + '</div>').css({
                    position: 'absolute',
                    display: 'none',
                    top: y + 5,
                    left: x + 15,
                    border: '1px solid #333',
                    padding: '4px',
                    color: '#fff',
                    'border-radius': '3px',
                    'background-color': '#333',
                    opacity: 0.80
                }).appendTo("body").fadeIn(200);
       reposition_tooltip(x,y,100);
        }

        var previousPoint = null;
        $("#dashboard_chart").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));

            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = item.datapoint[0],
                        y = item.datapoint[1];
                     //   xx = format_date(new Date(x));
                     // console.log(x + ":" + xx);
                    showTooltip(item.pageX, item.pageY, build_tool_tip(item.series.label, x, y));
                }
            } else {
                $("#tooltip").remove();
                previousPoint = null;
            }
        });
        if($('#reportrange .thin').text() == "") {
            $('#reportrange').daterangepicker({
                    opens: (jQuery("body").hasClass("rtl")? 'right' : 'left'),
                    startDate: moment().subtract('days', 29),
                    endDate: moment(),
                    minDate: '01/01/2012',
/*                    maxDate: '12/31/2014',*/
                    dateLimit: {
                        days: 365
                    },
                    showDropdowns: true,
                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 1,
                    timePicker12Hour: true,
                    ranges: {
                      //  'Today': [moment(), moment()],
                      // 'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                        'Last 7 Days': [moment().subtract('days', 6), moment()],
                        'Last 30 Days': [moment().subtract('days', 29), moment()],
                        'This Month': [moment().startOf('month'), moment()],
                        'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
                        'This Year': [moment().startOf('year'), moment()]
                    },
                    buttonClasses: ['btn'],
                    applyClass: 'green',
                    cancelClass: 'default',
                    format: 'MM/DD/YYYY',
                    separator: ' to ',
                    locale: {
                        applyLabel: 'Apply',
                        fromLabel: 'From',
                        toLabel: 'To',
                        customRangeLabel: 'Custom Range',
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        firstDay: 1
                    }
                },
                function (start, end) {
                 //   s = start;
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    start_i = start.format("YYYY-MM-DD");//start.toDate().getTime()/1000;//start.toISOString();
                    end_i = end.format("YYYY-MM-DD");//end.toDate().getTime()/1000;//end.toISOString();
                   // console.log("Callback has been called! "  + start_i + " to " + end_i);
                    databoard_chart(start_i, end_i);
                }
            );
            //Set the initial state of the picker label 
            //$('#reportrange span').html(moment().subtract('days', 29).format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
            $('#reportrange span').html(moment(dataset[0].data[0][0]).format('MMMM D, YYYY') + ' - ' + moment(dataset[0].data[dataset[0].data.length-1][0]).format('MMMM D, YYYY'));
        }
        $('#dashboard_chart_daycount').text(statements.length);
        $("#dashboard_chart_portlet .caption i").removeClass("fa-spin fa-refresh").addClass("fa-bar-chart-o");
    }
    function databoard_chart_reload() {
        if(statements != "" && verbs != "") {
                dashboard_chart_plot([{data:statements, label:"Statements"}, {data:verbs, label:"Verbs"}]);
        }
    }
    function databoard_chart(start, end) {
         statements = "";
         verbs = "";
         var params = '';

        var url = ajaxurl + "Reports/count/";

        if(start != undefined && end != undefined)
            params =  start + "/" + end + "/";

        jQuery("#dashboard_chart_portlet .caption i").removeClass("fa-bar-chart-o").addClass("fa-spin fa-refresh");

        jQuery.getJSON(url + "statements/" + params, function(data) {
            statements = data;
            if(verbs != "") {
                jQuery("#dashboard_chart_portlet .tools .expand.minimize").click();
                dashboard_chart_plot([{data:statements, label:"Statements"}, {data:verbs, label:"Verbs"}]);
            }
        });
        jQuery.getJSON(url + "verbs/" + params, function(data) {
            verbs = data;
            if(statements != "") {
                jQuery("#dashboard_chart_portlet .tools .expand.minimize").click();
                dashboard_chart_plot([{data:statements, label:"Statements"}, {data:verbs, label:"Verbs"}]);
            }
        });
    }



    function plot_report_bell_chart_load(at, score_type, objectid, agent_ids) {
        report_bell_scores = "";
        var params = '';

        var url = ajaxurl + "Reports/bell_chart/" + score_type + "?objectid=" + objectid;

        jQuery.each(agent_ids,  function(i, u) {
            url += "&agent_id[]=" + u;
        });

        jQuery("#dashboard_bell_chart_portlet .caption i").removeClass("fa-bar-chart-o").addClass("fa-spin fa-refresh");

        jQuery.getJSON(url, function(data) {
            jQuery("#dashboard_bell_chart_portlet .not_enough_data").hide();
            jQuery("#dashboard_bell_chart_portlet").removeClass("not_enough_data");

            jQuery("#dashboard_bell_chart_portlet .caption i").removeClass("fa-spin fa-refresh").addClass("fa-bar-chart-o");
            if(data == null || typeof data != "object")
            {
                //jQuery("#dashboard_bell_chart_portlet").hide();
                jQuery("#dashboard_bell_chart_portlet .not_enough_data").show();
                jQuery("#dashboard_bell_chart_portlet").addClass("not_enough_data");

                jQuery("#dashboard_bell_chart_portlet .tools .expand.minimize").click();
                setTimeout(function() {
                    jQuery("#dashboard_bell_chart_portlet .tools .collapse.minimize").click();
                }, 1000);
                return;
            }
            jQuery("#dashboard_bell_chart_portlet .tools .expand.minimize").click();
            report_bell_scores = data;
            plot_report_bell_chart(at, data);

        });   
    }
    function plot_report_bell_chart(at, data) {
        bell_averages = function(a) {
          var r = {mean: 0, variance: 0, deviation: 0}, t = a.length;
          for(var m, s = 0, l = t; l--; s += parseFloat(a[l]));
          for(m = r.mean = s / t, l = t, s = 0; l--; s += Math.pow(a[l] - m, 2));
          return r.deviation = Math.sqrt(r.variance = s / t), r;
        }
        report_array_data = function(a) {
            var r = {min: null, max: null};
            jQuery.each(a, function(i, d) {
                d = parseFloat(d);
                if(r.min == null)
                    r.min = d;
                if(r.max == null)
                    r.max = d;
                if(d < r.min)
                    r.min = d;
                if(d > r.max)
                    r.max = d;
            });
            return r;
        }
        bell_curve_fn = function(x, mean, variance) {
            if(variance == 0)
                return 1;
            else
            return ((1/( Math.sqrt( 2 * variance * Math.PI ))) * Math.exp((mean - x) * (x - mean)/(2 * variance)));
        }

        var bell_data = bell_averages(data.scores), min_max = report_array_data(data.scores);
        var min = bell_data.mean - 3.5 * bell_data.deviation;
        var max = bell_data.mean + 3.5 * bell_data.deviation;
        var step = 7*bell_data.deviation/200;
        var d1 = [], d2=[];
        var y_max = 0;
        for (var i = min; i <= max; i += step)
        {
            var y = bell_curve_fn(i, bell_data.mean, bell_data.variance);
           // console.log(i + ":" + y);
           d1.push([i, y]);
           if(y > y_max)
            y_max = y;
        }
        var scores = {}, scored_max = 0;

        jQuery.each(data.scores, function(i, d) {
            d = parseFloat(d);
           if(typeof scores[d] == "undefined")
                scores[d] = 1;
            else
                scores[d]++;

            if(scores[d] > scored_max)
                scored_max = scores[d];
        });
       // console.log(scores);
        jQuery.each(scores, function(i, d) {
            if(typeof d != "undefined") {
               // console.log(d + ":" + i * y_max/scored_max);
                d2.push([i, d]);
            }
        });

        var d3 = [];
        d3.push([bell_data.mean, 0]);
        d3.push([bell_data.mean, y_max*1.1]);

        var chart_i = 0;
        var chart_data = [];

        chart_data[chart_i++] = {
                label: " Bell Curve",
                data: d1,
                lines: {
                    lineWidth: 2,
                    fill: true,
                },
                points: { show: false},
                shadowSize: 0,
                yaxis: 1,
            };

        chart_data[chart_i++] = {
                label: " Score Distribution",
                data: d2,
                lines: {
                    show: false
                },
                points: { show: true, radius: 3, lineWidth: 5},
                shadowSize: 0,
                yaxis: 2,
            };

        chart_data[chart_i++] = {
                label: " Mean",
                data: d3,
                lines: {
                    lineWidth: 1,
                },
                points: { show: false},
                shadowSize: 0,
                yaxis: 1,
            };

        jQuery.each(data.user_scores, function(user, score) {
            var d = [];
            d.push([score, 0]);
            d.push([score, y_max*1.1]);

            chart_data[chart_i++] = {
                label: user,
                data: d,
                lines: {
                    lineWidth: 1,
                },
                points: { show: false},
                shadowSize: 0,
                yaxis: 1,
            };
        });

        jQuery.plot(jQuery(at), chart_data, {
            series: {
                lines: {
                    show: true,
                },
                points: {
                    show: true,
                    fill: true,
                    radius: 3,
                    lineWidth: 1
                }
            },
            xaxis: {
                tickColor: "#eee",
                ticks: [
                        [bell_data.mean - 3 * bell_data.deviation, "-3&sigma; = " + (bell_data.mean - 3 * bell_data.deviation).toFixed(2)],
                        [bell_data.mean - 2 * bell_data.deviation, "-2&sigma; = " + (bell_data.mean - 2 * bell_data.deviation).toFixed(2)],
                        [bell_data.mean - 1 * bell_data.deviation, "-1&sigma; = " + (bell_data.mean - 1 * bell_data.deviation).toFixed(2)],
                        [bell_data.mean , "&mu; = " + (bell_data.mean).toFixed(2)],
                        [bell_data.mean + 1 * bell_data.deviation, "-1&sigma; = " + (bell_data.mean + 1 * bell_data.deviation).toFixed(2)],
                        [bell_data.mean + 2 * bell_data.deviation, "-2&sigma; = " + (bell_data.mean + 2 * bell_data.deviation).toFixed(2)],
                        [bell_data.mean + 3 * bell_data.deviation, "-3&sigma; = " + (bell_data.mean + 3 * bell_data.deviation).toFixed(2)],

                    ],
                min: min,
                max: max
            },
            yaxis: {
                tickColor: "#eee",
                min: 0,
            },
            grid: {
                hoverable: true,
                clickable: true,
                borderColor: "#eee",
                borderWidth: 1
            }
        });
        function showTooltip(x, y, contents) {
            jQuery('<div id="tooltip">' + contents + '</div>').css({
                    position: 'absolute',
                    display: 'none',
                    top: y + 5,
                    left: x + 15,
                    border: '1px solid #333',
                    padding: '4px',
                    color: '#fff',
                    'border-radius': '3px',
                    'background-color': '#333',
                    opacity: 0.80
                }).appendTo("body").fadeIn(200);
        }
         jQuery(at).bind("plothover", function (event, pos, item) {
                jQuery("#x").text(pos.x.toFixed(2));
                jQuery("#y").text(pos.y.toFixed(2));

                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        jQuery("#tooltip").remove();
                        var x = item.datapoint[0],
                            y = item.datapoint[1];
                         //   xx = format_date(new Date(x));
                         // console.log(x + ":" + xx);
                         var tooltip = item.series.label + "<br>" + x.toFixed(2);

                         if(item.series.label.search("Distribution") >= 0)
                            tooltip += ", " + parseInt(y) + " times";

                        showTooltip(item.pageX, item.pageY, tooltip );
                    }
                } else {
                    jQuery("#tooltip").remove();
                    previousPoint = null;
                }
            });
    }

