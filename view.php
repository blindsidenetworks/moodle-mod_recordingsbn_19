<?php
/**
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
//require_once($CFG->dirroot.'/lib/ddllib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // recordingsbn instance ID

if ($id) {
    if (! $cm = get_coursemodule_from_id('recordingsbn', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (! $recordingsbn = get_record('recordingsbn', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }

} else if ($a) {
    if (! $recordingsbn = get_record('recordingsbn', 'id', $a)) {
        error('Course module is incorrect');
    }
    if (! $course = get_record('course', 'id', $recordingsbn->course)) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('recordingsbn', $recordingsbn->id, $course->id)) {
        error('Course Module ID was incorrect');
    }

} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$moderator = has_capability('mod/bigbluebuttonbn:moderate', $context);

add_to_log($course->id, "recordingsbn", "view", "view.php?id=$cm->id", "$recordingsbn->id");

/// Print the page header
$strrecordingsbns = get_string('modulenameplural', 'recordingsbn');
$strrecordingsbn  = get_string('modulename', 'recordingsbn');

$navlinks = array();
$navlinks[] = array('name' => $strrecordingsbns, 'link' => "index.php?id=$course->id", 'type' => 'activity');
$navlinks[] = array('name' => format_string($recordingsbn->name), 'link' => '', 'type' => 'activityinstance');

$navigation = build_navigation($navlinks);

echo '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/recordingsbn/js/libs/jquery/1.7.2/jquery.min.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/recordingsbn/js/libs/dataTables/1.9.1/jquery.dataTables.min.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/recordingsbn/js/recordingsbn.js"></script>'."\n";


print_header_simple(format_string($recordingsbn->name), '', $navigation, '', '', true,
              update_module_button($cm->id, $course->id, $strrecordingsbn), navmenu($course, $cm));

/// Print the main part of the page

// Recordings plugin code
//if( table_exists('bigbluebuttonbn') ) print "hello";
if ( isset($CFG->bigbluebuttonbnSecuritySalt) ) {
    //echo 'YOUR CODE GOES HERE';
    
    $meetingID='';
    $results = get_records_sql('SELECT DISTINCT meetingid, courseid, bigbluebuttonbnid FROM '.$CFG->prefix.'bigbluebuttonbn_log WHERE '.$CFG->prefix.'bigbluebuttonbn_log.courseid='.$course->id. ' AND '.$CFG->prefix.'bigbluebuttonbn_log.record = 1 AND '.$CFG->prefix.'bigbluebuttonbn_log.event = \'Create\';' );
    
    $groups = groups_get_all_groups($course->id);
    if( isset($groups) && count($groups) > 0 ){  //If the course has groups include groupid in the name to look for possible recordings related to the sub-activities
        foreach ($results as $result) {
            if (strlen($meetingID) > 0) $meetingID .= ',';
            $meetingID .= $result->meetingid;
            foreach ( $groups as $group ){
                $meetingID .= ','.$result->meetingid.'['.$group->id.']';
            }
        }
    
    } else {                                    // No groups means that it wont check any other sub-activity
        foreach ($results as $result) {
            if (strlen($meetingID) > 0) $meetingID .= ',';
            $meetingID .= $result->meetingid;
        }
    
    }
    
    //echo $OUTPUT->heading($recordingsbn->name);
    //echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');
    
    echo '<script type="text/javascript" >var meetingid = "'.$meetingID.'";</script>'."\n";
    echo '<script type="text/javascript" >var courseid = "'.$course->id.'";</script>'."\n";
    echo '<script type="text/javascript" >var ismoderator = "'.($moderator?'true':'false').'";</script>'."\n";
    echo '<script type="text/javascript" >var wwwroot = "'.$CFG->wwwroot.'";</script>'."\n";
    
    echo '<script type="text/javascript" >'."\n";
    echo '    var joining = "false";'."\n";
    echo '    var bigbluebuttonbn_view = "after";'."\n";
    echo '    var view_recording_list_recording = "'.get_string('view_recording_list_recording', 'bigbluebuttonbn').'";'."\n";
    echo '    var view_recording_list_course = "'.get_string('view_recording_list_course', 'bigbluebuttonbn').'";'."\n";
    echo '    var view_recording_list_activity = "'.get_string('view_recording_list_activity', 'bigbluebuttonbn').'";'."\n";
    echo '    var view_recording_list_description = "'.get_string('view_recording_list_description', 'bigbluebuttonbn').'";'."\n";
    echo '    var view_recording_list_date = "'.get_string('view_recording_list_date', 'bigbluebuttonbn').'";'."\n";
    echo '    var view_recording_list_duration = "'.get_string('view_recording_list_duration', 'bigbluebuttonbn').'";'."\n";
    echo '    var view_recording_list_actionbar = "'.get_string('view_recording_list_actionbar', 'bigbluebuttonbn').'";'."\n";
    echo '    var view_recording_list_actionbar_hide = "'.get_string('view_recording_list_actionbar_hide', 'bigbluebuttonbn').'";'."\n";
    echo '    var view_recording_list_actionbar_show = "'.get_string('view_recording_list_actionbar_show', 'bigbluebuttonbn').'";'."\n";
    echo '    var view_recording_list_actionbar_delete = "'.get_string('view_recording_list_actionbar_delete', 'bigbluebuttonbn').'";'."\n";
    echo '</script>'."\n";
    
    echo '    <center>'."\n";
    echo '      <div id="dynamic"></div>'."\n";
    echo '      <table cellpadding="0" cellspacing="0" border="0" class="display" id="recordingsbn">'."\n";
    echo '        <thead>'."\n";
    echo '        </thead>'."\n";
    echo '        <tbody>'."\n";
    echo '        </tbody>'."\n";
    echo '        <tfoot>'."\n";
    echo '        </tfoot>'."\n";
    echo '      </table>'."\n";
    echo '    </center>'."\n";
    
    //echo $OUTPUT->box_end();
} else {
    //echo $OUTPUT->heading($recordingsbn->name);
    //echo $OUTPUT->box_start('generalbox boxaligncenter', 'dates');
    
    print_error('view_error_bigbluebuttonbn_not_installed', 'recordingsbn');
    
    //echo $OUTPUT->box_end();
    
}

/// Finish the page
print_footer($course);

?>
