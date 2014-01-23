/**
 * Create new API key record.
 */
$('button#refreshApiKeyTable').click(function()
{    
    // Show loading overlay
    $('#apiKeyTable').overlay({'show' : true});
    
    // Query for all of the api keys
    $.getJSON(siteURL + 'ajax/get-api-keys', function(results)
    {
        if(results.length >= 1)
        {
            $('table').find('tbody').find('tr').remove();
            
            $.each(results, function(key, value){
                
                var id      = value.id;
                var keyVal  = value.key;
                var token   = value.token;
                var secret  = value.secret;
                var string  = value.string;
                                
                $('table').find('tbody').append(
                    '<tr ' + 'record_id="' + id + '">' +
                        '<td>' + keyVal + '</td>' +
                        '<td>' + token  + '</td>' +
                        '<td>' + secret + '</td>' +
                        '<td><a class="deleteApiKey" href="#">Remove</a></td>' +
                    '</tr>'
                );
                
            });
        } else {
            
                $('table').find('tbody').append(
                    '<tr>' +
                        '<td colspan="4">Sorry, there are currently no records to list.</td>' +
                    '</tr>'
                );
            
        }
        
        // hide loading overlay
        $('#apiKeyTable').overlay({'show' : false});
        
    });
});

/******************************************************************************/
/**
 * Create new Api Key
 */
$('button#addApiRecord').click(function(){
    
    $.getJSON(siteURL + 'ajax/create-api-key', function(response){
        
        if(response)
        {           
            $('.recordNote').remove();
            $('<p class="recordNote">Api Authorization Code: <span>' + response + '</span><p>').insertBefore('table');
            
            $('button#refreshApiKeyTable').click();
        }    
    });
    
});

/******************************************************************************/
/**
 * Delete Api Key
 */
$('.deleteApiKey').live('click', function(){
    
    // Get the id of the target record to be delete.
    var nearestID = $(this).closest('tr').attr('record_id');
        
    // Query for all of the api keys
    $.getJSON(siteURL + 'ajax/delete-api-key/id/' + nearestID, function(results)
    {        
        if(results)
        {
            $('.recordNote').remove();
            $('<p class="recordNote">Record successfully removed.<p>').insertBefore('table').fadeOut(2000);
        }
        
        $('button#refreshApiKeyTable').click();
    });

});

/******************************************************************************/

$('button#refreshApiKeyTable').click();