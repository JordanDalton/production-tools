$.ajaxSetup({error:function(){$t.overlay({show:true});alert("Error: Query Timed Out")}});$("#dialog:ui-dialog").dialog("destroy");var getWindowHeight=$(window).height()*0.75;var getWindowWidth=$(window).width()*0.75;var workCenterList={};var availableWorkCenters=[];var unavailableWorkCenterGroups=[];$.fn.restForm=function(){$("#autoCompleteResults").find("li").remove();$(".errorMessages").css("display","none").find("li.error").remove();$(this)[0].reset()};function workCenter(c,b,a){return{work_center_id:c,work_center_name:b,work_center_use:a}}function setErrors(b,a){return{label:b,message:a}}$(".newEntry").attr("disabled",true).css("opacity",0.2);$.getJSON(siteURL+"ajax/get-work-center",function(a){if(a.data.length>=1){$.each(a.data,function(b,c){workCenterList[c.work_center_id]=workCenter(c.work_center_id,c.work_center_name,false)})}else{}$("#groupTable").updateTable()});function split(a){return a.split(/,\s*/)}function extractLast(a){return split(a).pop()}function updateAvailableWorkCenters(){availableWorkCenters=[];$.each(workCenterList,function(b,c){if(c.work_center_use==false){var a=b+" - "+c.work_center_name;availableWorkCenters.push({id:b,label:a})}});$("#tags").autocomplete("option",{source:availableWorkCenters})}$("#formDialog").dialog({autoOpen:false,height:400,modal:true,open:function(b,c){updateAvailableWorkCenters();var a=availableWorkCenters;$("#tags").bind("keydown",function(d){if(d.keyCode===$.ui.keyCode.TAB&&$(this).data("autocomplete").menu.active){d.preventDefault()}}).autocomplete({minLength:0,source:function(e,d){d($.ui.autocomplete.filter(a,extractLast(e.term)))},focus:function(){return false},select:function(h,j){var i=$("<li>").attr({wc_id:j.item.id}).html(j.item.value+'<a class="icon-close"></a>');var g="#workCenters";var d=$(g).val();var e=d.length;var f=(e==0)?(d+j.item.id):(d+","+j.item.id);$("#autoCompleteResults").append(i);$("#workCenters").val(f);this.value="";workCenterList[j.item.id].work_center_use=true;updateAvailableWorkCenters();return false}})},width:500});$("a.icon-close").live("click",function(b){b.preventDefault();var c=$("input#workCenters").val();var d=c.split(",");var a=$(this).closest("li").attr("wc_id");$.each(d,function(e,f){f=$.trim(f);if(f===a){d.splice(d.indexOf(f),1);workCenterList[f].work_center_use=false;updateAvailableWorkCenters()}});$("input#workCenters").attr({value:function(e,f){return d.join(",")}});$(this).closest("li").remove()});$(".newEntry, .icon-edit").live("click",function(){var d=$(this).attr("class");var a=(d==="newEntry")?0:1;$("#formDialog").dialog("open").attr("isUpdate",a);$("#groupID").attr("disabled",false);switch(a){case 0:$("#formDialog").dialog({buttons:{"Add Work Center Group":function(j){j.preventDefault();var h=$("#groupID").val();var g=$("#description").val();var f=$("#workCenters").val();f.replace(/(,\s*)/gm,",");var i=$("#staffing").val();var l="group-id/"+h+"/description/"+g+"/work-center/"+f+"/staffing/"+i;var k=checkFormDialogErrors();if(!k){$.getJSON(siteURL+"ajax/put-work-center-group/"+l,function(m){$("#formDialog").dialog("close");$("#groupTable").updateTable()})}},Cancel:function(){$(this).dialog("close");$("#formDialog").find("form").restForm()}}});break;case 1:var c=$(this).closest("tr");var b=c.attr("workCenters");var e=b.split(",");$("#groupID").attr("disabled",true).val(c.attr("group"));$("#description").val(c.attr("description"));$("#workCenters").val(c.attr("workCenters"));$("#staffing").val(parseInt(c.attr("staffing")));$.each(e,function(g,i){i=$.trim(i);var f=workCenterList[i].work_center_id+" - "+workCenterList[i].work_center_name;var h=$("<li>").attr({wc_id:workCenterList[i].work_center_id}).html(f+'<a class="icon-close"></a>');$("#autoCompleteResults").append(h)});$("#formDialog").dialog({buttons:{"Update Work Center Group":function(j){j.preventDefault();var h=$("#groupID").val();var g=$("#description").val();var f=$.trim($("#workCenters").val().toString());var l=f.replace(/(,\s*)/,",","gm");var i=$("#staffing").val();var m="group-id/"+h+"/description/"+g+"/work-center/"+l+"/staffing/"+i;
var k=checkFormDialogUpdateErrors();if(!k){$.getJSON(siteURL+"ajax/put-work-center-group/"+m,function(n){$("#formDialog").dialog("close");$("#groupTable").updateTable()})}},Cancel:function(){$(this).dialog("close");$("#formDialog").find("form").restForm()}}});break}return false});function checkFormDialogErrors(){var e={};var d=false;var a=$("#groupID").val().toString();var c=$.trim($("#description").val().toString());var b=$.trim($("#workCenters").val().toString());var f=$.trim($("#staffing").val().toString());$(".errorMessages").find("li.error").remove();if(parseInt(unavailableWorkCenterGroups.indexOf(a))==0){d=true;e["Group ID"]=setErrors("Group ID","The group id you entered is already being used.")}if(a.length<1){d=true;e["Group ID"]=setErrors("Group ID","Please entere a group id.")}if(c.length<1){d=true;e.Description=setErrors("Description","Please provide a description.")}if(b.length<1){d=true;e["Work Centers"]=setErrors("Work Centers","Please select one or more work centers")}if(!f.match(/^\d+$/)){d=true;e.Staffing=setErrors("Staffing","Please enter number of staff.")}if(d){$("#formDialog").find(".errorMessages").css("display","block");$.each(e,function(g,i){var h=$("<li>").attr("class","error").html("<span>"+g+":</span> "+i.message);$("#formDialog").find("ul.errorMessages").append(h)})}else{e=[];$("#formDialog").find(".errorMessages").css("display","none");$(".ui-dialog-buttonset").find("button:first").attr("disabled",false).css("opacity","1")}return d}function checkFormDialogUpdateErrors(){var e={};var d=false;var a=$("#groupID").val().toString();var c=$.trim($("#description").val().toString());var b=$.trim($("#workCenters").val().toString());var f=$.trim($("#staffing").val().toString());$(".errorMessages").find("li.error").remove();if(a.length<1){d=true;e["Group ID"]=setErrors("Group ID","Please entere a group id.")}if(c.length<1){d=true;e.Description=setErrors("Description","Please provide a description.")}if(b.length<1){d=true;e["Work Centers"]=setErrors("Work Centers","Please select one or more work centers")}if(!f.match(/^\d+$/)){d=true;e.Staffing=setErrors("Staffing","Please enter number of staff.")}if(d){$("#formDialog").find(".errorMessages").css("display","block");$.each(e,function(g,i){var h=$("<li>").attr("class","error").html("<span>"+g+":</span> "+i.message);$("#formDialog").find("ul.errorMessages").append(h)})}else{e=[];$("#formDialog").find(".errorMessages").css("display","none");$(".ui-dialog-buttonset").find("button:first").attr("disabled",false).css("opacity","1")}return d}$.fn.updateTable=function(){$t=$(this);$t.overlay({show:true});$t.find("tbody").find("tr").remove();$("#formDialog").find("form").restForm();$.getJSON(siteURL+"ajax/get-work-center-group",function(a){var d=a.data?a.data.length:0;if(d>=1){var c=[];$.each(a.data,function(n,m){workCenterList[m.cwwcid].work_center_use=true;if(parseInt(c.indexOf(m.wcg_id))<0){c.push(m.wcg_id);unavailableWorkCenterGroups.push(m.wcg_id);var g='<a href="#" class="icon-edit"></a><a href="#" class="icon-delete"></a>';var k=(((60*8)-20)*0.9)/60;var i=parseInt(m.wcg_staffing);var l=parseFloat(m.wcg_staffing*k).toFixed(2);$t.find("tbody").append("<tr><td>"+m.wcg_id+"</td><td>"+m.cwwcid+"</td><td>"+m.wcg_name+'</td><td class="staffingCap">'+i+'</td><td class="laborCap">'+l+"</td><td>"+g+"</td></tr>");$t.find("tbody").find("tr:last").attr({group:m.wcg_id,workCenters:m.cwwcid,description:m.wcg_name,staffing:m.wcg_staffing})}else{var j=$t.find("tbody").find('tr[group="'+m.wcg_id+'"]');var f=j.find("td:nth-child(2)").text();var h=f+", "+m.cwwcid;j.attr("workCenters",h).find("td:nth-child(2)").text(h)}});var b=0;$("table").find("tbody").find(".staffingCap").each(function(){var f=parseFloat($(this).html());b+=f});var e=0;$("table").find("tbody").find(".laborCap").each(function(){var f=parseFloat($(this).html());e+=f});$(".totalStaffing").html(b);$(".totalLaborCapacity").html(e)}else{$(".totalStaffing").html(0);$(".totalLaborCapacity").html(0);$t.find("tbody").append('<tr><td colspan="6">There are no work center groups in use.</td></tr>')
}$(".newEntry").attr("disabled",false).css("opacity",1);$t.overlay({show:false})})};$(".icon-delete").live("click",function(c){c.preventDefault();var a=$(this).closest("tr").attr("group");var b=$(this).closest("tr").attr("workCenters");$.getJSON(siteURL+"ajax/delete-work-center-group/group-id/"+a,function(d){if(d.status==="failed"){alert(d.message)}else{var e=b.split(",");$.each(e,function(f,g){g=$.trim(g);workCenterList[g].work_center_use=false});unavailableWorkCenterGroups.splice(unavailableWorkCenterGroups.indexOf(a),1);$("#groupTable").updateTable()}})});