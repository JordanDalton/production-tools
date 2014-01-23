var usernameInput = $('input#username');
var passwordInput = $('input#password');

var usernameValue = usernameInput.val();
var passwordValue = passwordInput.val();

if(!usernameValue) usernameInput.val('Username');
if(!passwordValue) passwordInput.val('Password');


/*
 * Focus Function
 */
$('input#username, input#password').focus(function(){

    var getThis = $(this);

    switch(getThis.val().toLowerCase())
    {
        case 'username': getThis.val('');break;
        case 'password': getThis.val('');break;
    }
});

/*
 * Blur Function
 */
$('input#username, input#password').blur(function(){

    var getThis     = $(this);
    var getThisId   = getThis.attr('id');

    switch(getThisId)
    {
        case 'username':if(!getThis.val()) getThis.val('Username');break;
        case 'password':if(!getThis.val()) getThis.val('Password');break;
    }
});

// Custom jquery function to auto center the page
jQuery.fn.center = function ()
{
    this.css("position","absolute");
    this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
    this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    return this;
}

$(window).resize(function(){

    $('.wrapper').center();     
});

$(window).resize();