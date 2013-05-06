<?php

/**
**************************************************************************
**                                Chairman                              **
**************************************************************************
* @package mod                                                          **
* @subpackage chairman                                                  **
* @name Chairman                                                        **
* @copyright oohoo.biz                                                  **
* @link http://oohoo.biz                                                **
* @author Raymond Wainman                                               **
* @author Patrick Thibaudeau                                            **
* @author Dustin Durand                                                 **
* @license                                                              **
http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later                **
**************************************************************************
**************************************************************************/

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once('../lib_chairman.php');

$event_id = optional_param('event_id', 0, PARAM_INT); // event ID, or

global $DB,$PAGE,$USER,$CFG;

$agenda = null;

if ($event_id) {
	$agenda  = $DB->get_record('chairman_agenda', array('chairman_events_id' => $event_id), '*', $ignoremultiple=false);
	if($agenda){
	$chairman_id = $agenda->chairman_id;
	$event_id = $agenda->chairman_events_id;
	$agenda_id =$agenda->id;
	//$DB->delete_records('chairman_agenda', array('chairman_events_id' => $event_id));
       //exit();
	} else {
          print_error('Unable to Delete');
        }
} else {
print_error('Unable to Delete');
}

chairman_check($chairman_id);
$cm = get_coursemodule_from_id('chairman', $chairman_id); //get course module

//Get Credentials for this user
if ($current_user_record = $DB->get_record("chairman_members", array("chairman_id"=>$chairman_id,"user_id"=>$USER->id))){
$user_role = $current_user_record->role_id;
}


//Simple cypher for code clarity
$role_cypher = array('1' => 'president', '2' => 'vice', '3' => "member", "4" => 'admin');

//check if user has a valid user role, otherwise give them the credentials of a guest
if (isset($user_role) && ($user_role == '1' || $user_role == '2' || $user_role == '3' || $user_role == '4')) {
    $credentials = $role_cypher[$user_role];
} else {
    $credentials = "guest";
}

if ($credentials == 'president' || $credentials == 'vice' || $credentials == 'admin') {

//Delete all files within the instace of this module for agenda
$fs = get_file_storage();
$files = $fs->get_area_files($cm->instance, 'mod_chairman', 'attachment' );
foreach ($files as $f) {
    // $f is an instance of stored_file
$f->delete();
}


$DB->delete_records('chairman_agenda_topics', array('chairman_agenda'=>$agenda_id));
$DB->delete_records('chairman_agenda_guests', array('chairman_agenda'=>$agenda_id));
$DB->delete_records('chairman_agenda_motions', array('chairman_agenda'=>$agenda_id));
$DB->delete_records('chairman_agenda_attendance', array('chairman_agenda'=>$agenda_id));
$DB->delete_records('chairman_agenda_members', array('agenda_id'=>$agenda_id));
$DB->delete_records('chairman_agenda', array('id'=>$agenda_id));

redirect($CFG->wwwroot."/mod/chairman/view.php?id=".$chairman_id);

} else {
print_error("Access Restriction");
}


?>
