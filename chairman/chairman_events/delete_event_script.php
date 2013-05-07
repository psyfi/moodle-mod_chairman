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
/**
 * @package   chairman
 * @copyright 2011 Raymond Wainman, Dustin Durand, Patrick Thibaudeau (Campus St. Jean, University of Alberta)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once('../lib.php');
require_once('../chairman_meetingagenda/lib.php');
require_once('../lib_chairman.php');
echo '<link rel="stylesheet" type="text/css" href="../style.php">';

$id = optional_param('id', 0, PARAM_INT);    // Course Module ID

$event_id = optional_param('event_id', 0, PARAM_INT);

chairman_check($id);
chairman_header($id, 'deleteevent', 'events.php?id=' . $id);

echo '<div><div class="title">' . get_string('deleteevent', 'chairman') . '</div>';

echo get_string('deletingevent', 'chairman');

echo '<span class="content">';

echo '</span></div>';

chairman_footer();


//-----------------------------------------------------------------------------
//Delete Event itself
$DB->delete_records('chairman_events', array('id' => $event_id));

echo '<script type="text/javascript">';
echo 'window.location.href="' . $CFG->wwwroot . '/mod/chairman/chairman_events/events.php?id=' . $id . '";';
echo '</script>';
?>
