/*
 * layout.js
 * 
 * Conducts overrides of the css styling, so that the website layout is fluid.
 * 
 * @author Jordan Dalton <jordandalton@wrsgroup.com>
 */

$(window).resize(function()
{    
    var content     = $('#content');
    var header      = $('#header');
    var iWindow     = $(window);    
    var mainContent = $('.mainContent');
    var sideBarNav  = $('.sideBarNav');
    
    // Get the width of #content
    var contentWidth = content.width();
        
    // Get the height of #header
    var headerHeight = parseInt(header.height()) + 
                       parseInt(header.css('borderTopWidth')) + 
                       parseInt(header.css('borderBottomWidth'));
    
    // Get the height of the window
    var windowHeight = iWindow.height();
    
    // Get the width of .mainContent
    var mainContentCssWidth = parseInt(mainContent.css('borderLeftWidth')) +
                              parseInt(mainContent.css('borderRightWidth'));
                          
    // Get the height of .mainContent
    var mainContentCssHeight = parseInt(mainContent.css('borderTopWidth')) +
                               parseInt(mainContent.css('borderBottomWidth'));                
    
    // Get the width of .sideBarNav
    var sideBarNavHeight = sideBarNav.height();;
    
    // Get the width of .sideBarNav
    var sideBarNavWidth = parseInt(sideBarNav.width()) + 
                          parseInt(sideBarNav.css('borderLeftWidth')) + 
                          parseInt(sideBarNav.css('borderRightWidth'));
    
    var preSetHeight = (windowHeight - headerHeight) - 25;
    
    var calculatedHeight = (preSetHeight > sideBarNavHeight) ? preSetHeight : sideBarNavHeight;
    
    $('.mainContent').css({
        'height'    : calculatedHeight,
        'width'     : contentWidth - (sideBarNavWidth + mainContentCssWidth + .5)
    });
});

$(window).resize();
