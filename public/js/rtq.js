/**
 * Created by tl on 14-4-30.
 */
//Real Time Quotes
(function(){
    function Rtq(options) {
        this.$target = options.$target;
        this.$dom = this.$target.find('[data-rtq-dom]');
        this.$script = this.$target.find('script[data-rtq-template]');
        this.baseData = null;
        this.interval = null;
        this.config = {};
        this.config.overview = options.overview;
        this.config.defaultInterval = options.defaultInterval;
        if (this.config.overview) {
            this.$overviewDom = this.$target.find('[data-overview-dom]');
            this.$overviewScript = this.$target.find('[data-overview-template]');
        }
        this.config.defaultSymbol = options.defaultSymbol;
        this.config.symbolEvent = options.symbolEvent === false ? false : true;
        this.config.formatPrice = options.formatPrice;
        this.config.updateTimeFormat = options.updateTimeFormat || 'YYYY年MM月DD日 HH:mm:ss';
        this.config.interval = options.interval || 10 * 1000;
        this.init();
    };
    Rtq.prototype.init = function() {
        this.initData();
        this.initEvent();
    };
    Rtq.prototype.initData = function() {
        var $active = this.$target.find('[data-rtq-symbol].active');
        if ($active.length) {
            var symbol = $active.attr('data-rtq-symbol');
        } else {
            var symbol = this.config.defaultSymbol;
            var match = location.href.match(/\/techanalysis\/\w+/);
            if (match && match.length) {
                symbol = match[0].substring('/techanalysis/'.length);
            }
            this.$target.find('[data-rtq-symbol=' + symbol + ']').addClass('active');
        }
        this.showData(symbol);
    };
    Rtq.prototype.initEvent = function() {
        var root = this;
        var $target = this.$target;
        if (this.config.symbolEvent) {
            $target.on('click', '[data-rtq-symbol]', function(e){
                var $this = $(this);
                var symbol = $this.attr('data-rtq-symbol');
                root.showData(symbol);
                $target.find('[data-rtq-symbol].active').removeClass('active');
                $this.addClass('active');
                e.preventDefault();
            });
        }
        if (this.config.overview) {
            $target.on('click', '[data-rtq-interval]', function(e){
                var $this = $(this);
                var interval = $this.attr('data-rtq-interval');
                root.overview(interval);
                $target.find('[data-rtq-interval].active').removeClass('active');
                $this.addClass('active');
                e.preventDefault();
            });
        }
    };
    Rtq.prototype.overview = function(interval) {
        var root = this;
        $.ajax({
            url: 'http://www.goldtoutiao.com/data/techanalysis/' + root.symbol + '/' + interval,
            dataType: 'json',
            success: function(response) {
                var html = '';
                if (response.data) {
                    html = _.template(root.$overviewScript.html(), {
                        data: response.data
                    });
                    //console.log('');
                }
                root.$overviewDom.html(html);
            },
            error: function() {
                //root.overview(interval);
            }
        });
    };
    Rtq.prototype.showData = function(symbol) {
        var root = this;
        root.symbol = symbol;
        $.ajax({
            url: 'http://api.markets.wallstreetcn.com/v1/quote.json/' + symbol,
            dataType: 'jsonp',
            success: function(result) {
                //清除之前的数据 并 取消 interval
                root.clearData();
                //
                root.baseData = result['results'];
                //显示数据
                root.updateData(symbol);
                //是否显示 overview
                if (root.config.overview) {
                    var $interval = root.$target.find('[data-rtq-interval].active');
                    if ($interval.length) {
                        root.overview($interval.attr('data-rtq-interval'));
                    } else {
                        root.overview(root.config.defaultInterval);
                    }

                }
                //实时跟新数据
                root.interval = setInterval(function(){
                    root.updateData(symbol);
                }, root.config.interval);
            },
            error: function() {
                root.showData(symbol);
            }
        });
    };
    Rtq.prototype.updateData = function(symbol) {
        var root = this;
        var $script = root.$script;
        var $dom = root.$dom;
        var baseData = this.baseData;
        $.ajax({
            url: 'http://api.markets.wallstreetcn.com/v1/price.json?symbol=' + symbol,
            dataType: 'jsonp',
            success: function(result, request) {
                var data = result['results'][0];
                data.title = baseData.title;
                data.prevClose = parseFloat(baseData.prevClose);
                data.open = parseFloat(baseData.open);
                var fixLength = baseData['numberFormat'].length - 2;
                data.diff = (data['price'] - data.prevClose).toFixed(fixLength);
                data.diffPercent = ((data['price'] - data.prevClose) * 100 / data.prevClose).toFixed(2) + '%';
                data.high = baseData['dayRangeHigh'];
                data.low = baseData['dayRangeLow'];
                if (root.config.formatPrice) {
                    data.price = root.formatPrice(data.price);
                    data.prevClose = root.formatPrice(data.prevClose);
                    data.open = root.formatPrice(data.open);
                    data.high = root.formatPrice(data.high);
                    data.low = root.formatPrice(data.low);
                }
                data.updateTime = moment.unix(data.timestamp).format(root.config.updateTimeFormat);
                if (data['price'] > data.prevClose) {
                    data.trend = 'up';
                    data.diffPercent = '+' + data.diffPercent;
                    data.diff = '+' + data.diff;
                } else if (data['price'] < data.prevClose) {
                    data.trend = 'down';
                } else {
                    data.trend = '';
                }
                //console.log(data);
                //todo 跟新视图数据
                var html = _.template($script.html(), data);
                //console.log(html);
                $dom.html(html);
            }
        });
    };

    Rtq.prototype.clearData = function() {
        if (this.interval) {
            clearInterval(this.interval);
        }
    };
    Rtq.prototype.formatPrice = function(price) {
        var num = + price;
        if (typeof num != 'number') {
            return price;
        }
        var abs = Math.abs(num);
        if (abs < 1000) {
            return price;
        }
        if (num > 0) {
            var flag = '';
        } else {
            var flag = '-'
        }
        abs = '' + abs;
        var array = abs.split('.');
        var str = array[0];
        var decimal = '.00';
        if (array.length>1) {
            decimal = '.' + array[1];
        }
        var i = 0;
        var l = Math.floor(str.length / 3);
        var mod = str.length % 3;
        var temp = str.substring(0, mod);
        for (; i < l; i ++) {
            temp += ',' + str.substring(mod + i * 3, mod + (i + 1) * 3);
        }
        if (mod == 0) {
            temp = temp.substring(1);
        }
        return flag + temp + decimal;
    }


    $.fn.rtq = function(inputOptions) {
        if (! this.length) {
            return;
        }
        for (var l = this.length - 1; l > -1; l --) {
            var $this = $(this[l]);
            if ($this.attr('data-rtq') === 'initialized') {
                return;
            }
            $this.attr('data-rtq', 'initialized');
            var options = {
                $target: $this
            };
            var domOptions = {};
            var str = $this.attr('data-rtq-option');
            if (str) {
                domOptions = tool.parseDomData(str);
            }
            $.extend(options, domOptions, inputOptions);
            new Rtq(options);
        }
    }

})();
