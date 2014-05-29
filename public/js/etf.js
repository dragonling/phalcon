/**
 * Created by tl on 14-5-7.
 */
(function(){

    function Etf(options) {
        this.symbol = options.symbol;
        this.$target   = options.$target;
        this.dataTemp = this.$target.find('script[data-etf-template=data]').html();
        this.$dataDom  = this.$target.find('[data-etf-dom=data]');
        this.$chartDom = this.$target.find('[data-etf-dom=chart]');
        this.config = {};
        this.config.size = options.size || 5;
        this.config.chart = options.chart;
        this.init();
    };
    Etf.prototype.init = function() {
        this.initData();
        if (this.config.chart) {
            this.initChart();
        }
    };
    Etf.prototype.initData = function() {
        var root = this;
        var symbol = root.symbol;
        var size = root.config.size;
        $.ajax({
            url: 'http://api.markets.wallstreetcn.com/v1/chart.json?symbol=' + symbol + 'ETF&interval=1d&rows=50',
            dataType: 'jsonp',
            success: function(response) {
                var results = response['results'];
                if (results.length) {
                    var data = [];
                    var i = 0;
                    var l = results.length;
                    l = l > size ? size : l;
                    for (i = 0; i < l; i ++) {
                        var item = {};
                        item.utm = results[i].start;
                        item.date = moment.unix(results[i].start).format('YYYY.MM.DD');
                        item.etf_oz = results[i].open;
                        item.etf_ton = results[i].close;
                        item.price = results[i].high;
                        item.change = (results[i].low * 31.1034768 / 1000000).toFixed(2) ;
                        data.push(item);
                    }
                    var html = _.template(root.dataTemp, {
                        data: data
                    });
                    root.$dataDom.html(html);
                }
            }
        });
    };
    Etf.prototype.initChart = function(data) {
        var symbol = this.symbol;
        var data;
        var root = this;
        $.ajax({
            url: 'http://api.markets.wallstreetcn.com/v1/chart.json?symbol=' + symbol + 'ETF&interval=1d&rows=50&highchart=area',
            dataType: 'jsonp',
            success: function(response) {
                if (response.length) {
                    data = response;
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
                    /*
                    data.sort(function(arg1, arg2){
                        return arg1[0] - arg2[0];
                    });
                    */
                    console.log('max is : ' + max + '; min is : ' + min);
                    root.$chartDom.highcharts({
                        chart: {
                            type: 'area',
                            zoomType: 'x',
                            spacing: 1,
                            backgroundColor: '#000',
                            plotBorderWidth: 1,
                            plotBorderColor: '#323232'
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
                            maxPadding: 0.01,
                            endOnTick: false,
                            labels: {
                                formatter: function() {
                                    return this.value;
                                }
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
                                    '<br/>ETF值：<b>'+ this.y +'</b>';
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
                            name: 'ETF',
                            data: data
                        }]
                    });
                }
            }
        });
    };

    $.fn.etf = function(inputOptions) {
        if (! this.length) {
            return;
        }
        var results = [];
        for (var l = this.length - 1; l > -1; l --) {
            var $this = $(this[l]);
            if ($this.attr('data-etf') === 'initialized') {
                continue;
            }
            $this.attr('data-etf', 'initialized');
            var options = {
                $target: $this
            };
            var domOptions = {};
            var str = $this.attr('data-etf-option');
            if (str) {
                domOptions = tool.parseDomData(str);
            }
            $.extend(options, domOptions, inputOptions);
            var result = new Etf(options);
            results.push(result);
        }
        if (results.length == 1) {
            return results[0];
        } else {
            return results;
        }

    };

})();