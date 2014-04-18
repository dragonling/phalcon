//finance calender
/**
 * fcl -> finance calendar list
 * utm -> unix timestamp
 */
(function(){

    var apiUrl = 'http://api.markets.wallstreetcn.com/v1/calendar.json';
    var apiType = 'jsonp';

    var FCL_INDEX = 1;

    function Fcl(options) {

        this.$target = options.$target;
        this.id = this.$target.attr('id') || 'fcl' + FCL_INDEX ++;
        this.itemIndex = 1;
        this.scrollable = options.scrollable === false ? false : true;
        this.$datepicker = options.$datepicker;
        this.dateChangeEvent = options.dateChangeEvent;
        this.countryChangeEvent = options.countryChangeEvent;
        this.sort = options.sort;
        this.$clock = this.$target.find('.clock');
        this.init(options);
    }
    Fcl.prototype.init = function(options) {
        if (this.scrollable) {
            this.$target.nanoScroller({
                preventPageScrolling: true
            });
        }
        if (this.$datepicker) {
            this.$dateLabel = this.$target.find('[data-calendar-label]');
            var texts = moment().format('DD;YYYY年M月D日;dddd').split(';');
            this.$dateLabel.html('<span class="text big">' + texts[0] +
                '</span><span class="text">' + texts[1] +
                '</span><span class="text">' + texts[2] + '</span>');
            this.$dateText  = this.$target.find('[data-calendar-text]');
            this.$calendar = this.$target.find('.calendar');
            this.initDatePicker();
        }
        this.initData(options);
        this.initEvent(options);
    };
    Fcl.prototype.initData = function(options) {
        var root = this;
        var start = moment().format('YYYY-MM-DD');
        var end   = moment().add('days', 1).format('YYYY-MM-DD');
        if (options.autoScroll) {
            this.getData(start, end, _.bind(this.autoScroll, this));
        } else {
            this.getData(start, end);
        }
    };
    Fcl.prototype.initEvent = function(options) {
        var $target = this.$target;
        var root = this;
        if (this.scrollable) {
            $target.bind('scrolltop', _.bind(this.prevDay, this));
            $target.bind('scrollend', _.bind(this.nextDay, this));
        }
        if (options['heightChange']) {
            $target.bind('height_change', function(e, height){
                var $this = $(this);
                $this.height(height);
                if ($target.hasClass('nano')) {
                    $this.nanoScroller();
                }
            });
        }
        if (this.dateChangeEvent) {
            /*this.$target.on('click', '.head>.item[data-action=change-date]', this.$target, function(e){
                var $this = $(this);
                if ($this.hasClass('active') || e.data.hasClass('loading')) {
                    return;
                }
                var $active = $this.parent().children('.active');
                $active.removeClass('active');
                $this.addClass('active');
                var date = $this.attr('data-date');
                root.changeDate(date);
            });*/
            this.$target.on('click', '[data-action=change-date]', this.$target, function(e) {
                var $this = $(this);
                if ($this.hasClass('active') || e.data.hasClass('loading')) {
                    return;
                }
                if ($this.is('.item')) {
                    var $item = $this;
                } else {
                    var $item = $this.parent();
                }
                var $active = $item.siblings('.active');
                if ($active.length) {
                    $active.removeClass('active');
                }
                $item.addClass('active');
                var date = $this.attr('data-date');
                root.changeDate(date);
            });
        }
        if (this.countryChangeEvent) {
            this.$target.on('click', '[data-toggle=currency]', function(e){
                var $content = root.$target.children('.content');
                var checkedItems = root.$target.find('[data-toggle=currency]:checked');
                if (checkedItems.length == 0) {
                    $content.find('.item').show();
                } else if (checkedItems.length == 1) {
                    var $this = $(this);
                    if ($this.attr('data-currency') === checkedItems.attr('data-currency')) {
                        $content.find('.item').hide();
                        $content.find('.item[data-currency=' + $this.attr('data-currency') + ']').show();
                    } else {
                        $content.find('.item[data-currency=' + $this.attr('data-currency') + ']').hide();
                    }
                } else {
                    var $this = $(this);
                    if ($this.is(':checked')) {
                        $content.find('.item[data-currency=' + $this.attr('data-currency') + ']').show();
                    } else {
                        $content.find('.item[data-currency=' + $this.attr('data-currency') + ']').hide();
                    }
                }
            });
        }
    };
    Fcl.prototype.initDatePicker = function() {
        var today = new Date();
        var root = this;
        this.$datepicker.DatePicker({
            flat : true,
            date : today,
            locale: {
                days: ["星期天", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期天"],
                daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
                daysMin: ["日", "一", "二", "三", "四", "五", "六", "日"],
                months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一", "十二"],
                weekMin: '周'
            },
            onChange: _.bind(root.changeDate, root)
        });
        this.$target.find('[data-calendar-text],[data-toggle=calendar]').on('click', function(){
            var $this = $(this);
            var $input = $this.parent();
            var $active = $input.siblings('.active');
            if ($active.length) {
                $active.removeClass('active');
            }
            $input.addClass('active');
            root.$calendar.slideToggle();
        });
    };
    Fcl.prototype.timer = function() {
        if (this.timerTargetId) {
            var $clock = this.$clock;
            var now = new Date().getTime();
            var fromNow = (this.timerTargetUtm * 1000 - now);
            if (fromNow > 0) {
                var seconds = Math.floor(fromNow / 1000);
                var ss = seconds % 60;
                ss = ss > 0 ? ss + '秒' : '';
                var minutes = Math.floor(seconds / 60);
                var mm = minutes % 60;
                mm = mm > 0 ? mm + '分钟' : '';
                var hh = Math.floor(minutes / 60);
                hh = hh > 0 ? hh + '小时' : '';
                $clock.text(hh + mm + ss);
            } else {
                var $timerTarget = $('#' + this.timerTargetId);
                if (this.scrollable) {
                    this.$target.nanoScroller({
                        scrollTo: $timerTarget
                    });
                }
                var next = $timerTarget.next('.item');
                if (next && next.length) {
                    this.timerTargetId  = next.attr('id');
                    this.timerTargetUtm = next.attr('data-utm');
                }
            }
        }
    };
    Fcl.prototype.nextTimerTarget = function($current) {
        var $next = $current.next();
        if ($next.length) {
            if ($next.hasClass('item')) {
                if ($next.attr('data-utm') == $current.attr('data-utm')) {
                    this.nextTimerTarget($next);
                } else {
                    return $next;
                }
            } else {
                this.nextTimerTarget($next);
            }
        } else if (! this.$target.hasClass('loading')) {
            this.nextDay();
        }
    };
    Fcl.prototype.autoScroll = function(arg) {

        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
        if (arg === false) {
            return;
        }
        var root = this;
        var $target = root.$target;

        if (typeof arg === 'string') {
            var $timerTarget = $(arg);
            root.timerTargetId  = $timerTarget.attr('id');
            root.timerTargetUtm = $timerTarget.attr('data-utm');
        } else {
            var items = $target.find('.item');
            if (items && items.length) {
                var i;
                var l = items.length;
                var item;
                for (i=0; i<l; i++) {
                    item = items[i];
                    if (item.getAttribute('data-utm') * 1000 > new Date().getTime()) {
                        //todo
                        root.timerTargetId = item.id;
                        root.timerTargetUtm = item.getAttribute('data-utm');
                        console.log('the timer target id is : ' + root.timerTargetId);
                        console.log('the timer target utm is : ' + root.timerTargetUtm);
                        break;
                    }
                }
            }
        }
        root.timerInterval = setInterval(_.bind(root.timer, root), 1000);

    };
    Fcl.prototype.changeDate = function(date) {
        var start,end,days;
        switch (date) {
            case 'yesterday' :
                start = moment().subtract('days', 1).format('YYYY-MM-DD');
                end   = moment().format('YYYY-MM-DD');
                break;
            case 'today' :
                start = moment().format('YYYY-MM-DD');
                end   = moment().add('days', 1).format('YYYY-MM-DD');
                break;
            case 'tomorrow' :
                start = moment().add('days', 1).format('YYYY-MM-DD');
                end   = moment().add('days', 2).format('YYYY-MM-DD');
                break;
            case 'week' :
                start = moment().day(0).format('YYYY-MM-DD');
                end   = moment().day(6).format('YYYY-MM-DD');
                break;
            case 'prev-date' :
                start = moment(this.unixTimeFrame().start, 'YYYY-MM-DD');
                end   = moment(this.unixTimeFrame().end, 'YYYY-MM-DD');
                days  = parseInt((end - start) / (1000*60*60*24));
                end   = this.unixTimeFrame().start;
                start = start.subtract('days', days).format('YYYY-MM-DD');
                break;
            case 'next-date' :
                start = moment(this.unixTimeFrame().start, 'YYYY-MM-DD');
                end   = moment(this.unixTimeFrame().end, 'YYYY-MM-DD');
                days  = parseInt((end - start) / (1000*60*60*24));
                start = this.unixTimeFrame().end;
                end   = end.add('days', days).format('YYYY-MM-DD');
                break;
            default :
                //todo
                start = this.$datepicker.DatePickerGetDate(true);
                end   = moment(start, 'YYYY-MM-DD').add('days', 1).format('YYYY-MM-DD');
                this.$calendar.slideUp();
        }
        this.getData(start, end);
    };
    Fcl.prototype.getData = function(start, end, arg) {

        var root = this;
        var $target = root.$target;

        if ($target.hasClass('loading')) {
            return;
        }
        $target.addClass('loading');
        $.ajax({
            url: apiUrl,
            dataType: apiType,
            data: {
                start: start,
                end: end
            },
            success: function(response){

                var results = response['results'];
                var $content = $target.children('.content');
                var template = $target.find('script[data-template]').html();
                var data = root.parseData(results);
                var html = _.template(template, {
                    records : data
                });
                if (arg === 'prev') {
                    $content.prepend(html);
                } else if (arg === 'next') {
                    $content.append(html);
                } else {
                    $content.html(html);
                }
                //4
                if (root.scrollable) {
                    $target.nanoScroller();
                }
                //5
                if (arg === 'prev') {
                    root.unixTimeFrame(start, null);
                } else if (arg === 'next') {
                    root.unixTimeFrame(null, end);
                } else {
                    root.unixTimeFrame(start, end);
                    if (_.isFunction(arg)) {
                        arg();
                    }
                }
                //6
                $target.removeClass('loading');
            },
            failure: function() {
                console.log('do it again!')
                root.getData(start, end, arg);
            }
        });
    };
    Fcl.prototype.parseData = function(results) {
        var data = [];
        if (this.sort) {
            data[0] = []; //fd 财经日历
            data[1] = []; //fe 财经大事
            data[2] = []; //sr 股票财报
            data[3] = []; //bi 国债发行
            data[4] = [];  //vn 假期预告
        }
        if (! results || ! results.length) {
            return data;
        }
        var i;
        var l = results.length;
        var result;
        //var record = {};
        for (i=0; i<l; i++) {
            result = results[i];
            result.id   = this.id + '-item' + this.itemIndex ++;
            result.time = result['localDateTime'].substring(11, 16);
            var mt = moment(result['localDateTime'], 'YYYY-MM-DD hh:mm:ss');
            result.date = mt.format('YYYY年MM月DD日 dddd');
            result.utm  = mt.unix();
            result.trend = '';
            if (result['actual'] && result['forecast']) {
                if (parseFloat(result['actual']) > parseFloat(result['forecast'])) {
                    result.trend = 'up';
                } else if (parseFloat(result['actual']) < parseFloat(result['forecast'])) {
                    result.trend = 'down';
                }
            }
            //
            if (!result['forecast']) {
                result['forecast'] = '- -';
            }
            //importance
            //.icon.star
            switch (parseInt(result['importance'])) {
                case 1:
                    result['stars'] = '<img class="icon star" src="/img/icon/star_blue.png"/>' +
                        '<img class="icon star" src="/img/icon/star_grey.png"/>' +
                        '<img class="icon star" src="/img/icon/star_grey.png"/>'
                    break;
                case 2:
                    result['stars'] = '<img class="icon star" src="/img/icon/star_blue.png"/>' +
                        '<img class="icon star" src="/img/icon/star_blue.png"/>' +
                        '<img class="icon star" src="/img/icon/star_grey.png"/>'
                    break;
                case 3:
                    result['stars'] = '<img class="icon star" src="/img/icon/star_blue.png"/>' +
                        '<img class="icon star" src="/img/icon/star_blue.png"/>' +
                        '<img class="icon star" src="/img/icon/star_blue.png"/>'
                    break;
            }
            //
            if (this.sort) {
                switch (result['calendarType']) {
                    case 'FD':
                        data[0].push(result);
                        break;
                    case 'FE':
                        data[1].push(result);
                        break;
                    case 'SR':
                        data[2].push(result);
                        break;
                    case 'BI':
                        data[3].push(result);
                        break;
                    case 'VN':
                        data[4].push(result);
                        break;
                }
            } else {
                data.push(result);
            }
        }
        return data;
    };
    Fcl.prototype.prevDay = function() {
        console.log('fcl prev day');
        var end = this.unixTimeFrame().start;
        var start = moment(end, 'YYYY-MM-DD').subtract('days', 1).format('YYYY-MM-DD');
        this.getData(start, end, 'prev');
    };
    Fcl.prototype.nextDay = function() {
        console.log('fcl next day');
        var start = this.unixTimeFrame().end;
        var end = moment(start, 'YYYY-MM-DD').add('days', 1).format('YYYY-MM-DD');
        this.getData(start, end, 'next');
    };
    Fcl.prototype.prevDate = function() {
        console.log('fcl prev date');
        var start = moment(this.unixTimeFrame().start, 'YYYY-MM-DD');
        var end   = moment(this.unixTimeFrame().end, 'YYYY-MM-DD');
        var days  = parseInt((end - start) / (1000*60*60*24));
        end = this.unixTimeFrame().start;
        start = start.subtract('days', days).format('YYYY-MM-DD');
        this.getData(start, end);
    };
    Fcl.prototype.nextDate = function() {
        console.log('fcl next date');
        var start = moment(this.unixTimeFrame().start, 'YYYY-MM-DD');
        var end   = moment(this.unixTimeFrame().end, 'YYYY-MM-DD');
        var days  = parseInt((end - start) / (1000*60*60*24));
        start = this.unixTimeFrame().end;
        end = end.add('days', days).format('YYYY-MM-DD');
        this.getData(start, end);
    };
    Fcl.prototype.unixTimeFrame = function(arg1, arg2) {
        console.log('fcl unixTimeFrame : ' + arg1 + ' > ' + arg2);
        var $target = this.$target;
        if (arg1) {
            $target.attr('data-start', arg1);
        }
        if (arg2) {
            $target.attr('data-end', arg2);
        }
        if (this.$dateText && arg1 && arg2) {
            var moment1 = moment(arg1, 'YYYY-MM-DD');
            var moment2 = moment(arg2, 'YYYY-MM-DD').subtract('days', 1);
            if (moment2 - moment1) {
                var html = moment1.format('YYYY年MM月DD日') + ' - ' + moment2.format('YYYY年MM月DD日');
            } else {
                var html = moment1.format('YYYY年MM月DD日');
            }
            this.$dateText.html(html);
        }
        return {
            start: $target.attr('data-start'),
            end: $target.attr('data-end')
        };

    };

    $.fn.fcl = function(inputOptions) {
        if (! this.length) {
            return;
        }
        if (this.attr('data-fcl-init') === 'initialized') {
            return;
        }
        var $this = this;
        $this.attr('data-fcl-init', 'initialized');
        var options = {
            $target: $this
        };
        var domOptions = {};
        var str = $this.attr('data-fcl-option');
        if (str) {
            domOptions = tool.parseStringToObject(str);
        }
        $.extend(options, domOptions, inputOptions);
        new Fcl(options);
    };
})();
