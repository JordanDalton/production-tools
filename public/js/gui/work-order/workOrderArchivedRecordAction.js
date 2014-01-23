var getWorkCenterGroupsJSON;
    getWorkCenterGroupsJSON = $('.workCenterGroupsJSON').html();
    getWorkCenterGroupsJSON = (getWorkCenterGroupsJSON.length > 1) ? $.parseJSON(getWorkCenterGroupsJSON) : '';
    
workCenterGroupJSON = false;

//------------------------------------------------------------------------------

/**
 * Apply the work center groups data from that particular day.
 */
$('.applyHistoricWorkCenterGroups').live('click', function(event){
    
    // Prevent default click event.
    event.preventDefault();
    
   var  tableColors = ['blue', 'red', 'green', 'purple', 'orange'];
   var  tableColorDetails = {};
   var  tableColorCounter = 0;
    
   var getCurrentReport               = $('.sectionHeader').attr('runReport');
   var getApplicableWorkCenters       = $('.sectionHeader').attr('runWorkCenters');
   var getApplicableWorkCentersLength = parseInt(getApplicableWorkCenters);
   
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
   
   // Get the work center group JSON data
   workCenterGroupJSON = getWorkCenterGroupsJSON;

    // Results exits
    if(workCenterGroupJSON.data && workCenterGroupJSON.data.length >= 1)
    {
        console.log('i have length');
        
        // Loop through the results.
        $.each(workCenterGroupJSON.data, function(key,value)
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
                workCenterGroupDetails[setGroup] = {labor : setLabor};
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

                            $('.remainingProductionHoursMessage').css('display', 'none');

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

                                // We've hit our breakpoint
                                //breakPointMet = true;
                                workCenterGroupDetails[getWorkCenterGroup].breakPoint = true;

                                // Assigne blue border break to this row.
                                $(this).closest('tr').attr('class', 'blueBreak blur').css('border-color', tableColors[tableColorCounter]);

                                tableColorDetails[getWorkCenterGroup] = { color : tableColors[tableColorCounter]};
                                tableColorCounter++;

                            } // end if(preCalc >= 0)

                        } // end if(breakPointMet)

                    } // end if(wcMatch)

                }); // end $('table').find('tbody').find('tr').find(workCenterColumn).each();

            } // end if(workCenterColumn)

        } // end if(currentReport)
        
            var showWorkCenterGroupData = false;
        
            $.each(workCenterGroupDetails,function(key,value){
                
                var getTableColor = !(tableColorDetails[key] == undefined) ? tableColorDetails[key].color : 'transparent';
                
                if(getTableColor) showWorkCenterGroupData = true;
                
                var li_to_append = $('<li>').attr('group_id', key).html('<span style="background:'+getTableColor+';float:left;height:15px;width:20px;margin-right:5px"></span>Group <span class="bold underlined">' + key + '</span> : <span class="bold underlined">' + value.labor + '</span> avail. production hours.');
                
                $('.appliedWorkCenterGroupData').find('ul:nth-child(1)').append(li_to_append);
                                
            });
            
            $('.appliedWorkCenterGroupData').css('display', showWorkCenterGroupData ? 'block' : 'none')
        
        
    }
        
});




$('.applyHistoricWorkCenterGroups').click();