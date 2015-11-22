<?php

/**
 * Name: Migrate users in bulk as admin
 * Description: Export a user and all their channels from one hub to another
 * Version: 0.6
 * Author: ken restivo <ken@restivo.org>
 */

define ('MIGRATOR_VERSION', 6);

require_once('include/api_auth.php');
require_once('migrator_export.php');
require_once('migrator_import.php');


function migrator_load(){
	register_hook('construct_page', 'addon/migrator/migrator.php', 'migrator_all_pages');
}

function migrator_unload(){
	unregister_hook('construct_page', 'addon/migrator/migrator.php', 'migrator_all_pages');
}


function migrator_settings(&$a,&$s) {
}

function migrator_settings_post($a,&$post) {
}



function migrator_content(&$a){
	// It's not an API call. Check login.
	if(! is_site_admin()){
		json_error_die(401, 'Unauthorized', 
			       'Only admin accounts may use this endpoint.');
	}

	return("Migrator version " . MIGRATOR_VERSION);
}



function migrator_all_pages(&$a, &$b){
	// TODO: settings and insructions and such
}

function migrator_init(&$a) {

	$x = argc(); 
	if($x > 1){
		api_login($a);
		switch(argv(1)){
		case "version":
			json_return_and_die(array("status" => "OK",
						  'platform' => PLATFORM_NAME,
						  'platform_version' => RED_VERSION,
						  'zot_version' => ZOT_REVISION,
						  'db_version' => DB_UPDATE_VERSION,
						  'migrator_version' => MIGRATOR_VERSION));
			break;
		case "import":
			if(PLATFORM_NAME == "redmatrix"){
				json_error_die(400, 'Bad Request', 
					       'Cannot import to Redmatrix, only to Hubzilla.');
			}
			switch(argv(2)){
			case 'account':
				migrator_import_account($a);
				break;
			case 'identity':
				migrator_import_identity($a, argv(3));
				break;
			case 'items':
				migrator_import_items($a, argv(3));
				break;
			case 'directory':
				migrator_update_directory($a, argv(3));
				break;
			default:
				json_error_die(404, 'Not Found',
					       'No such endpoint');
				break;
			}
		case "export":
			switch(argv(2)){
			case "users":
				export_users($a);
				break;
			case "channel_hashes":
				export_channel_hashes($a, argv(3));
				break;
			case "identity":
				export_identity($a, argv(3));
				break;
			case "first_post":
				first_post($a, argv(3));
				break;
			case "items":
				export_items($a, argv(3), argv(4), argv(5));
				break;
			default:
				json_error_die(404, 'Not Found',
					       'No such endpoint');
				break;
			}
			break;
		default:
			json_error_die(404, 'Not Found',
				       'No such endpoint');
			break;
		}

	} 
}

function migrator_module() { return; }
