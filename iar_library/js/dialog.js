
function messageDlg($title) {
	
	$('<Label>'+$title+'</label>').dialog({
        modal: true,
        title: $title,
        buttons: {
            
        	'OK': function () {
        		$(this).dialog('close');
                	window.location.href = "home.php";
        	}
         },
    });
}

function getId(title,dest){
	$.when(getItemId(title,dest)).then(
           )
}
function getItemId($title,  $dest) {
	/*var rules = {
	         txtName: {
	             required: true
	         }
	     };
	     var messages = {
	         txtName: {
	             required: "Please enter name"
	         }
	     };*/
	$('<form ><input  type="number" min=1  id="id"></form>').dialog({
        modal: true,
        title: $title,
        buttons: {
            
        	'OK': function () {
        		
               var name = $('input[id="id"]').val();
                if(name>0){
                	$(this).dialog('close');
                	window.location.href = $dest+name;
                }
                else
                	return false;
            },
                'Cancel': function () {
                	
                $(this).dialog('close');
                
            }
        },
       
    	
    });
	$('form').submit(function(evt) {
		evt.preventDefault();
	 });
	/*$("#itemForm").validate({
        rules: rules,
        messages: messages
    });*/
	
}

function callback(dest,value,arr,memid){
	$.post(dest, {
		query : value,
		memid:memid,
	}, function(data, status) {
		if (status == "success") {
			//alert(data);			
		    console.log("success");
		    window.location.href = "member_info.php?id="+memid;
		    var len=arr.length;
		    var tbl = document.getElementById("LateFees");
		    if(tble.rows.length>len+1){
			console.log("RemoveRows");
			for(i=len-1;i>=0;i--){
			    document.getElementById("LateFees").deleteRow(arr[i]);

			}
		    }
		    else
			window.location.href = "member_info.php?id="+memid;
		}
	});
}
function confirm($title,dest,query,arr,memid) {
    // Dialog here
    $('<Label>'+$title+'</label>').dialog({
        modal: true,
        title: "Please Confirm",
        buttons: {
            'OK': function () {
                $(this).dialog('close');
               callback(dest,query,arr,memid)
            
            },
            'Cancel': function () {
                $(this).dialog('close');
            }
        }
    });
  
}
function message($title) {
    // Dialog here
    $('<Label>'+$title+'</label>').dialog({
        modal: true,
        title: "Note",
        buttons: {
            'OK': function () {
                $(this).dialog('close');
            }
        }
    });
  
}

