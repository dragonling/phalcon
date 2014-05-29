//live news
(function(){

    //var PAGE_SIZE = 10;
    var UPDATE_INTERVAL = 10*1000;

    function Lnl(options) {
        //todo 添加默认config 到 原型中，所有的配置放在config中
        this.config = {};
        this.$target = options.$target;
        this.$content = this.$target.children('.content');
        this.$script = this.$target.find('script[data-template]');
        this.config.alert = options.alert;
        //todo
        this.autoRefresh = options.autoRefresh === false ? false : true;
        this.scrollable = options.scrollable === false ? false : true;
        this.updateUrl = options.updateUrl || 'http://api.wallstreetcn.com/apiv1/livenews.jsonp';
        this.countUrl = options.countUrl;
        this.url = options.url || 'http://api.wallstreetcn.com/apiv1/livenews-list-v2.jsonp';
        this.baseUpdateUrl = this.updateUrl;
        this.baseCountUrl = this.countUrl;
        this.baseUrl = this.url;
        this.pageSize = options.pageSize;
        this.dateFormat = options.dateFormat || 'YYYY年MM月DD日';
        this.timeFormat = options.timeFormat || 'HH:mm';
        this.clockFormat = options.clockFormat || 'YYYY年M月D日 HH:mm:ss';
        this.updateInterval = null;
        this.init(options);

    };

    Lnl.prototype.init = function(options) {
        this.initData();
        this.initEvent();
        if (options['clock']) {
            this.$clock = this.$target.find('.clock');
            setInterval(_.bind(function($clock, clockFormat){
                var text = moment().format(clockFormat);
                $clock.text(text);
            }, null, this.$clock, this.clockFormat), 1000);
        }
        if (options.paging) {
            this.lnlType = 'paging';
            this.initPaging(options);
        }
        if (options.menu) {
            this.initMenu();
        }
        if (options['heightChange']) {
            this.$target.bind('height_change', function(e, height){
                var $this = $(this);
                $this.animate(
                    {
                        height: height
                    },
                    500,
                    'linear',
                    function() {
                        if ($this.hasClass('nano')) {
                            $this.nanoScroller();
                        }
                    }
                );
            });
        }
    };
    Lnl.prototype.initData = function() {
        this.page = 1;
        var callback = _.bind(function(){
            if (this.scrollable) {
                this.$target.nanoScroller({
                    disableResize: true,
                    alwaysVisible: true,
                    preventPageScrolling: true
                });
            }
            if (this.autoRefresh) {
                this.updateInterval = setInterval(_.bind(this.updateData, this), UPDATE_INTERVAL);
            }
        }, this);
        this.loadPage(this.page, callback);
    };
    Lnl.prototype.initEvent = function() {
        var root = this;
        var $target = this.$target;
        //
        $target.bind('scrollend', function(e) {
            console.log('scrollend~~');
            root.nextPage();
        });
        //绑定 全部收起事件
        $target.bind('shrink_all', function(e){
            console.log('------------------------------------');
            console.log('livenews list shrink_all !!!!!!');
            $target.find('.item').addClass('hidden');
            $target.find('[data-toggle=shrink]').addClass('active');
        });
        //绑定 全部展开事件
        $target.bind('spread_all', function(e){
            console.log('------------------------------------');
            console.log('livenews list spread_all !!!!!!');
            $target.find('.item').removeClass('hidden');
            $target.find('[data-toggle=shrink]').removeClass('active');
        });
        //绑定 声音提醒事件
        $target.bind('on_alert', function(e){
            console.log('------------------------------------');
            console.log('livenews list on alert !!!!!!');
            root.config.alert = true;
        });
        //绑定 关闭声音提醒事件
        $target.bind('off_alert', function(e){
            console.log('------------------------------------');
            console.log('livenews list off alert !!!!!!');
            root.config.alert = false;
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
            if (root.scrollable) {
                $target.nanoScroller();
            }
        });
        $target.on('click', '.link', function(e){
            e.preventDefault();
            var $dom = $(this);
            var nid = $dom.attr('data-nid');
            root.detail($dom, nid);
        });
    };
    Lnl.prototype.updateData = function() {
        var root = this;
        var $target = root.$target;
        if (root.lnlType === 'paging' && ($target.hasClass('loading') || root.page != 1)) {
            return;
        }
        console.log('to update the livenews');
        $.ajax({
            url: root.updateUrl,
            dataType: 'jsonp',
            success: function(response){
                if (response && response.length) {
                    var data = [];
                    var $first = $target.find('.item:first');
                    var $date  = $target.find('.date:first');
                    //var day    = $first.attr('data-day');
                    for (var i=0; i<response.length; i++) {
                        var item = response[i];
                        if (item['node_created'] * 1000 > $first.attr('data-timestamp') * 1) {
                            var record = root.convertRecord(item);
                            data.push(record);
                        }
                    }
                    if (data.length) {
                        var template = $target.find('script[data-template]').html();
                        /*var beforeData = [];
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
                        }*/
                        if (root.lnlType === 'paging') {
                            var html = _.template(template, {
                                records: data,
                                day: moment().format('DDD')
                            });
                            //todo
                            /*if (data[data.length-1].day != $first.attr('data-day')) {
                                var record = {
                                    date: $first.attr('data-date'),
                                    day : $first.attr('data-day')
                                };
                                html += _.template($target.find('[data-date-template]').html(), {record: record});
                            }*/
                            $first.before(html);
                        } else {
                            var html = _.template(template, {
                                records: data,
                                day: null
                            });
                            $date.replaceWith(html);
                        }
                        if (root.config.alert) {
                            jplayer.jPlayer('play');
                        }
                        if (root.scrollable) {
                            //
                            $target.nanoScroller();
                            //
                            $target.nanoScroller({ scroll: 'top' });
                        }
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
    Lnl.prototype.loadPage = function(page, callback) {
        var root = this;
        var $target = root.$target;
        var $content = root.$content;
        var $script = root.$script;
        if ($target.hasClass('loading')) {
            return;
        }
        $target.addClass('loading');

        if (root.lnlType === 'paging') {
            $content.html('');
        }
        $.ajax({
            url: root.url,
            data: {
                page: page - 1
            },
            dataType: 'jsonp',
            success: function(response){
                var data = [];
                if (response && response.length) {
                    for (var i=0; i<response.length; i++) {
                        var record = root.convertRecord(response[i]);
                        data.push(record);
                    }
                    var day;
                    if (root.lnlType === 'paging') {
                        day = moment().format('DDD');
                    } else {
                        var $date = $content.find('.date:last');
                        if ($date.length) {
                            day = $date.attr('data-day');
                        }
                    }
                    var html = _.template($script.html(), {
                        records: data,
                        day: day
                    });
                    //1.
                    $content.append(html);
                    //2.
                    if (root.scrollable) {
                        $target.nanoScroller();
                    }
                }
                //3.
                root.page = page;
                console.log('load the page ' + root.page);
                //4
                $target.removeClass('loading');
                //5.
                if (callback) {
                    callback();
                }
            },
            error: function() {
                console.log('load the livenews list again!!!!!');
                root.loadPage(page, callback);
            }
        });
    };
    Lnl.prototype.nextPage = function() {
        this.loadPage(this.page + 1);
    };
    Lnl.prototype.count = function(callback) {
        var root = this;
        $.ajax({
            url : root.countUrl,
            dataType: 'jsonp',
            success: function(response) {
                if (response.length) {
                    root.total = response[0].count;
                }
                if (typeof callback == 'function') {
                    callback();
                }
            },
            error: function() {
                root.count(callback);
            }
        });
    };
    Lnl.prototype.changeType = function(type) {
        switch (type) {
            case 'all' :
                this.url = this.baseUrl;
                this.countUrl = this.baseCountUrl;
                this.updateUrl = this.baseUpdateUrl;
                break;
            case 'chart' :
            case 'chart_pie' :
            case 'alert' :
            case 'warning' :
            case 'rumor' :
                this.url = this.baseUrl + '?field_icon=' + type;
                this.countUrl = this.baseCountUrl + '?field_icon=' + type;
                this.updateUrl = this.baseUpdateUrl + '?field_icon=' + type;
                break;
        }
        this.loadPage(1);
        this.paging();
        //this.count(_.bind(this.overwritePaging(), this));
    };
    Lnl.prototype.initMenu = function() {
        var selector = '[data-lnl-type][data-lnl-target=#' + this.$target.attr('id') + ']'
        var root = this;
        $(document).on('click.menu', selector, function(e){
            var $this = $(this);
            if ($this.hasClass('active')) {
                return;
            }
            root.changeType($this.attr('data-lnl-type'));
            var $active = $this.parent().children('.active');
            if ($active.length) {
                $active.removeClass('active');
            }
            $this.addClass('active');
        });
    };
    Lnl.prototype.initPaging = function() {
        this.paging();
        var root = this;
        this.$target.bind('paging', function(e, page){
            root.loadPage(page);
        });
    };
    Lnl.prototype.paging = function() {
        var root = this;
        $.ajax({
            url : root.countUrl,
            dataType: 'jsonp',
            success: function(response) {
                if (response.length) {
                    var count = response[0].count;
                }
                var maxPage = parseInt(count/root.pageSize);
                root.$target.paging({
                    maxPage: maxPage,
                    defaultPage: 1
                });
            },
            error: function() {
                root.paging();
            }
        });
    };
    Lnl.prototype.detail = function($dom, nid) {
        if ($dom.hasClass('active')) {
            return;
        }
        var root = this;
        $dom.addClass('loading');
        $.ajax({
            url : 'http://api.wallstreetcn.com/apiv1/node/' + nid + '.jsonp',
            dataType: 'jsonp',
            success: function(result) {
                var detail = '';
                if (result.body && result.body['und'] && result.body['und'].length) {
                    detail = result.body['und'][0]['safe_value'];
                    //过滤样式表
                    detail = detail.replace(/style="[^"]*"/g, '');
                }
                if (result['upload'] && result['upload']['und'] && result['upload']['und'].length) {
                    var images = result['upload']['und'];
                    for (var j=0; j<images.length; j++) {
                        // 截取 public://
                        var imgRui = 'http://img.wallstreetcn.com/sites/default/files/' + images[j]['uri'].substring(9);
                        detail += '<img src="' + imgRui + '" />';
                    }
                }
                $dom.removeClass('loading');
                $dom.addClass('active');
                $dom.html(detail);
                if (root.scrollable) {
                    root.$target.nanoScroller();
                }
            },
            error: function() {

            }
        });
    }
    Lnl.prototype.convertRecord = function(record) {

        var item = {}
        item.title = record['node_title'];
        item.nid = record['nid'];
        item.timestamp = record['node_created'] * 1000;
        item.day = moment(item.timestamp).format('DDD');
        item.time = moment.unix(record['node_created']).format(this.timeFormat);
        item.date = moment.unix(record['node_created']).format(this.dateFormat);
        item.icon = this.parse(record['node_icon'], 'icon');
        item.format = this.parse(record['node_format'], 'format');
        item.color = this.parse(record['node_color'], 'color');
        return item;
    };
    Lnl.prototype.parse = function(val, type) {

        if (type === 'icon') {
            if (val === '折线') {
                return 'chart';
            } else if (val === '柱状') {
                return 'chart_pie';
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
        if (! this.length) {
            return;
        }
        for (var l = this.length - 1; l > -1; l --) {
            var $this = $(this[l]);
            if ($this.attr('data-lnl') === 'initialized') {
                return;
            }
            $this.attr('data-lnl', 'initialized');
            var options = {
                $target: $this
            };
            var domOptions = {};
            var str = $this.attr('data-lnl-option');
            if (str) {
                domOptions = tool.parseDomData(str);
            }
            $.extend(options, domOptions, inputOptions);
            new Lnl(options);
        }
    }
})();

