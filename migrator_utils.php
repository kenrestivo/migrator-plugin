<?php

require_once('include/network.php');

/// via https://stackoverflow.com/questions/2012187/how-to-check-that-a-string-is-an-int-but-not-a-double-etc
function validatesAsInt($number)
{
	$number = filter_var($number, FILTER_VALIDATE_INT);
	return ($number !== FALSE);
}


function json_error_die($num, $desc, $message){
	http_status($num, $desc);
	json_return_and_die(array("status" => "Error",
				  "message" => $message));


function get_channel_id($channel_hash){
	$c = q("select channel_id from channel where channel_hash = '%s' LIMIT 1",
	       dbesc($channel_hash));
				
	if(! $c){
		json_error_die(404, 'Not Found',
			       'No such channel '. $channel_hash);
	}	
	return $c[0]['channel_id'];
}


function get_account_by_email($email){
	$c = q("select account_id from account where account_email = '%s' LIMIT 1",
	       dbesc($email));
	if(count($c) > 0){
		return $c[0]['account_id'];
	}
}

}
