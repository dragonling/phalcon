



/**
 * 汇率计算器
 */
(function(){

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
    /*
    function getExchangeRate(baseCurrency, exchangeCurrency) {

        var ounce = 28.3495231;

        if (baseCurrency === 'CNY' && exchangeCurrency ==='USD') {
            return ounce / getMarketPrice('USDCNY') ;
        } else if (baseCurrency === 'USD' && exchangeCurrency === 'CNY') {
            return getMarketPrice('USDCNY') / ounce;
        }
    }
    */

})();




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
    mam.init();
    /**
     * chart 图表
     */
    $(document).on('click', '[data-efc-target]', function(e) {

        var $target = $(this.getAttribute('data-efc-target'));
        var frame = $target.find('iframe')[0];
        var symbol = this.getAttribute('data-efc-symbol');
        var interval = this.getAttribute('data-efc-interval');
        var type = this.getAttribute('data-efc-type');

        if (symbol) {
            frame.src = frame.src.replace(/symbol=\w+(&)?/, 'symbol=' + symbol + '$1');
            $target.find('[data-efc-symbol].active').removeClass('active');
            $(this).addClass('active');
        } else if (interval) {
            frame.src = frame.src.replace(/interval=\w+(&)?/, 'interval=' + interval + '$1');
            $target.find('[data-efc-interval].active').removeClass('active');
            $(this).addClass('active');
        } else if (type) {

        }

        return false;

    });

    /**
     *
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
     *
     */
    $(document).on('click.menu', '[data-toggle=menu]', function(e){
        var $this = $(this);
        var $parent = $this.parent();
        if ($parent.hasClass('active')) {
            $parent.removeClass('active');
            return false;
        }
        var $parents = $this.parents('.item.active');
        var $menu = $this.parents('.menu');
        var $active = $menu.find('.item.active');
        if ($active.length) {
            if ($parents.length) {
                var array = $.grep($active, function(dom, i){
                    var l = $parents.length - 1;
                    for (l; l>-1; l--) {
                        if (dom.innerHTML == $parents[l].innerHTML) {
                            return false;
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
        }
        $parent.addClass('active');
        return false;
    });

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
        mam.initData();
        $modal.children('.panel.active').removeClass('active');
        $modal.hide();
    }

    function showStareModal() {
        //todo
        mam.initData(true);
        $('#stare-livenews').lnl({
            heightChange: true
        });
        $('#stare-fcl').fcl({
            autoScroll: true,
            heightChange: true
        });
        $stare.addClass('active');
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
            $hideTarget.show();
            $this.removeClass('active');
        } else {
            var height = expandTargetHeight + hideTargetHeight;
            $hideTarget.hide();
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
