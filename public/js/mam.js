/**
 *
 * @type {{baseData: null, interval: null, init: init, initEvent: initEvent, initData: initData, showData: showData, updateData: updateData, clearData: clearData}}
 */
var mam = {

    baseData: null,
    interval: null,

    init: function() {
        this.initEvent();
        this.initData();
    },

    initEvent: function() {

        var root = this;

        $(document).on('click', '[data-ma-target][data-ma-symbol]', function(){

            var $this = $(this);
            var symbol = $this.attr('data-ma-symbol');
            var $target = $($this.attr('data-ma-target'));
            //
            root.showData(symbol, $target);
            $target.find('[data-ma-symbol].active').removeClass('active');
            $this.addClass('active');
            return false;
        });
    },

    initData: function(stareModal) {
        if (stareModal) {
            var $active = $('#modal-analysis [data-ma-target][data-ma-symbol].active');
        } else {
            var $active = $('#analysis [data-ma-target][data-ma-symbol].active');
        }
        if ($active && $active.length) {
            var symbol = $active.attr('data-ma-symbol');
            var $target = $($active.attr('data-ma-target'));
            this.showData(symbol, $target);
        }
    },

    showData: function(symbol, $target) {
        var root = this;
        $.ajax({
            url: 'http://api.markets.wallstreetcn.com/v1/quote.json/' + symbol,
            dataType: 'jsonp',
            success: function(result, request) {
                //
                root.clearData();
                //
                root.baseData = result['results'];
                //显示数据
                root.updateData(symbol, $target);
                //实时跟新数据
                root.interval = setInterval(function(){
                    root.updateData(symbol, $target);
                }, 1000*10);
            },
            failure: function() {
                root.showData(symbol, $target);
            }
        });
    },

    updateData: function(symbol, $target) {
        var root = this;
        var baseData = root.baseData;
        $.ajax({
            url: 'http://api.markets.wallstreetcn.com/v1/price.json?symbol=' + symbol,
            dataType: 'jsonp',
            success: function(result, request) {

                console.log(symbol);
                console.log('i am in~~~~~~~~~~~~~~~~~~~~~~~~~~~~');
                var data = result['results'][0];
                data.title = baseData.title;
                data.prevClose = parseFloat(baseData.prevClose);
                data.open = parseFloat(baseData.open);
                /*
                 var fixLength = baseData['numberFormat'].length - 2;
                 data.diff = (data['price'] - data.prevClose).toFixed(fixLength);
                 data.diffPercent = ((data['price'] - data.prevClose) * 100 / data.prevClose).toFixed(2) + '%';
                 data.high = baseData['dayRangeHigh'];
                 data.low = baseData['dayRangeLow'];
                 */
                data.updateTime = moment.unix(data.timestamp).format('YYYY年MM月DD日 HH:mm:ss');
                if (data['price'] > data.prevClose) {
                    data.trend = 'up';
                    //data.diffPercent = '+' + data.diffPercent;
                    //data.diff = '+' + data.diff;
                } else if (data['price'] < data.prevClose) {
                    data.trend = 'down';
                } else {
                    data.trend = '';
                }
                //console.log(data);
                //todo 跟新视图数据
                var $script = $target.find('script[data-template-target]');
                var $htmlTarget = $($script.attr('data-template-target'));
                var html = _.template($script.html(), data);
                //console.log(html);
                $htmlTarget.html(html);
            }
        });
    },

    clearData: function() {
        if (this.interval) {
            clearInterval(this.interval);
        }
    }

};
