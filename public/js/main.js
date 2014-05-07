$(function(){
    /**
     * 导航栏高亮
     */
    var fullPathUrl = window.location.pathname + window.location.search;
    $('#header .navbar .link').each(function(){
        var item = $(this);
        var pattern = item.attr("data-active-url");
        if (pattern) {
            var reg = new RegExp(pattern);
            if(reg.test(fullPathUrl)) {
                item.addClass("active");
            }
        }
    });
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
        e.preventDefault();

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
    /**
     * 汇率计算器
     */
    var CER = {};
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
        var ounce = 28.3495231;
        if (type === 'CNY' && convertType ==='USD') {
            return price * ounce / CER['USDCNY'] ;
        } else if (type === 'USD' && convertType === 'CNY') {
            return price *  CER['USDCNY'] / ounce;
        } else if (type === convertType) {
            return price;
        }
    }
    $('#gold-price-converter .button.submit').on('click', function(){
        var input = parseFloat($('#gold-price-converter input[name=input]').val());
        var inputType = $('#gold-price-converter select[name=input-type]').val().toUpperCase();
        var resultType = $('#gold-price-converter select[name=result-type]').val().toUpperCase();
        var result = convertPrice(input, inputType, resultType).toFixed(2);
        $('#gold-price-converter input[name=result]').val(result);
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
    $('#side-fcl').fcl();
    //
    $('#finance-calendar').fcl({
        scrollable: false,
        datepicker: true,
        dateChangeEvent: true,
        countryChangeEvent: true,
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
            frame.src = frame.src.replace(/symbol=\w+(&)?/, 'symbol=' + symbol + '$1');
            $this.parent().find('[data-efc-symbol].active').removeClass('active');
            $this.addClass('active');
        } else if (interval) {
            frame.src = frame.src.replace(/interval=\w+(&)?/, 'interval=' + interval + '$1');
            $this.parent().find('[data-efc-interval].active').removeClass('active');
            $this.addClass('active');
        } else if (type) {
            //todo
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
            }
        });
        //todo
        //mam.initData(true);
        $('#stare-livenews').lnl({
            heightChange: true
        });
        $('#stare-fcl').fcl({
            autoScroll: true,
            heightChange: true
        });
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
