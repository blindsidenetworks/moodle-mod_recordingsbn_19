<?php /**
 * View and administrate BigBlueButton playback recordings
 *
 * Authors:
 *    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 *
 * @package   mod_recordingsbn
 * @copyright 2012 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // course

if (! $course = get_record('course', 'id', $id)) {
    error('Course ID is incorrect');
}

require_course_login($course);

add_to_log($course->id, 'recordingsbn', 'view all', "index.php?id=$course->id", '');


/// Get all required stringsrecordingsbn

$strrecordingsbns = get_string('modulenameplural', 'recordingsbn');
$strrecordingsbn  = get_string('modulename', 'recordingsbn');


/// Print the header

$navlinks = array();
$navlinks[] = array('name' => $strrecordingsbns, 'link' => '', 'type' => 'activity');
$navigation = build_navigation($navlinks);

print_header_simple($strrecordingsbns, '', $navigation, '', '', true, '', navmenu($course));

/// Get all the appropriate data

if (! $recordingsbns = get_all_instances_in_course('recordingsbn', $course)) {
    notice('There are no instances of recordingsbn', "../../course/view.php?id=$course->id");
    die;
}

/// Print the list of instances (your module will probably extend this)
$timenow  = time();
$strname  = get_string('name');
$strweek  = get_string('week');
$strtopic = get_string('topic');

if ($course->format == 'weeks') {
    $table->head  = array ($strweek, $strname);
    $table->align = array ('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array ($strtopic, $strname);
    $table->align = array ('center', 'left', 'left', 'left');
} else {
    $table->head  = array ($strname);
    $table->align = array ('left', 'left', 'left');
}

foreach ($recordingsbns as $recordingsbn) {
    if (!$recordingsbn->visible) {
        //Show dimmed if the mod is hidden
        $link = '<a class="dimmed" href="view.php?id='.$recordingsbn->coursemodule.'">'.format_string($recordingsbn->name).'</a>';
    } else {
        //Show normal if the mod is visible
        $link = '<a href="view.php?id='.$recordingsbn->coursemodule.'">'.format_string($recordingsbn->name).'</a>';
    }

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array ($recordingsbn->section, $link);
    } else {
        $table->data[] = array ($link);
    }
}
print_heading($strrecordingsbns);
print_table($table);

/// Finish the page

print_footer($course);

?>
