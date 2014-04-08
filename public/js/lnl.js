//live news
(function(){

    var PAGE_SIZE = 10;
    var UPDATE_INTERVAL = 10*1000;

    function Lnl(options) {

        this.$target = options.$target;
        this.pageSize = options.pageSize || PAGE_SIZE;
        this.autoRefresh = options.autoRefresh || true;
        this.updateInterval = null;
        this.init();

    };
    Lnl.prototype.init = function() {
        this.initData();
        this.initEvent();
    };
    Lnl.prototype.initData = function() {
        this.page = -1;
        var callback = _.bind(function(){
            this.$target.nanoScroller({
                preventPageScrolling: true
            });
            if (this.autoRefresh) {
                this.updateInterval = setInterval(_.bind(this.updateData, this), UPDATE_INTERVAL);
            }
        }, this);
        this.nextPage(callback);
    };
    Lnl.prototype.initEvent = function() {
        var root = this;
        var $target = this.$target;
        //
        $target.bind('scrollend', function(e) {
            console.log('scrollend~~');
            root.nextPage();
        });
        //
        $target.on('click', '[data-toggle=shrink]', function(){

            var $this = $(this);

            if ($this.hasClass('active')) {
                $this.parent().nextUntil('.date').removeClass('hidden');
                $this.removeClass('active');
            } else {
                $this.parent().nextUntil('.date').addClass('hidden');
                $this.addClass('active');
            }
            //
            $target.nanoScroller();
        });
    };

    Lnl.prototype.updateData = function() {

        console.log('to update the livenews');
        var root = this;
        var $target = root.$target;

        $.ajax({
            url: 'http://api.wallstreetcn.com/apiv1/livenews.jsonp',
            dataType: 'jsonp',
            success: function(response){

                if (response && response.length) {

                    var data = [];

                    var $first = $target.find('.item:first');
                    var $date  = $target.find('.date:first');
                    var day    = $date.attr('data-day');

                    for (var i=0; i<response.length; i++) {

                        var item = response[i];

                        if (item['node_created'] * 1000 > $first.attr('data-timestamp') * 1) {
                            var record = root.convertRecord(item);
                            data.push(record);
                        }
                    }
                    if (data.length) {

                        var template = $target.find('script[data-template]').html();

                        var beforeData = [];

                        var afterData = [];

                        for (var i=0; i<data.length; i++) {
                            if (data[i].day > day) {
                                beforeData.push(data[i]);
                            } else {
                                afterData.push(data[i]);
                            }
                        }
                        console.log('the before data is ' + beforeData);
                        console.log('the after data is ' + afterData);

                        if (beforeData.length) {
                            var beforeHtml = _.template(template, {
                                records: beforeData,
                                day: day
                            });
                            $date.before(beforeHtml);
                        }
                        if (afterData.length) {
                            var afterHtml = _.template(template, {
                                records: afterData,
                                day: day
                            });
                            $date.after(afterHtml);
                        }
                        //
                        $target.nanoScroller();
                        //
                        $target.nanoScroller({ scroll: 'top' });

                    }
                }
            }
        });
    };
    Lnl.prototype.stopUpdateData = function() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }
    };

    Lnl.prototype.nextPage = function(callback) {

        var root = this;
        var $target = root.$target;

        if (! $target.hasClass('loading')) {

            $target.addClass('loading');

            $.ajax({
                url: 'http://api.wallstreetcn.com/apiv1/livenews-list.jsonp',
                data: {
                    page: root.page + 1
                },
                dataType: 'jsonp',
                success: function(response){

                    var data = [];

                    if (response && response.length) {

                        for (var i=0; i<response.length; i++) {
                            var record = root.convertRecord(response[i]);
                            data.push(record);
                        }

                        var $last = $target.find('.item:last');
                        //var $date = $target.find('.date:last');
                        var day;
                        if ($last.length) {
                            day = $last.attr('data-day');
                        }
                        var $script = $target.find('script[data-template]');
                        var html = _.template($script.html(), {
                            records: data,
                            day: day
                        });
                        //1.
                        if ($last.length) {
                            $last.after(html);
                        } else {
                            $script.before(html);
                        }
                        //2.
                        $target.nanoScroller();
                    }
                    //3.
                    root.page ++;
                    console.log('load the page ' + root.page);
                    //4
                    $target.removeClass('loading');
                    //5.
                    if (callback) {
                        callback();
                    }
                },
                failure: function() {
                    this.nextPage(callback);
                }
            });
        }

    };
    Lnl.prototype.convertRecord = function(record) {

        var item = {}
        item.title = record['node_title'];
        item.nid = record['nid'];
        item.timestamp = record['node_created'] * 1000;
        item.day = moment(item.timestamp).format('DDD');
        item.time = moment.unix(record['node_created']).format('HH:mm');
        item.date = moment.unix(record['node_created']).format('YYYY年MM月DD日');
        item.icon = this.parse(record['新闻图标'], 'icon');
        item.format = this.parse(record['格式'], 'format');
        item.color = this.parse(record['颜色'], 'color');
        return item;
    };
    Lnl.prototype.parse = function(val, type) {

        if (type === 'icon') {
            if (val === '折线') {
                return 'chart-line';
            } else if (val === '柱状') {
                return 'chart-column';
            } else if (val === '提醒') {
                return 'alert';
            } else if (val === '警告') {
                return 'warning';
            } else if (val === '传言') {
                return 'rumor';
            } else {
                return 'common';
            }
        } else if (type === 'color') {
            if (val === '红色') {
                return 'red';
            } else if (val === '蓝色') {
                return 'blue';
            } else {
                return '';
            }
        } else if (type === 'format') {
            if (val === '加粗') {
                return 'bold';
            } else {
                return '';
            }
        }

    };


    $.fn.lnl = function(inputOptions) {
        var $this = this;
        if ($this.attr('data-init') === 'initialized') {
            return;
        }
        $this.attr('data-init', 'initialized');
        var options = {
            $target: $this
        };
        var domOptions = {};
        var str = $this.attr('data-lnl-options');
        if (str) {
            domOptions = tool.parseStringToObject(str);
        }
        $.extend(options, domOptions, inputOptions);
        new Lnl(options);
    }
})();

