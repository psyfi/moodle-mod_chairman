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
 * The form for the "Motions By Year" tab for the Agenda/Meeting Extension to Committee Module.
 *
 *          **List View
 *
 *
 * @package   Agenda/Meeting Extension to Committee Module
 * @copyright 2011 Dustin Durand
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
require_once("$CFG->dirroot/mod/chairman/chairman_meetingagenda/util/moodle_user_selector.php");
require_once("$CFG->dirroot/mod/chairman/chairman_meetingagenda/lib.php");
require_once("$CFG->dirroot/mod/chairman/chairman_meetingagenda/agenda/css/agenda_link.css");

class mod_agenda_motions_by_year_list_form extends moodleform {

    private $instance;
    private $event_id;
    private $agenda_id;
    private $chairman_id;
    private $default_toform;

    private $topicNames; //Used by menu to determine which html anchor points are to what name

function __construct($event_id, $agenda_id, $chairman_id, $cm) {
$this->event_id = $event_id;
$this->agenda_id = $agenda_id;
$this->chairman_id = $chairman_id;
$this->instance = $cm;
parent::__construct();
    }

function definition() {

global $DB,$CFG;

$mform = & $this->_form;

//object to contain default values
$toform = new stdClass();
$exclusion_id = array();

//--variable convience--
$event_id = $this->event_id;
$agenda_id = $this->agenda_id;
$chairman_id = $this->chairman_id;
$instance = $this->instance;

$commityRecords = $DB->get_records('chairman_agenda_members', array('chairman_id' => $chairman_id), '', '*', $ignoremultiple = false);

//print_object($commityRecords);

//--------Comittee Members------------------------------------------------------
$chairmanmembers = array();//Used to store commitee members in an array
if ($commityRecords) {

    $index = 0;
    foreach ($commityRecords as $member) {
        $name = $this->getUserName($member->user_id);
        $toform->participant_name[$index] = $name.": ";
        $chairmanmembers[$member->id] = $name;
    }
}

     //possible motion status
    $motion_result = array( '-1'=>'-----',
                            '1'=>'<font color="#4AA02C">['.get_string('motion_accepted', 'chairman').']</font>',
                           '0'=>'<font color="#8F8F8F">['.get_string('motion_rejected', 'chairman').']</font>');

//Max/Min Years for all motions of the committee
$sql = "SELECT min(e.year) as minyear, max(e.year) as maxyear from {chairman_events} e WHERE e.chairman_id = $chairman_id";
$record =  $DB->get_record_sql($sql, array());
$start_year = $record->minyear;
$end_year = $record->maxyear;

if(!$start_year){
    $start_year = 99999;
}
if(!$end_year){
    $start_year = -1;
}

$index = 1;
//For each $year get motions for the committee
for($year=$start_year;$year<=$end_year;$year++){


$sql = "SELECT DISTINCT m.*, e.day, e.month, e.year, e.id as EID ".
        "FROM {chairman_agenda} a, {chairman_agenda_motions} m, {chairman_events} e, {chairman_agenda_topics} t ".
        "WHERE m.chairman_agenda = a.id AND a.chairman_id = e.chairman_id AND a.chairman_events_id = e.id ".
        "AND a.chairman_id = $chairman_id AND e.year = $year AND t.id=m.chairman_agenda_topics AND ".
        "t.chairman_agenda = m.chairman_agenda AND t.chairman_agenda=a.id AND t.hidden <> 1 ".
        "ORDER BY year ASC, month ASC, day ASC";

$motions =  $DB->get_records_sql($sql, array(), $limitfrom=0, $limitnum=0);


//-----MOTIONS------------------------------------------------------------------
//------------------------------------------------------------------------------
if($motions){

    $mform->closeHeaderBefore('YEAR');
    $mform->addElement('html', "<a name=\"year_$year\"></a>");
    $mform->addElement('header', "YEAR","$year");
    $motion_index=1;

    
    foreach($motions as $key=>$motion){

        $proposing_choices = $chairmanmembers;
        $supporting_choices = $chairmanmembers;

        $proposing_choices['-1']=get_string('proposedby', 'chairman');
        $supporting_choices['-1']=get_string('supportedby', 'chairman');

        
//-----LINK TO TOPIC'S AGENDA-----------------------------------------------------------

        $motionitems = array();
        
        $url = "$CFG->wwwroot/mod/chairman/chairman_meetingagenda/view.php?event_id=" . $motion->eid . "&selected_tab=" . 3;


$motionitems[] = $mform->createElement('static', 'description', '',$motion_index . '. <a href="'.$url.'">'.toMonth($motion->month) ." ".$motion->day.", ".$motion->year.'</a>');



        //$motionitems[] = $mform->createElement('static','test', '', $motion->motion);
        $motionitems[] = $mform->createElement('static', "motion_result[$index][$motion_index]", '', $motion_result, $attributes=null);

$mform->addElement('html', '<div class="chairman_list">');

        $mform->addGroup($motionitems, 'group', '', array(' '), false);

        //$notes = print_collapsible_region($motion->motion, 'motion_proposal_list', "proposition_".$index."_".$motion_index, get_string('motion_proposal', 'chairman'), FALSE, false, TRUE);

        $notes = $motion->motion;

        if(isset($motion->carried) && $motion->carried==0){
       $notes = '<font color="#8F8F8F">'.$notes.'</font>';
        }

        $mform->addElement('html', '<div class="collapsibleregion">'.$notes.'</div>');
        //$mform->addElement('html', $notes);

$mform->addElement('html', '</div><br style="clear:both;">');

//-------DEFAULT VALUEs FOR MOTIONS---------------------------------------------

$toform->proposition[$index][$motion_index] = $motion->motion;

if(isset($motion->carried)){
 $mform->setDefault("motion_result[$index][$motion_index]", $motion_result[$motion->carried]);
} else {
$mform->setDefault("motion_result[$index][$motion_index]", "");
}


//---------END DEFAULTS---------------------------------------------------------

$motion_index++;
    }

$index++;
}



    }

//Hidden Variables
$mform->addElement('hidden', 'event_id', '');
$mform->setType('event_id', PARAM_TEXT);
$mform->addElement('hidden', 'selected_tab', '');
$mform->setType('selected_tab', PARAM_TEXT);

//Set defaults to private variable
$this->default_toform = $toform;
    }

/*
 * Returns the default values for the form.
 */
    function getDefault_toform() {
        return $this->default_toform;
    }

/*
 * Converts a given moodle ID into a FirstName LastName String.
 *
 *  @param $int $userID An unique moodle ID for a moodle user.
 */
    function getUserName($userID){
    Global $DB;

    $user = $DB->get_record('user', array('id' => $userID), '*', $ignoremultiple = false);
    $name = null;
    if($user){
    $name = $user->firstname . " " . $user->lastname;
    }
    return $name;
    }

/*
 * Returns An array of topic names with array keys being the index that the topic
 * is on the page. Used for menu sidebar creation.
 */
    function getIndexToNamesArray(){
        return $this->topicNames;
    }


}
