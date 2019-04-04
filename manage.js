(function(){

})(jQuery);
var globalGroupName;
function getUserInGroup(groupName){
	globalGroupName = groupName;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	    if (this.readyState == 4 && this.status == 200) {
	    	var obj = JSON.parse(xhttp.responseText);
	    	jQuery('#groupNameTitle').text(groupName);
	    	try{
	    		jQuery("#userGroup").html('');
		    	if(obj.users.length > 0){
			    	for(var i=0; i<obj.users.length; i++){
			    		var d = new Date(obj.users[i].UserCreateDate);
			    		var month = d.getMonth()+parseInt(1);
			    		UserCreateDate = d.getDate()+'-'+month+'-'+d.getFullYear();
			    		jQuery('#out'+obj.users[i].Username).css('display','none');
			    		jQuery('#'+obj.users[i].Username).attr('alt','groupName');
			    		jQuery("#userGroup").append('<tr id="in'+obj.users[i].Username+'"><td>'+obj.users[i].Username+'</td><td>'+obj.users[i].email+'</td><td>'+obj.users[i].UserCreateDate+'</td><td>'+obj.users[i].UserStatus+'</td><td><a href="javascript:;" onclick="removeUserFromGroup(`'+obj.users[i].Username+'`)"><i class="fa fa-times-circle"></i></a></td></tr>');
			    	}
			    }
			}catch(err){

			}
	    }
	};
	xhttp.open("GET", "/cognito-login/get-user-in-group?gp="+groupName, true);
	xhttp.send();
}

function addUserInGroup(userName){
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	    if (this.readyState == 4 && this.status == 200) {
	    	var obj = JSON.parse(xhttp.responseText);
	    		jQuery('#'+userName).remove();	
	    		getUserInGroup(globalGroupName);
	    }
	};
	xhttp.open("GET", "/cognito-login/add-user-in-group?gp="+globalGroupName+"&uid="+userName, true);
	xhttp.send();
}

function deleteGroup(groupName){
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	    if (this.readyState == 4 && this.status == 200) {
	    	var obj = JSON.parse(xhttp.responseText);
	    		jQuery('#group-'+groupName).remove();	
	    }
	};
	xhttp.open("GET", "/cognito-login/delete-group?gp="+groupName, true);
	xhttp.send();
}

function removeUserFromGroup(userName){
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	    if (this.readyState == 4 && this.status == 200) {
	    	var obj = JSON.parse(xhttp.responseText);
	    		jQuery('#out'+userName).css('display','');
	    		jQuery('#in'+userName).remove();	
	    		
	    }
	};
	xhttp.open("GET", "/cognito-login/remove-user-from-group?gp="+globalGroupName+"&uid="+userName, true);
	xhttp.send();
}

