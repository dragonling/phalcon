/**
 * Created by tl on 14-4-14.
 */
(function(){
    function Paging(options) {
        this.id = 'ss-paging-' + Paging.prototype.index ++;
        this.$target = options.$target;
        this.$bar = this.$target.find('.toolbar.paging');
        this.maxPage = options.maxPage;
        this.defaultPage = options.defaultPage || 1;
        this.buttonAmount = options.buttonAmount || 10;

        this.initDom();
        this.initEvent();
    };
    Paging.prototype.index = 0;
    Paging.prototype.initDom = function() {
        this.refresh(this.defaultPage);
    };
    Paging.prototype.initEvent = function() {
        var $bar = this.$bar;
        var max  = this.maxPage;
        var root = this;
        $bar.on('click.paging', '.button', function(e){
            var $this = $(this);
            if ($this.hasClass('active')) {
                return;
            }
            if ($this.is('[data-action=prev-page]')) {
                var value = parseInt($bar.children('.active').text()) - 1;
            } else if ($this.is('[data-action=next-page]')) {
                var value = parseInt($bar.children('.active').text()) + 1;
            } else {
                var value = parseInt($this.text());
            }
            if (value > 0 || value < max + 1) {
                root.refresh(value);
                //todo
                root.$target.trigger('paging', value);
            }
        });
    };
    Paging.prototype.refresh = function(num) {
        var $bar = this.$bar;
        var max = this.maxPage;
        var value = num - Math.round(this.buttonAmount/2);
        var html = '';
        var top;
        var maxFirst = max - this.buttonAmount + 1;
        if (value > maxFirst) {
            value = maxFirst;
        }
        if (value < 1) {
            value = 1;
        }
        if (max < this.buttonAmount) {
            top = max + 1;
        } else {
            top = value + this.buttonAmount;
        }
        if (num > 1) {
            html += '<div class="button" data-action="prev-page"><i class="icon-caret-left"></i> 上一页</div>';
        }
        while (value < top) {
            if (value === num) {
                html += '<div class="button active">' + value + '</div>'
            } else {
                html += '<div class="button">' + value + '</div>'
            }
            value ++ ;
        }
        if (num < max) {
            html += '<div class="button" data-action="next-page">下一页 <i class="icon-caret-right"></i></div>';
        }
        //console.log(html);
        $bar.html(html);
    };
    Paging.prototype.overwrite = function(options) {
        this.maxPage = options.maxPage;
        this.defaultPage = options.defaultPage || this.defaultPage;
        this.buttonAmount = options.buttonAmount || this.buttonAmount;

        this.initDom();
    };

    $.fn.paging = function(inputOptions) {
        if (! this.length) {
            return;
        }
        if (this.attr('data-paging-id')) {
            var paging = window[this.attr('data-paging-id')];
            paging.overwrite(inputOptions);
        } else {
            var options = {
                $target: this
            };
            var domOptions = {};
            var str = this.attr('data-paging-option');
            if (str) {
                domOptions = tool.parseDomData(str);
            }
            $.extend(options, domOptions, inputOptions);
            var paging = new Paging(options);
            window[paging.id] = paging;
            this.attr('data-paging-id', paging.id);
        }
    };
})();