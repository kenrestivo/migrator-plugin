<?php




function get_channels(&$a, $type) {
		json_return_and_die(post_activity_item($_REQUEST));


}




function migrateusers_load() {
// register hooks
	
	// get all channels
       
	

}

function migrateusers_unload() {
	// unregister hooks

}


// allow getting a channel by proxy, this might be a tweeze/hook to the existing mod/export not an endpoint
