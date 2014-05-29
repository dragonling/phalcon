/**
 * Created by tl on 14-5-7.
 */
(function(){

    function Cftc(options) {
        this.symbol = options.symbol;
        this.$target   = options.$target;
        this.dataTemp  = this.$target.find('script[data-cftc-template=data]').html();
        this.$dataDom  = this.$target.find('[data-cftc-dom=data]');
        this.$chartDom = this.$target.find('[data-cftc-dom=chart]');
        this.config = {};
        this.config.chartWidth = options.chartWidth;
        this.config.chartHeight = options.chartHeight;
        this.config.size = options.size || 5;
        this.config.chart = options.chart;
        this.init();
    };
    Cftc.prototype.init = function() {
        this.initData();
    };
    Cftc.prototype.initData = function() {
        var root = this;
        var symbol = root.symbol;
        var size = root.config.size;
        $.ajax({
            url: 'http://api.markets.wallstreetcn.com/v1/chart.json?symbol=' + symbol + 'CFTC&interval=1d&rows=50',
            dataType: 'jsonp',
            success: function(response) {
                var results = response['results'];
                if (results.length) {
                    var data = [];
                    var chartData = [];
                    var i = 0;
                    var l = results.length - 1;
                    for (i = 0; i < l; i ++) {
                        var item = {};
                        item.utm = results[i].start;
                        item.date = moment.unix(results[i].start).format('YYYY.MM.DD');
                        //nc: non-commercial  非-商业
                        //lp: Long positions  多头持仓
                        //sp: Short positions 空头持仓
                        //op: open position
                        item.nc_high = results[i].open;
                        item.nc_low = results[i].close;
                        item.nc_diff = item.nc_high - item.nc_low;
                        item.nc_change = results[i].high;
                        item.c_high = results[i].price;
                        item.c_low = results[i].low;
                        item.op = results[i].volume;
                        item.op_change = results[i].volume - results[i+1].volume;
                        data.push(item);
                        chartData.push([results[i].start*1000, item.nc_diff]);
                    }
                    chartData.sort(function(arg1, arg2){
                        return arg1[0] - arg2[0];
                    });
                    if (root.config.chart) {
                        root.initChart(chartData);
                    }
                    if (data.length > size) {
                        data = data.slice(0, size);
                    }
                    var html = _.template(root.dataTemp, {
                        data: data
                    });
                    root.$dataDom.html(html);
                }
            }
        });
    };
    Cftc.prototype.initChart = function(data) {
        var l = data.length;
        var i;
        var max = data[0][1];
        var min = data[0][1];
        for (i = 0; i < l; i++) {
            if (data[i][1] > max) {
                max = data[i][1];
            } else if (data[i][1] < min) {
                min = data[i][1];
            }
        }
        var digit = (parseInt(min) + '').length - 1;
        var unit;
        switch (digit) {
            case 3 :
                unit = 'k';
                break;
            case 4 :
                unit = 'w';
                break;
            default :
                unit = false;
        }
        /*
         for (i = 0; i < l; i++) {
         if (data[i][1] < min) {
         min = data[i][1];
         }
         }
         */
        this.$chartDom.highcharts({
            chart: {
                type: 'area',
                zoomType: 'x',
                spacing: 1,
                backgroundColor: '#000',
                plotBorderWidth: 1,
                plotBorderColor: '#323232'
                //width: this.config.chartWidth,
                //height: this.config.chartHeight
            },
            title: false,
            subtitle: false,
            credits: false,
            legend: false,
            xAxis: {
                allowDecimals: false,
                type: 'datetime',
                labels: {
                    formatter: function() {
                        return Highcharts.dateFormat('%m-%d', this.value);
                    }
                },
                minPadding: 0,
                maxPadding: 0,
                lineWidth: 0,
                tickLength: 0,
                //lineColor: '#323232',
                //tickColor: '#323232',
                gridLineColor: '#323232',
                gridLineWidth: 1
            },
            yAxis: {
                title: false,
                min: min,
                max: max,
                endOnTick: false,
                maxPadding: 0.01,
                labels: {
                    /*formatter: function() {
                        if (unit) {
                            return (parseInt(this.value) + '').slice(0 , -digit) + unit;
                        } else {
                            return this.value;
                        }
                    }*/
                },
                allowDecimals: true,
                opposite: true,
                //lineWidth: 1,
                //lineColor: '#323232',
                gridLineColor: '#323232',
                gridLineWidth: 1
            },
            tooltip: {
                formatter: function() {
                    return Highcharts.dateFormat('%Y年%m月%d日 %H:%M:%S', this.x) +
                        '<br/>CFTC：<b>'+ this.y +'</b>';
                }
            },
            plotOptions: {
                area: {
                    marker: {
                        enabled: false,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    },
                    lineColor: '#03f',
                    lineWidth: 1,
                    fillColor: {
                        linearGradient: [0, 0, 0, 300],
                        stops: [
                            [0, 'rgba(50,50,255,1)'],
                            [1, 'rgba(30,30,30,0)']
                        ]
                    }
                }
            },
            series: [{
                name: 'CFTC',
                data: data
            }]
        });
    };

    $.fn.cftc = function(inputOptions) {
        if (! this.length) {
            return;
        }
        for (var l = this.length - 1; l > -1; l --) {
            var $this = $(this[l]);
            if ($this.attr('data-cftc') === 'initialized') {
                return;
            }
            $this.attr('data-cftc', 'initialized');
            var options = {
                $target: $this
            };
            var domOptions = {};
            var str = $this.attr('data-cftc-option');
            if (str) {
                domOptions = tool.parseDomData(str);
            }
            $.extend(options, domOptions, inputOptions);
            new Cftc(options);
        }
    };

})();