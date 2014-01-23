
// Get the run set parameter from the section header
var getRunSet = parseInt($('.sectionHeader').attr('runSet'));

// If getRunSet is 1, then true (yes), otherwise false (no)
var runSet = (getRunSet == 1) ? true : false;

var workCenterGroupJSON = false;

//------------------------------------------------------------------------------

// implement JSON.stringify serialization
JSON.stringify = JSON.stringify || function (obj) {
    var t = typeof (obj);
    if (t != "object" || obj === null) {
        // simple data type
        if (t == "string") obj = '"'+obj+'"';
        return String(obj);
    }
    else {
        // recurse array or object
        var n, v, json = [], arr = (obj && obj.constructor == Array);
        for (n in obj) {
            v = obj[n];t = typeof(v);
            if (t == "string") v = '"'+v+'"';
            else if (t == "object" && v !== null) v = JSON.stringify(v);
            json.push((arr ? "" : '"' + n + '":') + String(v));
        }
        return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
    }
};

//------------------------------------------------------------------------------

// Expand or minimize icon click event.
$('.boxTitle').find('a').click(function(event)
{    
    event.preventDefault();    
    
    var getClassName = $(this).attr('class');
    var getParent    = $(this).parent();
    
    switch(getClassName)
    {
        //----------------------------------------------------------------------
        case 'collapse':
            
            $(this).attr('class', 'expand');
            //$(this).closest('.boxContainer').find('.boxContent').css('display', 'none');
            
            $(this).closest('.boxContainer').find('.boxContent').fadeOut('fast');
            
        break;
        //----------------------------------------------------------------------
        case 'expand':
            
            $(this).attr('class', 'collapse');
            //$(this).closest('.boxContainer').find('.boxContent').css('display', 'block');
            
            $(this).closest('.boxContainer').find('.boxContent').fadeIn('fast');
            
        break;
        //----------------------------------------------------------------------
    }
});

//------------------------------------------------------------------------------

// If the user changed to another report name, clear out the form.
$('#selectReportName').live('change', function(event){
    
    var getThis = $(this);
    var workCentersSelector     = '#selectReportWorkCenters';
    var workOrderStatusSelector = '#selectReportOrderStatuses';
    var reportsFilterSelector   = '.reportFilters';
    var codeFilterSelector      = '#selectReportFilters-code'
            
	// Loop through all the filters and rest them
	$.each($('input[id^=selectReportFilters-]'), function(key,value)
	{
		var prependSelector = 'input#selectReportFilters-';
			
		$(prependSelector + this.value).attr('checked', false)
									   .click()
									   .attr('checked', false);
	
	});
    
    switch($(this).val())
    {
        //----------------------------------------------------------------------
        case 'WO1002RG':
            
            // Hide the work centers multi-select box and description.
            $(workCentersSelector).css('display', 'none')
                                  .prev('.specialNote').css('display', 'none')
                                  .prev('.hint').css('display', 'none');
                                  
			// Clear out any previous work-centers that were selected.
			$(workCentersSelector).val('');
                                     
			// Clear out any previous work order status(es) that were selected.
			$(workOrderStatusSelector).val('');
            
            // Show the work order status selection
            $(workOrderStatusSelector).css('display', 'block')
            
            $(reportsFilterSelector).find('label[for=selectReportFilters-item_number]').removeAttr('style');
            $(reportsFilterSelector).find('br:nth-child(2)').removeAttr('style');
            
            // Hide the code filter selector
            $(reportsFilterSelector).find('label[for=selectReportFilters-code]').css('display', 'none');
            
            // Clear out any previous code that was selected.
            $(codeFilterSelector).val('');

            // Hide the <br/> after code
            $(reportsFilterSelector).find('br:first').css('display', 'none');
            
            
        break;
        //----------------------------------------------------------------------
        case 'OR1000RG':
            
            // Hide the work centers multi-select box and description.
            $(workCentersSelector).css('display', 'none')
                                  .prev('.specialNote').css('display', 'none')
                                  .prev('.hint').css('display', 'none');
                                  
			// Clear out any previous work-centers that were selected.
			$(workCentersSelector).val('');
                                     
			// Clear out any previous work order status(es) that were selected.
			$(workOrderStatusSelector).val('');
            
            // Hide the work stats multi-select box and description
            $(workOrderStatusSelector).css('display', 'none')
                                  .prev('.hint').css('display', 'none');


            // Hide the code filter selector
            $(reportsFilterSelector).find('label[for=selectReportFilters-code]').removeAttr('style');
                        
            // Hide the item number checkbox
            $(reportsFilterSelector).find('label[for=selectReportFilters-item_number]').css('display', 'none');
            
            // Hide the <br/> after item number
            $(reportsFilterSelector).find('br:first').css('display', 'none');
            
            
        break;    
        //----------------------------------------------------------------------
        case 'WO1010RG':
            // Show the work centers multi-select box and description.
            $(workCentersSelector).css('display', 'block')
                                  .prev('.specialNote').css('display', 'block')
                                  .prev('.hint').css('display', 'block');
                                  
            // Show the work centers multi-select box and description.
            $(workOrderStatusSelector).css('display', 'block')
                                      .prev('.hint')
                                      .css('display', 'block');
                                      
			// Clear out any previous work order status(es) that were selected.
			$(workOrderStatusSelector).val('');
            
            // Show the work order status selection
            $(workOrderStatusSelector).css('display', 'block')
            
            $(reportsFilterSelector).find('label[for=selectReportFilters-item_number]').removeAttr('style');
            $(reportsFilterSelector).find('br:nth-child(2)').removeAttr('style');
                        
            // Hide the code filter selector
            $(reportsFilterSelector).find('label[for=selectReportFilters-code]').css('display', 'none');
            
            // Clear out any previous code that was selected.
            $(codeFilterSelector).val('');
            
            // Hide the <br/>
            $(reportsFilterSelector).find('br:first').css('display', 'none');
            
            
        break;
        //----------------------------------------------------------------------
    }
    
});

//------------------------------------------------------------------------------
// Set input default values.
var inputDefaultValues = [];
inputDefaultValues['item_number']  = 'Enter Item #';
inputDefaultValues['order_number'] = 'Enter Order #';
//------------------------------------------------------------------------------

$('input#selectReportFilters-code').live('click', function()
{
    var getIDValue = this.id;
    var getElementFromID = getIDValue.toString().replace(/selectReportFilters-(.*)/g, "$1");

    // Check if it is checked/unchecked
    var isSelected = $(this).is(':checked') ? true : false;
    
    // Variable for html that will be added.
    var htmlToInsert;  

    switch(isSelected)
    {
        //----------------------------------------------------------------------
        /*
         * Is Checked
         *  -- Append text input element below
         */
        case true:
            
            htmlToInsert = $('<select/>').attr({
                autocomplete : 'off',
                name :  getElementFromID
            });
            
            htmlToInsert.append(
                '<option value="m" selected="selected">M</option>' +
                '<option value="p">P</option>'
            );
            
            var labelSelector = 'label[for=' + getIDValue + ']';

            $(htmlToInsert).insertAfter($(labelSelector));
            
            $('select[name=' + getElementFromID + ']').val('m')
            
        break;
        //----------------------------------------------------------------------
        /*
         * Is not/no-longer checked
         * -- Remove the appended text element
         */
        case false:
            
            // Remove the input text element should it exist.
            $('select[name=' + getElementFromID + ']').remove();
            
        break;
        //----------------------------------------------------------------------
    }
    
});

//------------------------------------------------------------------------------

$('input#selectReportFilters-item_number, input#selectReportFilters-order_number').live('click', function()
{    
    var getIDValue = this.id;
    var getElementFromID = getIDValue.toString().replace(/selectReportFilters-(.*)/g, "$1");

    // Check if it is checked/unchecked
    var isSelected = $(this).is(':checked') ? true : false;
    
    // Variable for html that will be added.
    var htmlToInsert;    
    
    switch(isSelected)
    {
        //----------------------------------------------------------------------
        /*
         * Is Checked
         *  -- Append text input element below
         */
        case true:
            
            htmlToInsert = $('<input/>').attr({
                autocomplete : 'off',
                name :  getElementFromID,
                type :  'text', 
                value:  inputDefaultValues[getElementFromID]
            });
            
            var labelSelector = 'label[for=' + getIDValue + ']';

            $(htmlToInsert).insertAfter($(labelSelector));
            
        break;
        //----------------------------------------------------------------------
        /*
         * Is not/no-longer checked
         * -- Remove the appended text element
         */
        case false:
            
            // Remove the input text element should it exist.
            $('input[type=text][name=' + getElementFromID + ']').remove();
            
        break;
        //----------------------------------------------------------------------
    }

});

//------------------------------------------------------------------------------

// Update "RUN WORK ORDER REPORT" form
function updateRunWorkOrderReportForm()
{
    // What is the select that we're getting the attribute data from?
    var sectionHeaderSelector = '.sectionHeader';
    
    // What is the selector of the form that changes will be made to?
    var selectReportFormSelector = '#selectReportForm';
    
    // Which attributes are we going to need data from.
    var attributesToGet = [
        'runReport',
        'runWorkCenters',
        'runOrderNumber',
        'runItemNumber',
        'runStatus',
        'runCode'
    ]
    
    // Create index array for the the run values to be appended to.
    var getRuns = []
    
    // If runSet is tru, proceed to further logic.
    if(runSet)
    {
        // Minimize the "Run WOrk Order Report" box if runSet is true;
        $('.runWorkOrderReportContainer').find('.boxTitle').find('a').click();
        
        /*
         * Loop through the attributesToGet array, and create a variable with
         * appropriate value form the page.
         */
        $.each(attributesToGet, function(key, value){
            
            // Append to the getRuns array
            getRuns[value] = {stringVal : $(sectionHeaderSelector).attr(value)};
        });
        
        // Update "Select Report" with the current report name
        $(selectReportFormSelector).find('#selectReportName').val(getRuns['runReport'].stringVal);
        
        $('#selectReportName').change();
        
        // Split the values of each work center
        var splitWorkCenters = getRuns['runWorkCenters'].stringVal.split(',');
        
        // Update to be a checked option
        $(selectReportFormSelector).find('#selectReportWorkCenters').val(splitWorkCenters);
        
        // Split the values of each order status
        var splitOrderStatus = getRuns['runStatus'].stringVal.split(',');
        
        // Update to be a checked option
        $(selectReportFormSelector).find('#selectReportOrderStatuses').val(splitOrderStatus);
        
        // If item number has a value
        if(getRuns['runItemNumber'].stringVal.length > 0)
        {
            $('input#selectReportFilters-item_number').click()
                                                      .click()
                                                      .attr({'checked' : true});
                                                      
                                                      
            $('input[name=item_number]').attr({value : getRuns['runItemNumber'].stringVal});
                                                      
        }
        
        // If order number has a value
        if(getRuns['runOrderNumber'].stringVal.length > 0)
        {
            $('input#selectReportFilters-order_number').click().click().attr('checked', true);
            
            $('input[name=order_number]').attr({value : getRuns['runOrderNumber'].stringVal});
        } 
        
        // If order number has a value
        if(getRuns['runCode'].stringVal.length > 0)
        {
            $('input#selectReportFilters-code').click().click().attr('checked', true);
            
            $('select[name=code]').attr({value : getRuns['runCode'].stringVal});
        } 
       
    }
}

//------------------------------------------------------------------------------

function calculateTotalBackOrderValue()
{
    // What is the select that we're getting the attribute data from?
    var sectionHeaderSelector = '.sectionHeader';
    
    // Get the name of the report that was ran.
    var getReportname = $(sectionHeaderSelector).attr('runReport');
    
    // Set the default total backorder value to 0.
    var calculatedTotalBOValue = 0;
    
    switch(getReportname)
    {
        //----------------------------------------------------------------------
        case 'WO1010RG':
            
           // Reset the value to 0
           calculatedTotalBOValue = 0;

           // Loop through each back order value colum data
           $('table').find('tbody')
                     .find('tr')
                     .find('td:nth-child(8)')
                     .each(function(key, value)
                     {
                         // Get the html and strip it of $ signs.
                         var getCellVal = $.trim(value.innerHTML).replace(/\$/, '');

                         // Convert value to a float.
                         var cellValToFloat = parseFloat(getCellVal);

                         // Add it to the existing calculatedTotalBOValue
                         calculatedTotalBOValue += cellValToFloat;
                     });

           // Round to nearest tenth
           calculatedTotalBOValue = parseFloat(calculatedTotalBOValue).toFixed(2);

           // Commafy it...
           calculatedTotalBOValue = calculatedTotalBOValue.replace(/\d{1,3}(?=(\d{3})+(?!\d))/g, '$&,');

           // Update the html on the page.
           $('.totalBackOrderValue').html(calculatedTotalBOValue);
            
        break;
        //----------------------------------------------------------------------
        case 'WO1002RG':
            
           // Reset the value to 0
           calculatedTotalBOValue = 0;

           // Loop through each back order value colum data
           $('table').find('tbody')
                     .find('tr')
                     .find('td:nth-child(4)')
                     .each(function(key, value)
                     {
                         // Get the html and strip it of $ signs.
                         var getCellVal = $.trim(value.innerHTML).replace(/\$/, '');

                         if(getCellVal === 'NaN') getCellVal = 0;

                         // Convert value to a float.
                         var cellValToFloat = parseFloat(getCellVal);
                         

                         // Add it to the existing calculatedTotalBOValue
                         calculatedTotalBOValue += cellValToFloat;
                     });

           // Round to nearest tenth
           calculatedTotalBOValue = parseFloat(calculatedTotalBOValue).toFixed(2);

           // Commafy it...
           calculatedTotalBOValue = calculatedTotalBOValue.replace(/\d{1,3}(?=(\d{3})+(?!\d))/g, '$&,');

           // Update the html on the page.
           $('.totalBackOrderValue').html(calculatedTotalBOValue);
            
        break;
        //----------------------------------------------------------------------
    }
    
}

//------------------------------------------------------------------------------

$('#selectReportRuButton').live('click', function(event)
{    
	// Prevent form from submitting
	event.preventDefault();

   // Show loading overlay
   $('.mainBoxContainer').overlay({'show' : true});
    
    // Delete any existing rows in the table.
	$('table').find('tbody').find('tr').remove();
       
    /*
     *--------------------------------------------------------------------------
     * Get the gets ;)
     * -------------------------------------------------------------------------
     * getReportName                : Get the name of desired report.
     * getSelectedWorkCenters       : Get the selected work centers.
     * getSelectedWorkOrderStatuses : Get the selected work order status(es)
     * getSelectedFilters           : Get the filters
     */
    var getReportName                = $('#selectReportName').val();
    var getSelectedWorkCenters       = $('#selectReportWorkCenters').val();
    var getSelectedWorkOrderStatuses = $('#selectReportOrderStatuses').val();
    var getSelectedFilters           = $('.reportFilters').find('input:checked').map(function(i,n){return $(n).val();});

    // Arrays
    if(getSelectedWorkCenters)       var workCentersArray     = getSelectedWorkCenters.join(',');
    if(getSelectedWorkOrderStatuses) var workOrderStatusArray = getSelectedWorkOrderStatuses.join(',');
    var filtersArray = [];
    var uriArray     = [];
    
    var resultsCount = 0;
    var resutlsCountMessage;
    
    var showTotalBackOrderMessage = false;
    var calculatedTotalBOValue    = 0;
    
    $('body').attr('currentReport', getReportName);
        
   // Loop through the filters selected
   $.each(getSelectedFilters.get(), function(key, val)
   {       
       // Check to see if it contains an underscore (_)
       var match = val.match(/_/g) ? true : false;
              
       // Default the key name to the value for the key
       var keyName = val;
       
       // If underscore id detected, replace it with a hyphen
       if(match) keyName = val.toString().replace(/_/g, '-');
       
       // Prepare the filter string for its role in the URI
       var addFilter = (val === 'code') ? keyName + '/' + $('select[name=' + val + ']').val() : keyName + '/' + $('input[name=' + val + ']').val();
       
       // Get the input value
       var getInputValue = (val === 'code') ? $('select[name=' + val + ']').val() : $('input[name=' + val + ']').val();
              
       // Check if input matches the default value for this field.
       if(jQuery.trim(getInputValue) === inputDefaultValues[val])
       {
           // Uncheck the box, simulate click event, and uncheck again
           $('input#selectReportFilters-'+val).attr('checked', false).click().attr('checked', false);
           
       } else {
           // Add the URI string to the array of filters that will applied to the URI
           filtersArray.push(addFilter);
       }
       
   }); // END -> $.each(getSelectedFilters.get(), function(key, val)
      
   /*
    * Merge the following arrays
    *   - Work Centers
    *   - Filters
    */
   uriArray.push('run/' + getReportName);
   if(workCentersArray) uriArray.push('work-center/' + workCentersArray);
   if(workOrderStatusArray) uriArray.push('order-status/' + workOrderStatusArray);
   uriArray.push(filtersArray.join("/"));

   var joinPath = uriArray.join('/'); 
   var setURIParams = (joinPath.length >= 1) ? '/' + joinPath : joinPath;

    // Redirect user to the results.
    window.location.replace(siteURL + 'work-order/index' + setURIParams);

});


//------------------------------------------------------------------------------

$('.saveWorkOrderTableReport').click(function(event){
   
    event.preventDefault();

    //$('.results-count').overlay({'show' : true});

    // What is the select that we're getting the attribute data from?
    var sectionHeaderSelector = '.sectionHeader';
    
    // Get the name of the report that was ran.
    var getReportname = $(sectionHeaderSelector).attr('runReport');
    
    // Get the name of the work centers being used.
    var getWorkCenters = $('.sectionHeader').attr('runWorkCenters');

    var setHeaders = '';

    switch(getReportname){
        /**********************************************************************/
        case 'WO1010RG':
            
            setHeaders = "{'0':'Co/Wo No.','1':'Order Status','2':'WC','3':'Item \#','4':'Description','5':'Order Qty','6':'B/O Qty','7':'B/O Value','8':'Build Time (Hrs)','9':'U/M','10':'Due Date'}";
            
        break;
        /**********************************************************************/
        case 'WO1002RG':
                        
            setHeaders = "{'0':'Item \#','1':'Item Description','2':'B/O Qty','3':'B/O Value','4':'Status','5':'Co/Wo No.','6':'Order Qty'}";
            
        break;
        /**********************************************************************/
    }


    $("table").tabletojson({
        headers: setHeaders,
        onComplete: function (x) {
            
            $('input#report').val(getReportname);
            $('input#bo_value').val($('.totalBackOrderValue').html().toString().replace(/\,/g, ''));
            $('input#record_count').val(parseInt($('.totalRecordsNumber').html()));
            $('textarea#work_centers').val((getReportname === 'WO1010RG') ? getWorkCenters : '');
            $('textarea#work_center_groups').val(workCenterGroupJSON ? workCenterGroupJSON.toSource().replace(/^(\()(.*)(\))$/, '$2').replace(/(\w+)\:/g, '"$1":') : '');
            $('textarea#postData').val(encodeURI(x));
            
            
            $('form[method="post"]').submit();
            
            /* Debug out *\/
            console.log(x);
            /**/
            
            /**\/
            $.ajax({
                beforeSend : function(xhr){
                    // Send as JSON, UTF-8 encoded format.
                    xhr.overrideMimeType('application/json; charset=UTF-8');
                },
                cache   : false,
                dataType: 'json',
                data    : {
                    postData    : encodeURI(x),
                    report      : getReportname,
                    bo_value    : $('.totalBackOrderValue').html().toString().replace(/\,/g, ''),
                    record_count: parseInt($('.totalRecordsNumber').html()),
                    work_centers: (getReportname === 'WO1010RG') ? getWorkCenters : '',
                    work_center_groups : workCenterGroupJSON ? workCenterGroupJSON.toSource().replace(/^(\()(.*)(\))$/, '$2').replace(/(\w+)\:/g, '"$1":') : ''
                },
                success : function(){
                    
                    $('.results-count').overlay({'show' : false});
                    
                    // Prevent the 15 second timeout message from appearing.
                    abortAjaxRequest = false;
                },
                type    : 'POST',
                url     : siteURL + 'ajax/save-work-order-table-report'
            });
            /**/
        }
    });

}); // END -> $('.runTest').click(function(event)

//------------------------------------------------------------------------------

$('.saveWorkOrderTableReportBackup').click(function(event){
    
    event.preventDefault();

    $('.results-count').overlay({'show' : true});

    // What is the select that we're getting the attribute data from?
    var sectionHeaderSelector = '.sectionHeader';
    
    // Get the name of the report that was ran.
    var getReportname = $(sectionHeaderSelector).attr('runReport');
    
    // Get the name of the work centers being used.
    var getWorkCenters = $('.sectionHeader').attr('runWorkCenters');

    var setHeaders = '';

    switch(getReportname){
        /**********************************************************************/
        case 'WO1010RG':
            
            setHeaders = "{'0':'Co/Wo No.','1':'Order Status','2':'WC','3':'Item \#','4':'Description','5':'Order Qty','6':'B/O Qty','7':'B/O Value','8':'Build Time (Hrs)','9':'U/M','10':'Due Date'}";
            
        break;
        /**********************************************************************/
        case 'WO1002RG':
                        
            setHeaders = "{'0':'Item \#','1':'Item Description','2':'B/O Qty','3':'B/O Value','4':'Status','5':'Co/Wo No.','6':'Order Qty'}";
            
        break;
        /**********************************************************************/
    }


    $("table").tabletojson({
        headers: setHeaders,
        //attributes: "{'customerID':'CustomerID', 'orderID':'OrderID'}", //supply attributes you wan to include, attribute name and then how you want it to appear in JSON string.
        onComplete: function (x) {
            
            /* Debug out *\/
            console.log(x);
            /**/
            
            /**/
            $.ajax({
                beforeSend : function(xhr){
                    // Send as JSON, UTF-8 encoded format.
                    xhr.overrideMimeType('application/json; charset=UTF-8');
                },
                cache   : false,
                dataType: 'json',
                data    : {
                    postData    : encodeURI(x),
                    report      : getReportname,
                    bo_value    : $('.totalBackOrderValue').html().toString().replace(/\,/g, ''),
                    record_count: parseInt($('.totalRecordsNumber').html()),
                    work_centers: (getReportname === 'WO1010RG') ? getWorkCenters : '',
                    work_center_groups : workCenterGroupJSON ? workCenterGroupJSON.toSource().replace(/^(\()(.*)(\))$/, '$2').replace(/(\w+)\:/g, '"$1":') : ''
                },
                success : function(){
                    
                    $('.results-count').overlay({'show' : false});
                    
                    // Prevent the 15 second timeout message from appearing.
                    abortAjaxRequest = false;
                },
                type    : 'POST',
                url     : siteURL + 'ajax/save-work-order-table-report'
            });
            /**/
        }
    });

}); // END -> $('.runTest').click(function(event)

//------------------------------------------------------------------------------



/**
 * Now comes the interesting (complicated..) part. We will check to see if the
 * work center(s) in the report are assigned to a work center group. 
 * 
 * If they are, the following is done (in order)
 *  1) Loop through each row
 *  2) Check to see if the work center in the td exists in a work center group
 *  3) If it exists, subtract the build time from the total availble labor hours for that work center GROUP
 *  4) When we have excess (negative number), set a break point in the table and gray out all remaining rows.
 */
$('.applyWorkCenterGroups').find('button').live('click', function(event){
   
   // Prevent postback from taking place.
   event.preventDefault();
   
   var  tableColors = ['blue', 'red', 'green', 'purple', 'orange'];
   var  tableColorDetails = {};
   var  tableColorCounter = 0;
   
   
   $('.applyWorkCenterGroups').overlay({'show' : true});   
   
   // console.log('i was clicked');
   
   var getCurrentReport               = $('.sectionHeader').attr('runReport');
   var getApplicableWorkCenters       = $('.sectionHeader').attr('runWorkCenters');
   var getApplicableWorkCentersLength = parseInt(getApplicableWorkCenters);
   
   var applyWorkCenters = (getApplicableWorkCentersLength >= 1)
                        ? '/work-center/' + getApplicableWorkCenters
                        : '';
   
   var isWO1002RG    = (getCurrentReport === 'WO1002RG') ? 'WO1002RG' : false;
   var isWO1010RG    = (getCurrentReport === 'WO1010RG') ? 'WO1010RG' : isWO1002RG;
   var currentReport = isWO1010RG;
   
   var workCenters            = [];
   var workCenterDetails      = {};
   var workCenterGroups       = [];
   var workCenterGroupDetails = {};
   var workCenterColumn;
   
   var breakPointMet = false;
   
   // First remove any class attributes to any existing rows
   $('table').find('tbody').find('tr').each(function(key,value){
       $(this).removeAttr('class');
   });
   
   $('.appliedWorkCenterGroupData').find('ul').find('li, ul').remove();
   
      
   // Retrieve all the work center groups from the database.
   xhr = $.getJSON(siteURL + 'ajax/get-work-center-group' + applyWorkCenters, function(results){

        workCenterGroupJSON = results;

        // Show overlay on table.
        $('.tableResultsContainer').overlay({'show' : true});

        // Prevent the 15 second timeout message from appearing.
        abortAjaxRequest = false;
        
        // Results exits
        if(results.data.length >= 1)
        {
            // Loop through the results.
            $.each(results.data, function(key,value)
            {                 
                // Setters
                var setGroup = value.wcg_id;
                var setName  = value.cwwcid;
                var setLabor = value.wcg_staffing * formula;
                
                var groupMatch = parseInt(workCenterGroups.indexOf(setGroup)) == 0;
              
                // If the group is not in our work center groups array, then add it.
                if(!groupMatch){
                    
                    // Add to work center groups array
                    workCenterGroups.push(setGroup);
                    
                    // Now set the labor for particular group.
                    workCenterGroupDetails[setGroup] = {labor : setLabor, breakPoint : false};
                }
                
                // Since all work centers will be unique, we will append them by default
                workCenters.push(setName);
                
                // The same for their details
                workCenterDetails[setName] = {
                    group : setGroup,
                    labor : setLabor
                };
            });
            
            // If current report is true, proceed.
            if(currentReport)
            {
                var workCenterColumn = false;
                var laborHoursColumn = false;
                
                if(currentReport === 'WO1010RG')
                {
                    workCenterColumn = 'td:nth-child(3)';
                    laborHoursColumn = 'td:nth-child(9)';
                }
                
                if(workCenterColumn)
                {    
                    // Now lets start to loop through the table
                    $('table').find('tbody').find('tr').find(workCenterColumn).each(function(key,value){
                        
                        // Get work center value
                        var getTDVal = $.trim(value.innerHTML).toString();
                        
                        // Get build time for this particular row.
                        var getLabor = parseFloat($(this).closest('tr').find(laborHoursColumn).html());
                        
                        // Check if work center matches one that we're looking for.
                        //var wcMatch = parseInt(workCenters.indexOf(getTDVal)) == 0;
                        var wcMatch = parseInt($.inArray(getTDVal, workCenters)) >= 0;
                        
                        // If there was a match, proceed to further logic.
                        if(wcMatch)
                        {
                            var getWorkCenterGroup = workCenterDetails[getTDVal].group;
                            
                            //var getCurrentTotal = workCenterDetails[getTDVal].labor;
                            var getCurrentTotal = workCenterGroupDetails[getWorkCenterGroup].labor;
                            
                            var preCalc = getCurrentTotal -= getLabor;
                            
                            preCalc = parseFloat(preCalc).toFixed(2);
                                                                
                            // Breakpoint has been set
                            //if(breakPointMet){
                            if(workCenterGroupDetails[getWorkCenterGroup].breakPoint){
                                
                                // Blur out the row.
                                $(this).closest('tr').attr('class', 'blur');
                            }
                            
                            // Breakpoint not met.
                            else {
                                
                                // As long as we still have a positive number, continue looping.
                                if(preCalc >= 0)
                                {
                                    /*
                                     * Subtract the build time from the current 
                                     * total avaialble labor hours for this
                                     * work center "group".
                                     */
                                    workCenterGroupDetails[getWorkCenterGroup].labor -= getLabor;
                                    workCenterGroupDetails[getWorkCenterGroup].labor = parseFloat(workCenterGroupDetails[getWorkCenterGroup].labor).toFixed(2);
                                    
                                    /*
                                     * Subtract the build time from the current 
                                     * total avaialble labor hours for this
                                     * work center.
                                     */
                                    workCenterDetails[getTDVal].labor -= getLabor;
                                    workCenterDetails[getTDVal].labor = parseFloat(workCenterDetails[getTDVal].labor).toFixed(2);

                                } 
                                
                                // Negative number encounter...BAIL!!
                                else {
                                    
                                    /*
                                    console.log('I broke @ ' + $(this).closest('tr').find('td:nth-child(1)').html());
                                    console.log('Prior: ' + workCenterDetails[getTDVal].labor);
                                    console.log('Pre-Calc: ' + preCalc);
                                    */

                                   // Hide our overlay
                                   $('.applyWorkCenterGroups').overlay({'show' : false});
                                   
                                    // We've hit our breakpoint
                                    //breakPointMet = true;
                                    workCenterGroupDetails[getWorkCenterGroup].breakPoint = true;
                                    
                                    // Assigne blue border break to this row.
                                    $(this).closest('tr').attr('class', 'blueBreak blur').css('border-color', tableColors[tableColorCounter]);
                                    
                                    
                                    tableColorDetails[getWorkCenterGroup] = {color : tableColors[tableColorCounter]};
                                    tableColorCounter++;
                                    

                                } // end if(preCalc >= 0)
                                
                            } // end if(breakPointMet)
                            
                        } // end if(wcMatch)
                        
                    }); // end $('table').find('tbody').find('tr').find(workCenterColumn).each();
                    
                } // end if(workCenterColumn)
                
            } // end if(currentReport)
            
            
            
            $.each(workCenterGroupDetails,function(key,value){
                
                var getTableColor = !(tableColorDetails[key] == undefined) ? tableColorDetails[key].color : 'transparent';
                
                var li_to_append = $('<li>').attr('group_id', key).html('<span class="colorCode" style="background:'+getTableColor+';"></span>Group <span class="bold underlined">' + key + '</span> : <span class="bold underlined">' + value.labor + '</span> avail. production hours.');
                
                $('.appliedWorkCenterGroupData').find('ul:nth-child(1)').append(li_to_append);
                                
            });

            
        }

        // No results
        else {

        }
        
        // Show overlay on table.
        $('.tableResultsContainer').overlay({'show' : false});
        
       // Hide our overlay
       $('.applyWorkCenterGroups').overlay({'show' : false});
        
   });   
   
   
   // Modify the message
   $('.applyWorkCenterGroups').find('p:nth-child(1)')
                              .find('span:nth-child(1)')
                              .attr('class', 'bold')
                              .html('Work center group(s) applied.');
                        
   // Modify the button text
    $('.applyWorkCenterGroups').find('button').html('Re-apply Work Center Groups');
    
});

//------------------------------------------------------------------------------





/******************************************************************************/

// Show the apply work center box if run is set
$('.applyWorkCenterGroups').css('display', runSet ? 'block' : 'none');

// Update the "RUN WORK ORDER REPORT" with parameters set in the URI
updateRunWorkOrderReportForm();

// Calculate the total backorder value.
calculateTotalBackOrderValue();


if($('.sectionHeader').attr('runReport') === ''){
    
    // Hide the code filter selector
    $('.reportFilters').find('label[for=selectReportFilters-code]').css('display', 'none');
    
    // Hide the <br/>
    $('.reportFilters').find('br:first').css('display', 'none');   
}


if($('.sectionHeader').attr('runReport') !== 'OR1000RG'){
    
    // Enable table sort order
    $("table").tablesorter();
    
    /*
    var getCurrentReport = $('.sectionHeader').attr('runReport');
    var bo_value_column  = 0;
    
    switch(getCurrentReport)
    {
        case 'WO1010RG':bo_value_column = '8';break;
        case 'WO1002RG':bo_value_column = '4';break;
    }
    
    $('table').find('tbody').find('tr').each(function(key,value){
        
        var goToColumn      = $(this).find('td:nth-child(' +  bo_value_column + ')');
        var getColumnValue  = goToColumn.html().replace(/\$/, '');
        var parseValue      = parseFloat(getColumnValue);
        
        if(parseValue > 0)
        {
            goToColumn.css({
                'color' : 'blue',
                'text-decoration' : 'underline'
            });
        }
        
        console.log(parseValue);
        
        //console.log('Key: ' + key + ' | ' + value);
        
    });
    */
    
} else {
    
    var getRunCode = ($('.sectionHeader').attr('runCode') !== '')
                   ? $('.sectionHeader').attr('runCode') 
                   : false;
          
    if(getRunCode)
    {   
       // Show loading overlay
       $('#or1000rgTable').overlay({'show' : true});
        
        $('table#or1000rgTable').find('tbody').find('tr.workOrderRow').each(function(key,value){
                if($(this).next().attr('class') === 'orderTotals'){
                    $(this).next().remove();
                    $(this).remove();   
                }   
        });
        
        $('#or1000rgTable').overlay({'show' : false});
    }    
}