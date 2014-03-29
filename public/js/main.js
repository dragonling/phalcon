//live news
(function(){

    var apiUrl = 'http://api.wallstreetcn.com/apiv1/livenews-list.jsonp';
    var apiType = 'jsonp';
    var dataSet;
    var html = [
        '<% for(var i=0; i<dataSet.length; i++) { %>',
        '<% var item = dataSet[i]; %>',
        '<% if (item.date.charAt(9) != dataSet[i-1].charAt(9) || i === 0) { %>',
            '<li class="date"><%= item.date %></li>',
        '<% } %>',
        '<li class="item">',
            '<span class="icon <%= item.icon %>"></span>',
            '<span class="time"><%= item.time %></span>',
            '<a class="link" href=""><%= item.title %></a>',
        '</li>',
        '<% } %>'
    ].join('');

    function initData() {
        $.ajax({
            url: apiUrl,
            dataType: apiType,
            success: function(response){
                var records = response;
                if (records && records.length) {
                    for (var i=0; i<records.length; i++) {

                    }
                }
            },
            failure: function() {
                initData();
            }
        });
    }


    /*$(function(){
        $('.nano.livenews.list').nanoScroller({
            preventPageScrolling: true
        });
    });*/

})();

$(function(){

    $(function(){
        $('.nano.list').nanoScroller({
            preventPageScrolling: true
        });
    });

    $(document).on('click.tab', '.tabbar .link', function(e){
        var $this = $(this);
        var thisBar = $this.parent();
        var tabbar = thisBar.parent();
        var tabs = tabbar.children('.tab');
        var content = tabbar.next();
        var panels = content.children();
        var activeLink = tabbar.find('.active.link');
        var activePanel = content.children('.active');
        console.log(activePanel);
        var index = tabs.index(thisBar);
        console.log(index);
        var panel = $(panels[index]);
        activeLink.removeClass('active');
        activePanel.removeClass('active');
        $this.addClass('active');
        panel.addClass('active');
        e.preventDefault();
    });

    $(document).on('click.spread', '[data-toggle=shrink]', function(e){
        $(this).parent().toggleClass('shrink');
    });
    /*
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
        $modal.children('.panel.active').removeClass('active');
        $modal.hide();
    }

    $modal.on('click', function(e){
        if (e.target == this) {
            hideModal();
        }
    });
    $('button[data-toggle=stare-modal]').click(function(){
        $modal.show(0, function(){
            $stare.addClass('active');
        });
    });
    $('#stare-modal>.close').click(function(){
        hideModal();
    });
});
