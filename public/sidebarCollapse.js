var ww= $(window).width();

$(function () {

    if (ww <= 670)
    {

        $('.hideable').hide();
        $('.OpA').css("margin-left","0.7em");
        $('.number').css("top","316px");
        $('.number').css("left","27px");
        $('div.sideBar').addClass('sidebarShrink');
        $('li#activeli').addClass('sideBarBar');


    }

})
