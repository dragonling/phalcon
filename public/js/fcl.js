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
        this.$clock = this.$target.find('.clock');
        this.init(options);
    }
    Fcl.prototype.init = function(options) {
        this.$target.nanoScroller({
            preventPageScrolling: true
        });
        this.initData(options);
        this.initEvent(options);
    };
    Fcl.prototype.initData = function(options) {
        var start = moment().format('YYYY-MM-DD');
        var end = moment(start, 'YYYY-MM-DD').add('days', 1).format('YYYY-MM-DD');
        if (options.autoScroll) {
            this.getData(start, end, _.bind(this.autoScroll, this));
        } else {
            this.getData(start, end);
        }
    };
    Fcl.prototype.initEvent = function(options) {
        var $target = this.$target;
        $target.bind('scrolltop', _.bind(this.prevDay, this));
        $target.bind('scrollend', _.bind(this.nextDay, this));
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
                this.$target.nanoScroller({
                    scrollTo: $timerTarget
                });
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

                if (results && results.length) {
                    var i;
                    var l = results.length;
                    var result;
                    var record = {};
                    var data = [];
                    for (i=0; i<l; i++) {

                        result = results[i];
                        result.id   = root.id + '-item' + root.itemIndex ++;
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
                                result['stars'] = '<img class="icon star" src="resources/icons/star-active.png"/>' +
                                    '<img class="icon star" src="resources/icons/star.png"/>' +
                                    '<img class="icon star" src="resources/icons/star.png"/>'
                                break;
                            case 2:
                                result['stars'] = '<img class="icon star" src="resources/icons/star-active.png"/>' +
                                    '<img class="icon star" src="resources/icons/star-active.png"/>' +
                                    '<img class="icon star" src="resources/icons/star.png"/>'
                                break;
                            case 3:
                                result['stars'] = '<img class="icon star" src="resources/icons/star-active.png"/>' +
                                    '<img class="icon star" src="resources/icons/star-active.png"/>' +
                                    '<img class="icon star" src="resources/icons/star-active.png"/>'
                                break;
                        }
                        data.push(result);
                    }

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
                this.getData(start, end, arg);
            }
        });

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
    Fcl.prototype.unixTimeFrame = function(arg1, arg2) {
        console.log('fcl unixTimeFrame : ' + arg1 + ' > ' + arg2);
        var $target = this.$target;
        if (arg1) {
            $target.attr('data-start', arg1);
        }
        if (arg2) {
            $target.attr('data-end', arg2);
        }
        return {
            start: $target.attr('data-start'),
            end: $target.attr('data-end')
        };

    }

    $.fn.fcl = function(inputOptions) {
        var $this = $(this);
        if ($this.attr('data-init') === 'initialized') {
            return;
        }
        $this.attr('data-init', 'initialized');
        var options = {
            $target: $this
        };
        var domOptions = {};
        var str = $this.attr('data-fcl-options');
        if (str) {
            domOptions = tool.parseStringToObject(str);
        }
        $.extend(options, domOptions, inputOptions);
        new Fcl(options);
    }
})();
