
$(function(){
    /**
     * 导航栏高亮
     */
    var fullPathUrl = window.location.pathname + window.location.search;
    $('#header .navbar .link[data-active-url]').each(function(){
        var $item = $(this);
        var reg = new RegExp($item.attr("data-active-url"));
        if(reg.test(fullPathUrl)) {
            $item.addClass("active");
            //跳出循环
            return false;
        }
    });
    /**
     * gold 头条 自定义 事件
     *
     * @type {{}}
     */
    var GTT_ACTION = {
        //todo
        cftc: function() {
            $('[data-cftc]').cftc();
        }
    };
    /**
     * tabpanel
     */
    $(document).on('click.tab', '.tab', function(e){
        var $tab = $(this);
        var $tabbar = $tab.parent();
        var $tabs = $tabbar.children('.tab');
        var $content = $tabbar.next();
        var $panels = $content.children();
        var $activeTab = $tabbar.children('.active');
        var $activePanel = $content.children('.active');
        var index = $tabs.index($tab);
        console.log(index);
        var $panel = $($panels[index]);
        $activeTab.removeClass('active');
        $activePanel.removeClass('active');
        $tab.addClass('active');
        $panel.addClass('active');
        var action = $tab.attr('data-tab-action');
        if (action && typeof GTT_ACTION[action] === 'function') {
            GTT_ACTION[action]();
        }
        e.preventDefault();
    });
    /**
     * menu
     */
    $('[data-tree]').on('click', '.item', function(e){
        var $this = $(this);
        if ($this.is('[data-toggle=sub-menu]')) {
            var $parent = $this.parent();
            $parent.toggleClass('active');
        } else {
            var $menu = $this.parents('.menu');
            var $active = $menu.find('[data-tree-leaf].active');
            $active.removeClass('active');
            $this.addClass('active');
        }
        //e.preventDefault();

        /*var $parents = $this.parents('.item.active');
        var $menu = $this.parents('.menu');
        var $active = $menu.find('.item.active');
        if ($active.length) {
            if ($parents.length) {
                var array = $.grep($active, function(dom, i){
                    var l = $parents.length - 1;
                    for (l; l>-1; l--) {
                        if (dom.innerHTML == $parents[l].innerHTML) {
                            e.preventDefault();
                        }
                    }
                    return true;
                });
                for (var i=0; i<array.length; i++) {
                    $(array[i]).removeClass('active');
                }
            } else {
                $active.removeClass('active');
            }
        }*/

    });



});
/**
 * 汇率计算器
 */
$(function()
{
    var CER = {};
    var map = {
        CNY: 'USD',
        USD: 'CNY'
    };
    var $input = $('#gold-price-converter input[name=input]');
    var $result = $('#gold-price-converter input[name=result]');
    var $inputTypeDom = $('#gold-price-converter select[name=input-type]');
    var $resultTypeDom = $('#gold-price-converter select[name=result-type]');
    initCurrencyRate('USDCNY');
    function initCurrencyRate(exchange) {
        $.ajax({
            url: 'http://api.markets.wallstreetcn.com/v1/price.json?symbol=' + exchange,
            dataType: 'jsonp',
            success: function(response){
                if (response.results && response.results.length) {
                    //var rate = response.results[0]['price'];
                    console.log(response.results[0]['price']);
                    CER[exchange] = response.results[0]['price'];
                }
            },
            failure: function() {
                initCurrencyRate(exchange);
            }
        });
    }
    function convertPrice(price, type, convertType) {
        var ounce = 31.1034768;
        if (type === 'CNY' && convertType ==='USD') {
            return price * ounce / CER['USDCNY'] ;
        } else if (type === 'USD' && convertType === 'CNY') {
            return price *  CER['USDCNY'] / ounce;
        } else if (type === convertType) {
            return price;
        }
    }
    $input.on('focusin', function(e){
        $result.val('');
    });
    $inputTypeDom.on('change', function(e){
        var $this = $(this);
        var inputType = $this.val();
        var resultType = map[inputType];
        $resultTypeDom.val(resultType);
        var input = $input.val();
        if (input) {
            var result = convertPrice(parseFloat(input), inputType, resultType).toFixed(2);
            $result.val(result);
        }
    });
    $('#gold-price-converter .button.submit').on('click', function(){
        var input = parseFloat($input.val());
        var inputType = $inputTypeDom.val();
        var resultType = $resultTypeDom.val();
        var result = convertPrice(input, inputType, resultType).toFixed(2);
        $result.val(result);
    });
});

/**
 * gold-research
 */
$(function(){

    var $dom = $('#gold-research');
    if (! $dom.length) {
        return;
    }
    var data = [];
    //todo
    data.push({
        value: 100,
        label: '上涨',
        color: ''
    });
    data.push({
        value: 100,
        label: '下跌',
        color: ''
    });
    data.push({
        value: 100,
        label: '盘整',
        color: ''
    });
    var $chart = $('#gold-research-chart');
    var chart = Raphael("gold-research-chart", $chart.width(), $chart.height()).circleChart({
        cx: $chart.width() / 2,
        cy: 75,
        or: 72,
        ir: 42,
        title: '投票结果',
        data: data
    });
    $dom.on('click', '[data-action]', function(){
        var $this = $(this);
        var action = $this.attr('data-action');
        if (action === 'rise') {
            data[0].value ++;
        } else if (action === 'fall') {
            data[1].value ++;
        } else if (action === 'dull') {
            data[2].value ++;
        }
        chart.updateData(data);
        //console.log(chart);
        //raphael.clear();
        //chart.circleChart(75, 75, 72, 42, data, "#fff");
    });
});
/**
 * 定盘价
 */
(function(){

    var $target = $('[data-fix-price]');
    if (! $target.length) {
        return;
    }
    var option = tool.parseDomData($target.attr('data-fix-price-option'));
    var config = {};
    config.today = option.today;
    config.size  = option.size || 10;
    config.withSilver = option.withSilver === false ? false : true;
    var $dom = $target.find('[data-fix-price-dom]');
    var template = $target.find('[data-fix-price-template]').html();
    /**
     * 合并 goldData 和 【silverData】。
     * @param arg1
     * @param arg2
     * @returns {{}}
     */
    var mergeData = function() {
        if (arguments.length == 0) {
            return null;
        } else if (arguments.length == 1) {
            var arg1 = arguments[0];
            var arg2 = null;
        } else if (arguments.length == 2) {
            var arg1 = arguments[0];
            var arg2 = arguments[1];
        }
        var item = {};
        item.utm = arg1.start;
        item.date = moment.unix(arg1.start).format('YYYY-MM-DD');
        if (arg1.silver) {
            item.gold_am = null;
            item.gold_pm = null;
            item.silver = arg1.open.toFixed(2);
            if (arg2) {
                item.gold_am = arg2.open.toFixed(2);
                item.gold_pm = arg2.close.toFixed(2);
            }
        } else {
            item.gold_am = arg1.open.toFixed(2);
            item.gold_pm = arg1.close.toFixed(2);
            item.silver = null;
            if (arg2) {
                item.silver = arg2.open.toFixed(2);
            }
        }
        return item;
    }

    $.ajax({
        url: 'http://api.markets.wallstreetcn.com/v1/chart.json?symbol=GOLDFIXPRICE&interval=1d&rows=' + config.size,
        dataType: 'jsonp',
        success: function(response) {
            var goldData = response['results'];
            var data = goldData;
            if (config.withSilver) {
                $.ajax({
                    url: 'http://api.markets.wallstreetcn.com/v1/chart.json?symbol=SILVERFIXPRICE&interval=1d&rows=' + config.size,
                    dataType: 'jsonp',
                    success: function(response) {
                        var silverData = response['results'];
                        var i, l = silverData.length;
                        var results = [];
                        for (i = 0; i < l; i++) {
                            silverData[i].silver = true;
                        }
                        data = data.concat(silverData);
                        data.sort(function(first, second){
                            return second.start - first.start;
                        });
                        //
                        i = 0;
                        l = data.length - 1;
                        //
                        while(i < l) {
                            if (data[i].start == data[i + 1].start) {
                                var item = mergeData(data[i], data[i+1]);
                                results.push(item);
                                i += 2 ;
                            } else {
                                var item = mergeData(data[i]);
                                results.push(item);
                                i ++ ;
                                if (i == l) {
                                    var item = mergeData(data[i]);
                                    results.push(item);
                                }
                            }
                            console.log('fix_price: the item is : ' +  item);
                        }
                        //转化为html
                        var html = _.template(template, {
                            data : results
                        });
                        $dom.html(html);
                        if (config.today) {
                            if (results[0].date == moment().format('YYYY-MM-DD')) {
                                var today_am_gold = results[0].gold_am;
                                var today_pm_gold = results[0].gold_pm;
                                var today_silver  = results[0].silver;
                            } else {
                                var today_am_gold = '- -';
                                var today_pm_gold = '- -';
                                var today_silver  = '- -';
                            }
                            $target.find('[data-fix-price-value=today-am-gold]').text(today_am_gold);
                            $target.find('[data-fix-price-value=today-pm-gold]').text(today_pm_gold);
                            $target.find('[data-fix-price-value=today-silver]').text(today_silver);
                        }
                    } // silver success function
                }); // silver ajax
            } else {
                var results = [];
                var i , l = data.length;
                for (i = 0; i < l; i++) {
                    var item = {};
                    item.date = moment.unix(data[i].start).format('YYYY-MM-DD');
                    item.gold_am = data[i].open.toFixed(2);
                    item.gold_pm = data[i].close.toFixed(2);
                    results.push(item);
                }
                var html = _.template(template, {
                    data : results
                });
                $dom.html(html);
            }
        } //gold success function
    });//gold ajax

})();

window.jplayer = $('<div id="jplayer"></div>').appendTo('body');
jplayer.jPlayer({
    ready: function () {
        $(this).jPlayer("setMedia", {
            mp3 : "/js/vendor/notification.mp3"
        });
    },
    swfPath: "/js/vendor/Jplayer.swf",
    supplied: "mp3"
});

$(function(){

    //
    $('#main-livenews').lnl();
    //
    $('#livenews').lnl({
        url: 'http://api.wallstreetcn.com/apiv1/livenews-list-gold.jsonp',
        updateUrl: 'http://api.wallstreetcn.com/apiv1/livenews-gold.jsonp',
        countUrl: 'http://api.wallstreetcn.com/apiv1/livenews-count-gold.jsonp',
        pageSize: 80,
        menu: true,
        paging: true,
        clock: true
    });
    //

    //
    $('[data-action=livenews-alert]').on('click', function(e){
        var $this = $(this);
        var $target = $($this.attr('data-target'));
        if (this.checked) {
            $target.trigger('on_alert');
        } else {
            $target.trigger('off_alert');
        }
    });
    //
    $('[data-action=livenews-all-shrink]').on('click', function(e){
        var $this = $(this);
        var $target = $($this.attr('data-target'));
        if (this.checked) {
            $target.trigger('shrink_all');
        } else {
            $target.trigger('spread_all');
        }
    });

    //
    $('#side-fcl').fcl();
    //
    $('#finance-calendar').fcl({
        scrollable: false,
        datepicker: true,
        dateChangeEvent: true,
        currencyEvent: true,
        loadMoreEvent: false,
        sort: true
    });

    //
    //mam.init();
    $('[data-rtq]').rtq();
    //
    $('[data-etf]').etf();

    /**
     * chart 图表
     */
    $(document).on('click', '[data-efc-target]', function(e) {
        var $this = $(this);
        var $target = $(this.getAttribute('data-efc-target'));
        var frame = $target.find('iframe')[0];
        var symbol = this.getAttribute('data-efc-symbol');
        var interval = this.getAttribute('data-efc-interval');
        var type = this.getAttribute('data-efc-type');
        if (symbol) {
            //frame.src = frame.src.replace(/symbol=\w+(&)?/, 'symbol=' + symbol + '$1');
            frame.src = frame.src.replace(/symbol=\w+/, 'symbol=' + symbol);
            $this.parent().find('[data-efc-symbol].active').removeClass('active');
            $this.addClass('active');
        } else if (interval) {
            //frame.src = frame.src.replace(/interval=\w+(&)?/, 'interval=' + interval + '$1');
            var src = frame.src.replace(/interval=\w+/, 'interval=' + interval);
            src = src.replace(/&newsUrl=http%3a%2f%2fapi.goldtoutiao.com%2fv2%2fpost%3fcid%3d14/, '');
            if (! /4H|[DWN]/.test(interval)) {
                src += '&newsUrl=http%3a%2f%2fapi.goldtoutiao.com%2fv2%2fpost%3fcid%3d14';
            }
            frame.src = src;
            $this.parent().find('[data-efc-interval].active').removeClass('active');
            $this.addClass('active');
        } else if (type) {
            frame.src = frame.src.replace(/type=\w+/, 'type=' + type);
            $this.parent().find('[data-efc-type].active').removeClass('active');
            $this.addClass('active');
        }
        /*
        if ($this.is('[data-etf-target]')) {
            var etf_frame  = $($this.attr('data-etf-target'))[0];
            var etf_symbol = symbol + 'ETF';
            etf_frame.src = etf_frame.src.replace(/symbol=\w+(&)?/, 'symbol=' + etf_symbol + '$1');
        }
        if ($this.is('[data-cftc-target]')) {
            var cftc_frame  = $($this.attr('data-cftc-target'))[0];
            var cftc_symbol = symbol + 'CFTC';
            cftc_frame.src = cftc_frame.src.replace(/symbol=\w+(&)?/, 'symbol=' + cftc_symbol + '$1');
        }
        */
        e.preventDefault();
    });



    /*
    $(document).on('click', '[data-etf-target]', function(e) {
        var $this = $(this);
        var frame = $($this.attr('data-etf-target'))[0];
        var symbol = $this.attr('data-efc-symbol') + 'ETF';
        frame.src = frame.src.replace(/symbol=\w+(&)?/, 'symbol=' + symbol + '$1');
    });
    $(document).on('click', '[data-cftc-target]', function(e) {
        var $this = $(this);
        var frame = $($this.attr('data-cftc-target'))[0];
        var symbol = $this.attr('data-efc-symbol') + 'CFTC';
        frame.src = frame.src.replace(/symbol=\w+(&)?/, 'symbol=' + symbol + '$1');
    });
    */
    /*
    $(document).on('click.spread', '[data-toggle=shrink]', function(e){
        $(this).parent().toggleClass('shrink');
    });

    $(document).on('click', '.tabbar>.tab', function(){
        console.log('ha ha ha ha');
        var thisTab = $(this);
        var tabbar = thisTab.parent();
        var content = tabbar.next();
        var panels = content.children('.panel');
    });
    */
    var $modal = $('#modal');
    var $stare = $('#stare-modal');

    function hideModal() {
        //todo
        //mam.initData();
        $modal.children('.panel.active').removeClass('active');
        $modal.hide();
    }

    /**
     * 初始化定盘模式
     */
    function initStareModal() {
        $('#stare-livenews').lnl({
            heightChange: true
        });
        $('#stare-fcl').fcl({
            autoScroll: true,
            heightChange: true
        });
        document.getElementById('stare-iframe').src = $('#stare-iframe').attr('data-src');
        $stare.attr('data-init', 'true');
    }

    function showStareModal() {
        $stare.addClass('active');
        //$stare.css('transform', 'scale(0%, 0%)');
        //$stare.css('transform', 'scale(100%, 100%)');
        $({number: 0}).animate({number: 100}, {
            duration: 500,
            step: function(now) {
                // in the step-callback (that is fired each step of the animation),
                // you can use the `now` parameter which contains the current
                // animation-position (`0` up to `angle`)
                $stare.css({
                    transform: 'scale(' + now/100 + ',' + now/100 +')'
                });
            },
            done: function() {
                if ($stare.attr('data-init') != 'true') {
                    initStareModal();
                }
            }
        });
        //todo

    }

    $modal.on('click', function(e){
        if (e.target == this) {
            hideModal();
        }
    });
    $stare.on('click', '[data-toggle=hide][data-hide-target][data-expand-target]', function(e){
        var $this = $(this);
        var $hideTarget = $($this.attr('data-hide-target'));
        var hideTargetHeight = $hideTarget.outerHeight();
        var $expandTarget = $($this.attr('data-expand-target'));
        var expandTargetHeight = $expandTarget.height();
        if ($this.hasClass('active')) {
            var height = expandTargetHeight - hideTargetHeight;
            $hideTarget.slideDown(500);
            $this.removeClass('active');
        } else {
            var height = expandTargetHeight + hideTargetHeight;
            $hideTarget.slideUp(500);
            $this.addClass('active');
        }
        console.log('set the expand Target Height from [' + expandTargetHeight + '] to [' + height + ']');
        $expandTarget.trigger('height_change', height);
    });
    $('button[data-toggle=stare-modal]').click(function(){
        $modal.show(0, showStareModal());
    });
    $('#stare-modal>.close').click(function(){
        hideModal();
    });
});
