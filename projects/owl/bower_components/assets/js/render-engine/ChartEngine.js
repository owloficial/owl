var ChartEngine = function(){
    this.render = function(columns, id, dataSource, title, type, widget){
        var columnNumber = columns.length;
            var series = [];
            var gridColorChart = "gray";
            var titleChart = "#FFCC00";
            var legendColorChart = "black";
            var labelFontFamilyChart = '"Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif';
            var labelFontSizeChart = 10;
            var labelAngleChart = -30;
            var colorBackgroundData = ["#4169E1","#7A8090", "#66CDAA"];

            var clickEvent = function(e){
                            widget.setCurrentRow(e.dataPointIndex);
            };
            var dataPointsMap = function(item){
                            return {label:item[columns[0].name], y: parseFloat(item[columns[i].name])};
            };

            for(var i=1;i<columnNumber;i++){
                series.push({
                        type: type,
                        legend: {
                            verticalAlign: "bottom",
                            horizontalAlign: "center",
                            fontColor:'gray',
                            fontSize: labelFontSizeChart
                        },
                        color : colorBackgroundData[i-1],
                        click: clickEvent,
                        showInLegend: true,
                        legendText: columns[i].label,
                        dataPoints: dataSource.data.map(dataPointsMap) 
                    });
            }
            var chart = new CanvasJS.Chart("chartdiv"+id, { 
                backgroundColor: "#f0eeea",
                title: {
                    text: title,
                    markerColor: "#CCC"
                },
                axisY: {
                    title: title,
                    labelFontSize : labelFontSizeChart,
                    labelAngle :labelAngleChart,
                    labelFontColor: legendColorChart,
                    labelFontFamily: labelFontFamilyChart,
                    gridColor: gridColorChart
                },
                axisX: {
                    title: title,
                    labelFontSize : labelFontSizeChart,
                    labelAngle :labelAngleChart,
                    labelFontColor: legendColorChart,
                    labelFontFamily: labelFontFamilyChart,
                    gridColor: gridColorChart
                },
                legend: {
                    title: title,
                    labelFontSize : labelFontSizeChart,
                    labelAngle :labelAngleChart,
                    labelFontColor: legendColorChart,
                    labelFontFamily: labelFontFamilyChart,
                    fontColor: legendColorChart
                },
                toolTip:{
                    borderColor: gridColorChart
        
                },
                theme: "theme2",
                data: series
            });
            chart.render();                             
    };
};