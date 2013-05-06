<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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

/**
 * @package   chairman
 * @copyright 2011 Raymond Wainman, Dustin Durand, Patrick Thibaudeau (Campus St. Jean, University of Alberta)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once('lib.php');
require_once('lib_chairman.php');
echo '<link rel="stylesheet" type="text/css" href="style.php">';
require_once("$CFG->dirroot/mod/chairman/meetingagenda/ajax_lib.php");

print '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/mod/chairman/meetingagenda/rooms_available.css" />';
print '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/mod/chairman/meetingagenda/fancybox/jquery.fancybox-1.3.1.css" />';

print '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/chairman/meetingagenda/fancybox/jquery.min.js"></script>';
print '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/chairman/meetingagenda/fancybox/jquery.fancybox-1.3.1.pack.js"></script>';
print '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/chairman/meetingagenda/fancybox/roomscheduler.js"></script>';
print '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/chairman/meetingagenda/fancybox/rooms_avaliable_event.js"></script>';


require_once($CFG->dirroot.'/mod/chairman/meetingagenda/rooms_avaliable_form.php');
print '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/chairman/meetingagenda/rooms_available.js"></script>';


$id = required_param('id',PARAM_INT);    // Course Module ID

chairman_check($id);
chairman_header($id,'addevent','add_event.php?id='.$id);

if(chairman_isadmin($id)) {

    echo '<div><div class="title">'.get_string('addevent', 'chairman').'</div>';

    echo '<form action="'.$CFG->wwwroot.'/mod/chairman/add_event_script.php?id='.$id.'" method="POST" name="newevent">';
    echo '<table width=100% border=0>';
    echo '<tr>';
    echo '<td valign="top">'.get_string('timezone_used','mod_chairman').'</td>';
    //Timezone
    require_once($CFG->dirroot.'/calendar/lib.php');
    $timezones = get_list_of_timezones();
    
    //get user timezone
    if (!$USER->timezone == '99'){
        $current = $USER->timezone;
    } else {
        $current = $CFG->timezone;
    }
    echo '<td>'.html_writer::select($timezones, "timezone", $current, array('99'=>get_string("serverlocaltime"))).'</td>';
    echo '</tr>';
    
    $now = time();

    $day = date('j',$now);
    echo '<tr><td>'.get_string('date', 'chairman').' : </td>';
    echo '<td width=85%><select name="day">';
    for($i=1; $i<=31; $i++) {
        echo '<option value="'.$i.'" ';
        if($day==$i){echo 'SELECTED';}
        echo '>'.$i.'</option>';
    }
    echo '</select>';

    $month = date('m',$now);

    echo '<select name="month">';
    echo '<option value="01" ';
    if($month=='01'){echo 'SELECTED ';}
    echo '>'.get_string('january', 'chairman').'</option>';
    echo '<option value="02" ';
    if($month=='02'){echo 'SELECTED ';}
    echo '>'.get_string('february', 'chairman').'</option>';
    echo '<option value="03" ';
    if($month=='03'){echo 'SELECTED ';}
    echo '>'.get_string('march', 'chairman').'</option>';
    echo '<option value="04" ';
    if($month=='04'){echo 'SELECTED ';}
    echo '>'.get_string('april', 'chairman').'</option>';
    echo '<option value="05" ';
    if($month=='05'){echo 'SELECTED ';}
    echo '>'.get_string('may', 'chairman').'</option>';
    echo '<option value="06" ';
    if($month=='06'){echo 'SELECTED ';}
    echo '>'.get_string('june', 'chairman').'</option>';
    echo '<option value="07" ';
    if($month=='07'){echo 'SELECTED ';}
    echo '>'.get_string('july', 'chairman').'</option>';
    echo '<option value="08" ';
    if($month=='08'){echo 'SELECTED ';}
    echo '>'.get_string('august', 'chairman').'</option>';
    echo '<option value="09" ';
    if($month=='09'){echo 'SELECTED ';}
    echo '>'.get_string('september', 'chairman').'</option>';
    echo '<option value="10" ';
    if($month=='10'){echo 'SELECTED ';}
    echo '>'.get_string('october', 'chairman').'</option>';
    echo '<option value="11" ';
    if($month=='11'){echo 'SELECTED ';}
    echo '>'.get_string('november', 'chairman').'</option>';
    echo '<option value="12" ';
    if($month=='12'){echo 'SELECTED ';}
    echo '>'.get_string('december', 'chairman').'</option>';
    echo '</select>';

    echo '<select name="year">';
    $year = date('Y');
    echo '<option value="'.($year-5).'">'.($year-5).'</option>';
    echo '<option value="'.($year-4).'">'.($year-4).'</option>';
    echo '<option value="'.($year-3).'">'.($year-3).'</option>';
    echo '<option value="'.($year-2).'">'.($year-2).'</option>';
    echo '<option value="'.($year-1).'">'.($year-1).'</option>';
    echo '<option value="'.$year.'" selected="selected" >'.$year.'</option>';
    echo '<option value="'.($year+1).'">'.($year+1).'</option>';
    echo '<option value="'.($year+2).'">'.($year+2).'</option>';
    echo '<option value="'.($year+3).'">'.($year+3).'</option>';
    echo '<option value="'.($year+4).'">'.($year+4).'</option>';
    echo '<option value="'.($year+5).'">'.($year+5).'</option>';
    echo '</select>';

    echo '</td></tr>';

    //Start time

    $hour = date('G',$now);

    echo '<tr><td>'.get_string('starttime', 'chairman').' : </td>';
    echo '<td><select name="starthour">';
    for($i=0;$i<24;$i++) {
        if($i<10) {
            echo '<option value="0'.$i.'" ';
            if($i==$hour){echo 'SELECTED';}
            echo '>0'.$i.'</option>';
        }
        else {
            echo '<option value="'.$i.'" ';
            if($i==$hour){echo 'SELECTED';}
            echo '>'.$i.'</option>';
        }
    }
    echo '</select> : ';

    $minute = date('i',$now);

    echo '<select name="startminutes">';
    for($i=0;$i<60;$i++) {
        if($i<10) {
            echo '<option value="0'.$i.'" ';
            if($i=='0'.$minute){echo 'SELECTED';}
            echo '>0'.$i.'</option>';
        }
        else {
            echo '<option value="'.$i.'" ';
            if($i==$minute){echo 'SELECTED';}
            echo '>'.$i.'</option>';
        }
    }
    echo '</select>';

    //End time

    echo '<tr><td>'.get_string('endtime', 'chairman').' : </td>';
    echo '<td><select name="endhour">';
    for($i=0;$i<24;$i++) {
        if($i<10) {
            echo '<option value="0'.$i.'" ';
            if($i==$hour){echo 'SELECTED';}
            echo '>0'.$i.'</option>';
        }
        else {
            echo '<option value="'.$i.'" ';
            if($i==$hour+1){echo 'SELECTED';}
            echo '>'.$i.'</option>';
        }
    }
    echo '</select> : ';

    echo '<select name="endminutes">';
    for($i=0;$i<60;$i++) {
        if($i<10) {
            echo '<option value="0'.$i.'" ';
            if($i=='0'.$minute){echo 'SELECTED';}
            echo '>0'.$i.'</option>';
        }
        else {
            echo '<option value="'.$i.'" ';
            if($i==$minute){echo 'SELECTED';}
            echo '>'.$i.'</option>';
        }
    }
    echo '</select>';
    echo '</td></tr>';

//----CHECK FOR SCHEDULER PLUGIN -----------------------------------------------
 $dbman = $DB->get_manager();
$table = new xmldb_table('roomscheduler_reservations');
$scheduler_plugin_installed = $dbman->table_exists($table);

$cm = get_coursemodule_from_id('chairman', $id);
$context = get_context_instance(CONTEXT_COURSE, $cm->course);

if ($scheduler_plugin_installed && has_capability('block/roomscheduler:reserve', $context)) {   //plugin exists
global $DB;

$cm = get_coursemodule_from_id('chairman', $id);
$chairman = $DB->get_record("chairman", array("id"=>$cm->instance));

echo '<tr><td>';
echo get_string('bookroom','chairman')." ";
echo '</td><td>';
echo '<a id="avaliable_rooms_link" href="#apptForm_2" onclick="initalize_popup_newEvent(\''.rooms_avaliable_form::apptForm_formName().'\',\''.$chairman->course.'\',\''.$chairman->name.'\');get_avaliable_rooms(\''.rooms_avaliable_form::apptForm_formName().'\')">Book Room</a>';
echo '<div id="booked_location"></div>';
echo '</td></tr>';


echo '<input type="hidden" name="room_reservation_id" value="0"/>';
}


    //Summary
    echo '<tr><td>'.get_string('summary', 'chairman').' : </td>';
    echo '<td><input type="text" name="summary" style="width:500px;"></td>';

    //Detailed description
    echo '<tr><td valign="top">'.get_string('description', 'chairman').' : </td>';
    echo '<td><textarea rows="4" cols="70" name="description">';
    echo '</textarea></td></tr>';
    
    //Notifications
    
    echo '<tr><td valign="top" colspan="2">'.get_string('sendnotificationweek', 'chairman').' : ';
    echo '<input type="checkbox" name="notify_week" value="1">';
    echo '</td></tr>';
    echo '<tr><td valign="top" colspan="2">'.get_string('sendnotification', 'chairman').' : ';
    echo '<input type="checkbox" name="notify" value="1">';
    echo '</td></tr>';
    
    //Buttons

    echo '<tr><td><br/></td><td></td></tr>';
    echo '<tr>';
    echo '<td></td><td><input type="submit" value="'.get_string('addevent', 'chairman').'">';
    echo '<input type="button" value="'.get_string('cancel', 'chairman').'" onClick="parent.location=\''.$CFG->wwwroot.'/mod/chairman/events.php?id='.$id.'\'"></td>';
    echo '</tr>';
    echo '</table>';
    echo '</form>';

    echo '<span class="content">';

    echo '</span></div>';

    $dbman = $DB->get_manager();
$table = new xmldb_table('roomscheduler_reservations');
$scheduler_plugin_installed = $dbman->table_exists($table);

$cm = get_coursemodule_from_id('chairman', $id);
$context = get_context_instance(CONTEXT_COURSE, $cm->course);

if ($scheduler_plugin_installed && has_capability('block/roomscheduler:reserve', $context)) {   //plugin exists
global $DB;

$cm = get_coursemodule_from_id('chairman', $id);
$chairman = $DB->get_record("chairman", array("id"=>$cm->instance));

$scheduler_form = new rooms_avaliable_form();
echo $scheduler_form;
$scheduler_form->initalize_popup_newEvent($chairman->course, $chairman->name);

echo '<input type="hidden" name="room_reservation_id" value="0"/>';
}


}

chairman_footer();

?>