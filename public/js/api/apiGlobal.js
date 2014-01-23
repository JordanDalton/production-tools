/*
 * global.js
 * 
 * @author Jordan Dalton <jordandalton@wrsgroup.com>
 */

/**
 * Variables
 */

var siteURL = (("https:" == document.location.protocol) 
            ? "https://" + document.location.host  + '/' 
            : "http://" + document.location.host + '/');




//------------------------------------------------------------------------------

// Loading Overlay
$.fn.overlay = function(){
    
    var parameters = $.extend.apply(true, arguments)
    
    $t = $(this); // CHANGE it to the table's id you have

    $("#overlay").css({
      //opacity : 0.5,
      opacity : 0.75,
      top     : $t.offset().top,
      width   : $t.outerWidth(),
      height  : $t.outerHeight()
    });

    $("#img-load").css({
      top  : ($t.height() / 2),
      left : ($t.width() / 2)
    });

    if(parameters.show){
        $("#overlay").fadeIn();    
    } else {
        $("#overlay").fadeOut();
    }
}