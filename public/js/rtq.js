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
        this.config.symbolEvent = options.symbolEvent === false ? false : true;
        this.config.formatPrice = options.formatPrice;
        this.config.updateTimeFormat = options.updateTimeFormat || 'YYYY年MM月DD日 HH:mm:ss';
        this.config.interval = options.interval || 10 * 1000;
        this.init();
    };
    Rtq.prototype.init = function() {
        var $active = this.$target.find('[data-rtq-symbol].active');
        var symbol = $active.attr('data-rtq-symbol');
        this.showData(symbol);
    };
    Rtq.prototype.initData = function(symbol) {
        var root = this;
        var $target = this.$target;
        var $active = $target.find('[data-rtq-symbol].active');
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
                //实时跟新数据
                root.interval = setInterval(function(){
                    root.updateData(symbol);
                }, root.config.interval);
            },
            error: function() {
                root.initData(symbol);
            }
        });
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
    };
    Rtq.prototype.showData = function(symbol) {
        var root = this;
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
                //实时跟新数据
                root.interval = setInterval(function(){
                    root.updateData(symbol);
                }, root.config.interval);
            },
            error: function() {
                root.initData(symbol);
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
        if (this.attr('data-rtq') === 'initialized') {
            return;
        }
        var $this = this;
        $this.attr('data-rtq', 'initialized');
        var options = {
            $target: $this
        };
        var domOptions = {};
        var str = $this.attr('data-rtq-option');
        if (str) {
            domOptions = tool.parseStringToObject(str);
        }
        $.extend(options, domOptions, inputOptions);
        new Rtq(options);
    }

})();
