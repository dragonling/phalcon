$(function(){

    console.log('i am in');

    $(document).on('click.tab', '.tabbar .link', function(e){
        var $this = $(this);
        var thisBar = $this.parent();
        var tabbar = thisBar.parent();
        var tabs = tabbar.children('.tab');
        var content = tabbar.next();
        var panels = content.children();
        var activeLink = tabbar.find('.active.link');
        var activePanel = content.children('.active');
        var index = tabs.index(thisBar);
        console.log(index);
        var panel = $(panels[index]);
        activeLink.removeClass('active');
        activePanel.removeClass('active');
        $this.addClass('active');
        panel.addClass('active');
        e.preventDefault();
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
    $('button[data-toggle=stare-modal]').click(function(){

    });
});
