$(window).resize(function()
{
    var getHeader                 = $('#header');
    var getHeaderHeight           = parseInt(getHeader.height());
    var getHeaderLogoTopMargin    = parseInt($('#header').find('h1').css('marginTop'));
    var getHeadetLogoBottomMargin = parseInt($('#header').find('h1').css('marginBottom'));

    var calculateHeaderHeight     = getHeaderHeight + getHeaderLogoTopMargin + getHeadetLogoBottomMargin;

    var getWindow       = $(window);
    var getWindowHeight = getWindow.height();

    $('#content').css('min-height', getWindowHeight - calculateHeaderHeight);  
});

$(window).resize();
