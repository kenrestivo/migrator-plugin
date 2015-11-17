<?php

require_once('include/identity.php');
require_once('include/items.php');
require_once('migrator_utils.php');



function export_users(&$a) {

	$r = q("SELECT *  FROM account where account_flags = %d and ((account_roles & %d) < 1)",
	       intval(ACCOUNT_OK),
	       intval(ACCOUNT_ROLE_SYSTEM));

	$ret = array();
	foreach($r as $u){
		$ret[] = $u;
	}

	json_return_and_die(array('status' => 'OK',
				  'users' => $ret));
}




function export_channel_hashes(&$a, $account_id) {


	if( $account_id == '' ){
		json_error_die('422 Unprocessable Entity',
			       'Must supply account_id parameter.');

	}

	if(! validatesAsInt($account_id)){
		json_error_die('422 Unprocessable Entity',
			       "That's not a number: ". $account_id);
	}

	$c = q("select `channel_hash`, `channel_id` from `channel` where `channel_account_id` = %d",
	       intval($account_id));

	if(count($c) < 1){
		json_error_die('404 Not Found',
			       'No channels for '. $account_id);
	}			

	$ret = array();
	foreach ($c as $r){
		$ret[] = $r;
	}
	json_return_and_die(array('status' => 'OK',
				  'channel_hashes' => $ret));
}



function export_items(&$a, $channel_hash, $year, $month){

	if(validatesAsInt($year) && validatesAsInt($month)){
	} else {
		json_error_die('422 Unprocessable Entity',
			       'Month and year must be numbers'. $month . " " . $year);
	}

	if(($month < 1) || ($month > 12)){
		json_error_die('422 Unprocessable Entity',
			       'Invalid month'. $month);
	}
	
	if(($year < 1) || ($year > date('Y'))){
		json_error_die('422 Unprocessable Entity',
			       'Are you from the future? Invalid year '. $year);
	}

	$items = identity_export_year(get_channel_id($channel_hash), $year, $month);
	if(count($items) < 1){
		json_error_die('404 Not Found',
			       "No posts for $channel_hash on $year-$month");
	}
	json_return_and_die($items);
}


function first_post(&$a, $channel_hash){
	if( $channel_hash == ''){
		json_error_die('422 Unprocessable Entity',
			       'Must supply channel_hash parameter.');

	}
	$first = first_post_date(get_channel_id($channel_hash));
	
	if(! $first){
		json_error_die('404 Not Found',
			       "No posts for " . $channel_hash);
	}
	json_return_and_die(array("status" => "OK",
				  "date" => $first));
}


function export_identity(&$a, $channel_hash) {

	if( $channel_hash == ''){
		json_error_die('422 Unprocessable Entity',
			       'Must supply channel_hash parameter.');

	}
		
	json_return_and_die(
		identity_basic_export(get_channel_id($channel_hash),
				      (($_REQUEST['posts']) ? intval($_REQUEST['posts']) : 0 )));
}
