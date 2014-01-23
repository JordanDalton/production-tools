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

// Just incase we neeed to use it.
var apiURL  = siteURL.replace(/(productiontoolsv2)/, "api.$1");

/***********************************************************
 * Individual Labor Capacity Formula
 * ------------------------------------
 * 60 minutes (1hr) x 8 hrs (work day)      =   480 mins
 * 480 (minutes) - 20 minutes (for breaks)  =   460 mins
 * 460 (minutes) x .9 (90% efficiency(      =   414 minutes
 * 414 (minutes) / 60 (# of minutes/hr)     =   6.9 hours
 **********************************************************/
var formula = (((60 * 8) - 20) *.9)/60;

/******************************************************************************/

$(function(){

    $("ul.navigation li").hover(function(){
    
        $(this).addClass("hover");
        $('ul:first',this).css('visibility', 'visible');
    
    }, function(){
    
        $(this).removeClass("hover");
        $('ul:first',this).css('visibility', 'hidden');
    
    });
    
    $("ul.navigation li ul li:has(ul)").find("a:first").append(" &raquo; ");

});

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