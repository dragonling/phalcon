/**
 * Created by tl on 14-5-21.
 */
(function($, _)
{
    function Carousel(options)
    {
        this.config = {};
        this.config.carouselTime = options.carouselTime || 5 * 1000;
        this.config.animateTime = options.animateTime || 500;
        this.config.autoPlay = options.autoPlay;
        this.config.toolbar = options.toolbar;
        this.config.arrow = options.arrow;
        this.activeIndex = options.activeIndex || 1;
        this.autoPlayTemp = null;
        this.init(options);
    };
    Carousel.prototype.init = function(options)
    {
        this.initDom(options);
        this.initEvent(options);
        this.show(this.activeIndex);
    };
    Carousel.prototype.initDom = function(options)
    {
        this.$target  = options.$target;
        this.$content = this.$target.find('.ss-content');
        this.$toolbar = this.$target.find('.ss-toolbar');
        this.innerWidth = this.$target.find('.ss-wrap').innerWidth();
        var $items = this.$content.children('.ss-item');
        this.itemWidth = $items.outerWidth();
        this.itemNum = $items.length;
        this.$content.width($items.length * this.itemWidth);
        this.minMarginLeft = this.innerWidth - this.itemNum * this.itemWidth;
        //$items.width(itemWidth);
    };
    Carousel.prototype.initEvent = function(options)
    {
        var root = this;
        root.$target.on('click', '[data-ss-action=carousel][data-ss-carousel-index]', function(e)
        {
            var $this = $(this);
            var index = $this.attr('data-ss-carousel-index');
            if (index === 'prev')
            {
                root.prev();
            }
            else if (index === 'next')
            {
                root.next();
            }
            else
            {
                index = parseInt(index);
                root.show(index);
            }
            e.preventDefault();
        });
    };
    Carousel.prototype.prev = function()
    {
        if (this.activeIndex == 1)
        {
            this.show(this.itemNum);
        }
        else
        {
            this.show(this.activeIndex - 1);
        }
    };
    Carousel.prototype.next = function()
    {
        if (this.activeIndex  == this.itemNum)
        {
            this.show(1);
        }
        else
        {
            this.show(this.activeIndex + 1);
        }
    };
    Carousel.prototype.show = function(index)
    {
        if (this.$target.attr('data-ss-state') == 'showing')
        {
            return;
        }
        this.$target.attr('data-ss-state', 'showing');
        clearTimeout(this.autoPlayTemp);
        var root = this;
        var marginLeft = this.innerWidth / 2 - ((index - 0.5) * this.itemWidth);
        if (marginLeft > 0)
        {
            marginLeft = 0;
        }
        else if (marginLeft < this.minMarginLeft)
        {
            marginLeft = this.minMarginLeft;
        }
        this.$content.stop().animate(
            {'margin-left': marginLeft},
            root.config.animateTime,
            function()
            {
                root.mark(index);
                root.$target.attr('data-ss-state', '');
                if (root.config.autoPlay)
                {
                    root.autoPlayTemp = setTimeout(_.bind(root.next, root), root.config.carouselTime);
                }
            }
        );
    };
    Carousel.prototype.mark = function(index)
    {
        var $active = this.$toolbar.find('.active');
        var $current = this.$toolbar.find('[data-ss-carousel-index=' + index + ']');
        $active.removeClass('active');
        $current.addClass('active');
        this.activeIndex = index;
    };

    $.fn.ssCarousel = function(inputOptions)
    {
        if (! this.length) {
            return;
        }
        var results = [];
        var i = 0, l = this.length;
        for (i; i < l; i++)
        {
            var $this = $(this[i]);
            if ($this.attr('data-ss-carousel') === 'initialized')
            {
                continue;
            }
            $this.attr('data-ss-carousel', 'initialized');
            var options = {
                $target : $this,
                autoPlay: true,
                toolbar : true
            };
            //todo
            //var domOptions = tool.parseDomData($this.attr('data-ss-carousel-option'));
            var domOptions = {};
            $.extend(options, domOptions, inputOptions);
            var result = new Carousel(options);
            results.push(result);
        }
        if (results.length == 1)
        {
            return results[0];
        }
        else
        {
            return results;
        }
    };

})(jQuery, _);