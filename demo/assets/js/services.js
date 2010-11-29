$(document).ready(function(){
	initServices();
});

/**
 * initialises the services
 */
function initServices(){
	
	// This data object is the instruction between your Drupal Services Server
	// // Example of how to implement this is in the next code box below

	var comment_object = {
	    "nid": 123 // Required, set appropriately
	};
	
	var data = { 
	  "method": "node.get", 
	  "node": comment_object
	};
	

	JSONRequest.get({
		url: "http://staging.dashboard.proximity.bbdo.be/services/json-rpc/node.get/callback=?", 
		onSuccess: function(response){
			var myDataArray = response['#data'];
			// Manipulate data as necessary
			}
	}).send({ data: data });

}

function bla(){
	alert("ok");
}