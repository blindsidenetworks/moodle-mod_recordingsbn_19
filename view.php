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
require_once($CFG->dirroot.'/mod/bigbluebuttonbn/locallib.php');
//require_once($CFG->dirroot.'/lib/ddllib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // recordingsbn instance ID

$action  = optional_param('action', 0, PARAM_TEXT);
$recordingid  = optional_param('recordingid', 0, PARAM_TEXT);

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

///Set strings to show
$view_head_recording = get_string('view_head_recording', 'recordingsbn');
$view_head_course = get_string('view_head_course', 'recordingsbn');
$view_head_activity = get_string('view_head_activity', 'recordingsbn');
$view_head_description = get_string('view_head_description', 'recordingsbn');
$view_head_date = get_string('view_head_date', 'recordingsbn');
$view_head_length = get_string('view_head_length', 'recordingsbn');
$view_head_duration = get_string('view_head_duration', 'recordingsbn');
$view_head_actionbar = get_string('view_head_actionbar', 'recordingsbn');

/// Prepare page header
$strrecordingsbns = get_string('modulenameplural', 'recordingsbn');
$strrecordingsbn  = get_string('modulename', 'recordingsbn');

$navlinks = array();
$navlinks[] = array('name' => $strrecordingsbns, 'link' => "index.php?id=$course->id", 'type' => 'activity');
$navlinks[] = array('name' => format_string($recordingsbn->name), 'link' => '', 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

///Initialize table headers
if ( $moderator ) {
    $table->head  = array ($view_head_recording, $view_head_activity, $view_head_description, $view_head_date, $view_head_duration, $view_head_actionbar);
    $table->align = array ('left', 'left', 'left', 'left', 'center', 'left');
} else {
    $table->head  = array ($view_head_recording, $view_head_activity, $view_head_description, $view_head_date, $view_head_duration);
    $table->align = array ('left', 'left', 'left', 'left', 'center');
}

/// Print page headers
print_header_simple(format_string($recordingsbn->name), '', $navigation, '', '', true,
              update_module_button($cm->id, $course->id, $strrecordingsbn), navmenu($course, $cm));


// Recordings plugin code
if ( isset($CFG->bigbluebuttonbnSecuritySalt) ) {
    
    // BigBlueButton Setup
    $url = trim(trim($CFG->bigbluebuttonbnServerURL),'/').'/';
    $salt = trim($CFG->bigbluebuttonbnSecuritySalt);

    //Execute actions if there is one and it is allowed
    if( isset($action) && isset($recordingid) && $moderator ){
        if( $action == 'show' )
            bigbluebuttonbn_doPublishRecordings($recordingid, 'true', $url, $salt);
        else if( $action == 'hide')
            bigbluebuttonbn_doPublishRecordings($recordingid, 'false', $url, $salt);
        else if( $action == 'delete')
            bigbluebuttonbn_doDeleteRecordings($recordingid, $url, $salt);
    }

    $meetingID='';
    if( $results = get_records_sql('SELECT DISTINCT meetingid, courseid, bigbluebuttonbnid FROM '.$CFG->prefix.'bigbluebuttonbn_log WHERE '.$CFG->prefix.'bigbluebuttonbn_log.courseid='.$course->id. ' AND '.$CFG->prefix.'bigbluebuttonbn_log.record = 1 AND '.$CFG->prefix.'bigbluebuttonbn_log.event = \'Create\';' ) ){
        foreach ($results as $result) {
            if (strlen($meetingID) > 0) $meetingID .= ',';
            $meetingID .= $result->meetingid;
        }
    }

    //If there are meetings with recordings load the data to the table
    if ( $meetingID != '' ){
        $recordingsbn = bigbluebuttonbn_getRecordingsArray($meetingID, $url, $salt);
        
        if( isset($recordingsbn) && !isset($recordingsbn['messageKey']) ){
            foreach ( $recordingsbn as $recording ){
                if ( $moderator || $recording['published'] == 'true' ) {

                    $length = 0;
                    $endTime = isset($recording['endTime'])? floatval($recording['endTime']):0;
                    $endTime = $endTime - ($endTime % 1000);
                    $startTime = isset($recording['startTime'])? floatval($recording['startTime']):0;
                    $startTime = $startTime - ($startTime % 1000);
                    $duration = intval(($endTime - $startTime) / 60000);

                    $meta_activity = isset($recording['meta_contextactivity'])?str_replace('"', '\"', $recording['meta_contextactivity']):'';
                    $meta_description = isset($recording['meta_contextactivitydescription'])?str_replace('"', '\"', $recording['meta_contextactivitydescription']):'';

                    $actionbar = '';
                    $params['id'] = $cm->id;
                    $params['recordingid'] = $recording['recordID'];
                    
                    if ( $moderator ) {
                        ///Set action [show|hide]
                        if ( $recording['published'] == 'true' ){
                            $params['action'] = 'hide';
                        } else {
                            $params['action'] = 'show';
                        }
                        $url = $CFG->wwwroot."/mod/recordingsbn/view.php?id=".$params['id']."&action=".$params['action']."&recordingid=".$params['recordingid'];
                        ///With text
                        $actionbar = "<a id='actionbar-".$params['action']."-a-".$recording['recordID']."' title='".get_string($params['action'])."' href='".$url."'>".get_string($params['action'])."</a>";
                        $actionbar .= '&nbsp;';
                        ///With icon

                        ///Set action delete
                        $params['action'] = 'delete';
                        $url = $CFG->wwwroot."/mod/recordingsbn/view.php?id=".$params['id']."&action=".$params['action']."&recordingid=".$params['recordingid'];
                        ///With text
                        $actionbar .= "<a id='actionbar-".$params['action']."-a-".$recording['recordID']."' title='".get_string($params['action'])."' onClick='if(confirm(\"".get_string('view_delete_confirmation', 'recordingsbn')."\")) window.location=\"".$url."\"; return false;' href='#'>".get_string($params['action'])."</a>";
                        ///With icon

                    }

                    $type = '';
                    foreach ( $recording['playbacks'] as $playback ){
                    	if ($recording['published'] == 'true'){
                    	    $type .= '<a href="'.$playback['url'].'" title="'.$playback['type'].'" target="_new">'.$playback['type'].'</a>&#32;';
                    	} else {
                    		$type .= $playback['type'].'&#32;';
                    	}
                    }
                    
                    //Make sure the startTime is timestamp
                    if( !is_numeric($recording['startTime']) ){
                        $date = new DateTime($recording['startTime']);
                        $recording['startTime'] = date_timestamp_get($date);
                    } else {
                        $recording['startTime'] = $recording['startTime'] / 1000;
                    }
                    //Set corresponding format
                    //$format = isset(get_string('strftimerecentfull', 'langconfig'));
                    //if( !isset($format) )
                    $format = '%a %h %d %H:%M:%S %Z %Y';
                    //Format the date
                    $formatedStartDate = userdate($recording['startTime'], $format, usertimezone($USER->timezone) );
                    
                    if ( $moderator ) {
                        $table->data[] = array ($type, $meta_activity, $meta_description, str_replace( " ", "&nbsp;", $formatedStartDate), $duration, $actionbar );
                    } else {
                        $table->data[] = array ($type, $meta_activity, $meta_description, str_replace( " ", "&nbsp;", $formatedStartDate), $duration);
                    }
                    
                }
            }
        }

    }

    //Print the table
    print_box_start();
    print_table($table);
    print_box_end();

} else {
    print_box_start();
    print_error('view_dependency_error', 'recordingsbn');
    print_box_end();

}

/// Finish the page
print_footer($course);
