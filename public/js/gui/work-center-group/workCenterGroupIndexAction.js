/******************************************************************************/

$.ajaxSetup({
      "error":function()
      { 
          // Show loading overlay
          $t.overlay({'show' : true});
          
          alert("Error: Query Timed Out");  
      }
});

/******************************************************************************/
/*
    setTimeout(function(){
                abortAjax();
            }, 1000);

function abortAjax()
{
    console.log('aborting...');
}
*/
/******************************************************************************/

$( "#dialog:ui-dialog" ).dialog( "destroy" );

var getWindowHeight = $(window).height() * .75;
var getWindowWidth  = $(window).width()  * .75;

var workCenterList   = {};
var availableWorkCenters = [];
var unavailableWorkCenterGroups = [];

$.fn.restForm = function(){
    
    $('#autoCompleteResults').find('li').remove();
        
    // Clear out any existing errors in the list.
    $('.errorMessages').css('display','none').find('li.error').remove();
    
    $(this)[0].reset();
}

function workCenter(id, name, inUse){
    
    return {
        work_center_id   : id,
        work_center_name : name,
        work_center_use  : inUse
    }
    
}

function setErrors(setlabel, setMessage){
    
    return {
        label   : setlabel,
        message : setMessage
    }
    
}

/******************************************************************************/

// Disable the "Create Work Center Group" button on page load.
$('.newEntry').attr('disabled', true).css('opacity', .2);

/******************************************************************************/

// Retrieve all the work centers from the database.
$.getJSON(siteURL + 'ajax/get-work-center', function(results){

    // Results exits
    if(results.data.length >= 1)
    {
        // Loop through the results.
        $.each(results.data, function(key,value){
            
            workCenterList[value.work_center_id] = workCenter(
                value.work_center_id, 
                value.work_center_name,
                false
            );
        });
    }
    
    // No results
    else {
        
    }
   
    // Update the table on page load.
    $('#groupTable').updateTable();
    
});

/******************************************************************************/

function split( val ) {
    return val.split( /,\s*/ );
}

function extractLast( term ) {
    return split( term ).pop();
}

function updateAvailableWorkCenters()
{
    availableWorkCenters = [];
    
    $.each(workCenterList, function(key,value)
    {    
       
        if(value.work_center_use == false)
        {                
            // Add to list of available work centers.
            //availableWorkCenters.push(key);

            var setJoinedName = key + ' - ' + value.work_center_name;

            availableWorkCenters.push({id:key, label:setJoinedName});
        }
    });

    //console.log(availableWorkCenters);

    // Update the autocomplete source
    $('#tags').autocomplete("option", {source: availableWorkCenters});
}

/******************************************************************************/

$( "#formDialog" ).dialog({
    autoOpen: false,
    height  : 400, //getWindowHeight,
    modal   : true,
    open    : function(event,ui){

        // Check for udpates to the available work centers list
        updateAvailableWorkCenters();
 
		var availableTags = availableWorkCenters;
        
        /*
        var availableTags = [
          {id:'43',label:"43 - ActionScript"}
        ]
        */
        
		$( "#tags" )
			// don't navigate away from the field on tab when selecting an item
			.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 0,
				source: function( request, response ) {
					// delegate back to autocomplete, but extract the last term
					response( $.ui.autocomplete.filter(
						availableTags, extractLast( request.term ) ) );
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
                    
                    // Prepare the new list item
                    var newEntry = $('<li>').attr({'wc_id' : ui.item.id})
                                            .html(ui.item.value + '<a class="icon-close"></a>');
                    
                    var workCentersSelector     = '#workCenters';                    
                    var chosenWorkCenters       = $(workCentersSelector).val();
                    var chosenWorkCentersLength = chosenWorkCenters.length;
                    var setWorkCenterValue      = (chosenWorkCentersLength == 0)
                                                ? (chosenWorkCenters + ui.item.id)
                                                : (chosenWorkCenters + ',' + ui.item.id);
                    
                    // Append to the unordered list.
                    $('#autoCompleteResults').append(newEntry);

                    // Update the chosen work center list/value
                    $('#workCenters').val(setWorkCenterValue);
                    
                    // Clear out the form input.
					this.value = '';
                    
                    // Show this work center in use.
                    workCenterList[ui.item.id].work_center_use = true;
                    
                    //console.log(workCenterList[ui.item.id]);
                    
                    // Check for udpates to the available work centers list
                    updateAvailableWorkCenters();
                    
                    /*
					var terms = split( this.value );
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.value );
					// add placeholder to get the comma-and-space at the end
					terms.push( "" );
					this.value = terms.join( ", \n" );
                    */
					return false;
				}
			});
        
        
        
    },
    width   : 500 //getWindowWidth,
    /*
    buttons: {
        "Add Work Center Group": function(event) {
            
            event.preventDefault();
            
            // Get the work centers that are for submission.
            var getWorkCenters = $('#workCenters').val();
            
            var isUpdate = parseInt($("#formDialog").attr('isUpdate'));

            var apiReqType = isUpdate == 1 ? 'post' : 'get';
            
            $.getJSON(siteURL + 'ajax/' + apiReqType + '-work-center-group', function(response){
                
            });
            
            
            //$( this ).dialog( "close" );
        },
        Cancel: function() {
            $( this ).dialog( "close" );
        }
    }
   */
});

/******************************************************************************/

$("a.icon-close" ).live('click', function(event)
{
    event.preventDefault();
    
    // Get the current value from the element.
    var workCentersValue = $('input#workCenters').val();
        
    // Split the value by commas
    var explodeWorkCentersValue = workCentersValue.split(',');
     
    // Get the work center from the closest li.
    var getWorkCenterID = $(this).closest('li').attr('wc_id');
      
    $.each(explodeWorkCentersValue, function(key,value)
    {
        // trim out any padding..
        value = $.trim(value);
        
        // If value matches our target, then proceed
        if(value === getWorkCenterID)
        {            
            // Remove index key of the match
            explodeWorkCentersValue.splice(explodeWorkCentersValue.indexOf(value), 1);
            
            // Show this work center in use.
            workCenterList[value].work_center_use = false;
            
            // Udpate the available work centers list
            updateAvailableWorkCenters();
            
            //console.log(workCenterList[value]);
        }
    });
      
    // Update the value for the work centers input.
    $('input#workCenters').attr({
        value : function(_,value){     
            return explodeWorkCentersValue.join(',');
        }
    });
      
    // Delete the li item from the list.
    $(this).closest('li').remove();
});

/******************************************************************************/

// Click event to make the dialog appear.
$('.newEntry, .icon-edit').live('click', function(){
        
    var getSelector = $(this).attr('class');
    
    var setUpdate = (getSelector === 'newEntry') ? 0 : 1 ;
    
    $('#formDialog').dialog('open').attr('isUpdate', setUpdate);
    
    // Append values to the form
    $('#groupID').attr('disabled', false);
    
    switch(setUpdate)
    {
        //----------------------------------------------------------------------
        // New entry
        case 0:
            $('#formDialog').dialog({
                buttons: {
                    "Add Work Center Group": function(event) {
                        
                        event.preventDefault();

                        // Set the group id
                        var setGroupID = $('#groupID').val();
                        
                        // Set the description
                        var setDescription = $('#description').val();

                        // Set the work centers that are for submission.
                        var setWorkCenters = $('#workCenters').val();
                        
                        // Remove any spacing around the commas
                        setWorkCenters.replace(/(,\s*)/gm, ',');
                       
                        // Set the staffing
                        var setStaffing = $('#staffing').val();

                        // Set the parameters to be passed.
                        var setParams = 'group-id/'    + setGroupID     +'/' +
                                        'description/' + setDescription +'/' +
                                        'work-center/' + setWorkCenters +'/' +
                                        'staffing/'    + setStaffing
                        
                        // Check for new submission errors
                        var formErrorsExists = checkFormDialogErrors();
                        
                        if(!formErrorsExists)
                        {
                            // Send post request to the REST srever.
                            $.getJSON(siteURL + 'ajax/put-work-center-group/' + setParams, 
                                function(response){
                                    
                                    $('#formDialog').dialog( "close" );                                    
                                    $('#groupTable').updateTable();
                                });
                        }
            
                        //$( this ).dialog( "close" );
                    },
                    'Cancel' : function(){
                        
                        $( this ).dialog( "close" );
                        
                        // Rest the form
                        $('#formDialog').find('form').restForm();
                    }
                }
            });
        break;
        //----------------------------------------------------------------------
        // Update Entry
        case 1:

            var getClosestTr = $(this).closest('tr');

            var getWCs = getClosestTr.attr('workCenters');
            var splitWcs = getWCs.split(',');

            // Append values to the form
            $('#groupID').attr('disabled', true).val(getClosestTr.attr('group'));
            $('#description').val(getClosestTr.attr('description'));
            $('#workCenters').val(getClosestTr.attr('workCenters'));
            $('#staffing').val(parseInt(getClosestTr.attr('staffing')));
            
            
            $.each(splitWcs, function(key,value)
            {
                value = $.trim(value);
                
                var setString = workCenterList[value].work_center_id + ' - ' +
                                workCenterList[value].work_center_name;
                
                // Prepare the new list item
                var newEntry = $('<li>').attr({'wc_id' : workCenterList[value].work_center_id})
                                        .html(setString + '<a class="icon-close"></a>');

                // Append to the unordered list.
                $('#autoCompleteResults').append(newEntry);
                
            });

            $('#formDialog').dialog({
                buttons: {
                    "Update Work Center Group": function(event) {
                        
                        event.preventDefault();

                        // Set the group id
                        var setGroupID = $('#groupID').val();
                        
                        // Set the description
                        var setDescription = $('#description').val();

                        // Set the work centers that are for submission.
                        var setWorkCenters = $.trim($('#workCenters').val().toString());
                        
                        // Regex'd work centers
                        var regexWorkCenters = setWorkCenters.replace(/(,\s*)/, ',', 'gm');
                       
                        // Set the staffing
                        var setStaffing = $('#staffing').val();

                        // Set the parameters to be passed.
                        var setParams = 'group-id/'    + setGroupID       +'/' +
                                        'description/' + setDescription   +'/' +
                                        'work-center/' + regexWorkCenters +'/' +
                                        'staffing/'    + setStaffing
                        
                        // Check for new submission errors
                        var formErrorsExists = checkFormDialogUpdateErrors();
                        
                        if(!formErrorsExists)
                        {
                            // Send post request to the REST srever.
                            $.getJSON(siteURL + 'ajax/put-work-center-group/' + setParams, 
                                function(response){
                                    
                                    $('#formDialog').dialog( "close" );                                    
                                    $('#groupTable').updateTable();
                                });
                        }
                    },
                    'Cancel' : function(){

                        $( this ).dialog( "close" );
                        
                        // Rest the form
                        $('#formDialog').find('form').restForm();
                    }
                }
            });
        break;
        //----------------------------------------------------------------------
    }
    
    //$('#formDialog').dialog('open').attr('isUpdate', setUpdate);
   
    return false;
});

/******************************************************************************/

function checkFormDialogErrors(){
    
    var errorList = {};
    var errorsExists = false;
    
    // Get the value of the group id.
    var getGroupID = $('#groupID').val().toString();
    
    // Get the value of the descripton
    var getDescription = $.trim($('#description').val().toString());
    
    // Get the value of the work centers
    var getWorkCenters = $.trim($('#workCenters').val().toString());
    
    // Get the value of the staffing
    var getStaffing = $.trim($('#staffing').val().toString());
        
        
    // Clear out any existing errors in the list.
    $('.errorMessages').find('li.error').remove();

    // Check if group id is already being used.
    if(parseInt(unavailableWorkCenterGroups.indexOf(getGroupID)) == 0)
    {
        errorsExists = true;
        errorList['Group ID'] = setErrors('Group ID', 'The group id you entered is already being used.');
    } 
    
    // Check if description was entered.
    if(getGroupID.length < 1)
    {
        errorsExists = true;
        errorList['Group ID'] = setErrors('Group ID', 'Please entere a group id.');
    } 
    
    // Check if description was entered.
    if(getDescription.length < 1)
    {
        errorsExists = true;
        errorList['Description'] = setErrors('Description', 'Please provide a description.');
    } 
    
    // Check if work center(s) were entered.
    if(getWorkCenters.length < 1)
    {
        errorsExists = true;
        errorList['Work Centers'] = setErrors('Work Centers', 'Please select one or more work centers');
    }

    // Check if staffing was entered and if it is an integer.
    if(!getStaffing.match(/^\d+$/))
    {
        errorsExists = true;
        errorList['Staffing'] = setErrors('Staffing', 'Please enter number of staff.');
    } 
       
    // Errors do exist.
    if(errorsExists)
    {
        $('#formDialog').find('.errorMessages').css('display', 'block');
        
        $.each(errorList, function(key, value)
        {
            var createLI = $('<li>').attr('class', 'error').html('<span>' + key + ':</span> ' + value.message);

            $('#formDialog').find('ul.errorMessages').append(createLI);
        });
        
        /*
        // Disable the submit button.
        $('.ui-dialog-buttonset').find('button:first')
                                 .attr('disabled', true)
                                 .css('opacity', '.5');
        */
    } 
    
    // Errors do no exist.
    else {
        
        errorList = []; // Rest the list
        
        $('#formDialog').find('.errorMessages').css('display', 'none');
        
        // Disable the submit button.
        $('.ui-dialog-buttonset').find('button:first')
                                 .attr('disabled', false)
                                 .css('opacity', '1');
        
    }
    
    return errorsExists;
}

/******************************************************************************/

function checkFormDialogUpdateErrors(){
    
    var errorList = {};
    var errorsExists = false;
    
    // Get the value of the group id.
    var getGroupID = $('#groupID').val().toString();
    
    // Get the value of the descripton
    var getDescription = $.trim($('#description').val().toString());
    
    // Get the value of the work centers
    var getWorkCenters = $.trim($('#workCenters').val().toString());
    
    // Get the value of the staffing
    var getStaffing = $.trim($('#staffing').val().toString());
        
    // Clear out any existing errors in the list.
    $('.errorMessages').find('li.error').remove();
         
    // Check if description was entered.
    if(getGroupID.length < 1)
    {
        errorsExists = true;
        errorList['Group ID'] = setErrors('Group ID', 'Please entere a group id.');
    }
       
    // Check if description was entered.
    if(getDescription.length < 1)
    {
        errorsExists = true;
        errorList['Description'] = setErrors('Description', 'Please provide a description.');
    } 
    
    // Check if work center(s) were entered.
    if(getWorkCenters.length < 1)
    {
        errorsExists = true;
        errorList['Work Centers'] = setErrors('Work Centers', 'Please select one or more work centers');
    }

    // Check if staffing was entered and if it is an integer.
    if(!getStaffing.match(/^\d+$/))
    {
        errorsExists = true;
        errorList['Staffing'] = setErrors('Staffing', 'Please enter number of staff.');
    } 
       
    // Errors do exist.
    if(errorsExists)
    {
        $('#formDialog').find('.errorMessages').css('display', 'block');
        
        $.each(errorList, function(key, value)
        {
            var createLI = $('<li>').attr('class', 'error').html('<span>' + key + ':</span> ' + value.message);

            $('#formDialog').find('ul.errorMessages').append(createLI);
        });
        
        /*
        // Disable the submit button.
        $('.ui-dialog-buttonset').find('button:first')
                                 .attr('disabled', true)
                                 .css('opacity', '.5');
        */
    } 
    
    // Errors do no exist.
    else {
        
        errorList = []; // Rest the list
        
        $('#formDialog').find('.errorMessages').css('display', 'none');
        
        // Disable the submit button.
        $('.ui-dialog-buttonset').find('button:first')
                                 .attr('disabled', false)
                                 .css('opacity', '1');
        
    }
    
    return errorsExists;
}

/******************************************************************************/

// Update the work center group table with the latest data.
$.fn.updateTable = function()
{       
    $t = $(this);
    
    // Show loading overlay
    $t.overlay({'show' : true});
    
    // Delete any existing table rows.
    $t.find('tbody').find('tr').remove();
    $t.find('tbody').find('tr').remove();
    
    // Rest the form
    $('#formDialog').find('form').restForm();
    
    // Query for all the current work center groups
    $.getJSON(siteURL + 'ajax/get-work-center-group', function(response)
    {
        // Capture if data exists.
        var dataLength = response.data ? response.data.length : 0;

        // Data Exists
        if(dataLength >= 1)
        {
            // Create index array for work centers to be appended to.
            var workCenters = []

            // Begin sub-loop
            $.each(response.data, function(key, value)
            {
                // Show this work center in use.
                workCenterList[value.cwwcid].work_center_use = true;
                
                // If group is not in use, add it to the index.
                if(parseInt(workCenters.indexOf(value.wcg_id)) < 0)
                {                       
                    // Add to the index of groups.
                    workCenters.push(value.wcg_id);
                    
                    // Add to work center groups that are being used.
                    unavailableWorkCenterGroups.push(value.wcg_id);

                    var editLink = '<a href="#" class="icon-edit"></a>' +
                                   '<a href="#" class="icon-delete"></a>';

                    /***********************************************************
                     * Individual Labor Capacity Formula
                     * ------------------------------------
                     * 60 minutes (1hr) x 8 hrs (work day)      =   480 mins
                     * 480 (minutes) - 20 minutes (for breaks)  =   460 mins
                     * 460 (minutes) x .9 (90% efficiency(      =   414 minutes
                     * 414 (minutes) / 60 (# of minutes/hr)     =   6.9 hours
                     **********************************************************/
                    var formula = (((60 * 8) - 20) *.9)/60;
                    
                    var staffingLevel = parseInt(value.wcg_staffing);
                    var laborCapacity = parseFloat(value.wcg_staffing * formula).toFixed(2);

                    // Create new row in the table.
                    $t.find('tbody').append(
                        '<tr>' +
                            '<td>' + value.wcg_id          + '</td>' +
                            '<td>' + value.cwwcid          + '</td>' +
                            '<td>' + value.wcg_name        + '</td>' +
                            '<td class="staffingCap">' + staffingLevel         + '</td>' +
                            '<td class="laborCap">' + laborCapacity         + '</td>' +
                            '<td>' + editLink + '</td>' +
                        '</tr>'
                    );

                    // Give our latest row some attributes.
                    $t.find('tbody').find('tr:last').attr({
                        'group'         : value.wcg_id,
                        'workCenters'   : value.cwwcid,
                        'description'   : value.wcg_name,
                        'staffing'      : value.wcg_staffing
                    });
                }

                // Append to exsiting record.
                else {

                    // Get the row of the existing group
                    var getRow = $t.find('tbody')
                                   .find('tr[group="'+ value.wcg_id +'"]');

                    // Get the work centers 
                    var getWorkCenter = getRow.find('td:nth-child(2)').text();

                    // Set the new work center(s)
                    var newWorkCenter = getWorkCenter + ', ' + value.cwwcid;

                    getRow.attr('workCenters', newWorkCenter)
                          .find('td:nth-child(2)')
                          .text(newWorkCenter);
                }
            });	
            // End sub-loop
            
            // Calculate total staffing
            var totalStaffing = 0;
            
            $('table').find('tbody').find('.staffingCap').each(function(){
                
                var getVal = parseFloat($(this).html());
                
                totalStaffing += getVal;
                
            });
           
            
            // Calculate total labor capacity
            var totalLaborCapacity = 0;
            
            $('table').find('tbody').find('.laborCap').each(function(){
                
                var getVal = parseFloat($(this).html());
                
                totalLaborCapacity += getVal;
                
            });
            
            $('.totalStaffing').html(totalStaffing);
            $('.totalLaborCapacity').html(totalLaborCapacity);
            
        }

        // No data returned.
        else {

            $('.totalStaffing').html(0);
            $('.totalLaborCapacity').html(0);

            // Create new row in the table.
            $t.find('tbody').append(
                '<tr>' +
                    '<td colspan="6">There are no work center groups in use.</td>' +
                '</tr>'
            );

        }
        
        // Enable the "Create Work Center Group" button and make it visible.
        $('.newEntry').attr('disabled', false).css('opacity', 1);
        
        // Show loading overlay
        $t.overlay({'show' : false});        
    });
}

/******************************************************************************/

//$.getJSON(siteURL + 'ajax/get-work-center', function(response){});

/******************************************************************************/

// Event when the delete icon is clicked.
$('.icon-delete').live('click', function(event){
    
    // Prevent default click action
    event.preventDefault();
    
    // Get the group id this link is referenced to.
    var getGroup = $(this).closest('tr').attr('group');
    
    // Get the work centers this link is referenced to.
    var getWorkCenters = $(this).closest('tr').attr('workCenters');
    
    // Attempt to delete the work center group.
    $.getJSON(siteURL + 'ajax/delete-work-center-group/group-id/' + getGroup, function(response)
    {
        // Check if delete failed.
        if(response.status === 'failed')
        {
            // Alert the user of the error.
            alert(response.message);
        } 
        
        // Delete was successful
        else {
            
            // Split the work centers by comma
            var explodeWorkCenters = getWorkCenters.split(',');
            
            // Loop through all the work centers
            $.each(explodeWorkCenters, function(key,value)
            {
                // Trim the value.
                value = $.trim(value);
                
                // Show this work center in use.
                workCenterList[value].work_center_use = false;
            });

            // Remove from the unavailable work center groups list            
            unavailableWorkCenterGroups.splice(unavailableWorkCenterGroups.indexOf(getGroup), 1);

            // Update the table
            $('#groupTable').updateTable();
        }
    });
});