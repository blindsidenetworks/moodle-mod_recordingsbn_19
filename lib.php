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

defined('MOODLE_INTERNAL') || die();

$recordingsbn_EXAMPLE_CONSTANT = 42;     /// for example


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $recordingsbn An object from the form in mod_form.php
 * @return int The id of the newly inserted recordingsbn record
 */
function recordingsbn_add_instance($recordingsbn) {

    $recordingsbn->timecreated = time();

    # You may have to add extra stuff in here #

    return insert_record('recordingsbn', $recordingsbn);
}


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $recordingsbn An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function recordingsbn_update_instance($recordingsbn) {

    $recordingsbn->timemodified = time();
    $recordingsbn->id = $recordingsbn->instance;

    # You may have to add extra stuff in here #

    return update_record('recordingsbn', $recordingsbn);
}


/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function recordingsbn_delete_instance($id) {

    if (! $recordingsbn = get_record('recordingsbn', 'id', $id)) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! delete_records('recordingsbn', 'id', $recordingsbn->id)) {
        $result = false;
    }

    return $result;
}


/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function recordingsbn_user_outline($course, $user, $mod, $recordingsbn) {
    return $return;
}


/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function recordingsbn_user_complete($course, $user, $mod, $recordingsbn) {
    return true;
}


/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in recordingsbn activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function recordingsbn_print_recent_activity($course, $isteacher, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}


/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function recordingsbn_cron () {
    return true;
}


/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of recordingsbn. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $recordingsbnid ID of an instance of this module
 * @return mixed boolean/array of students
 */
function recordingsbn_get_participants($recordingsbnid) {
    return false;
}


/**
 * This function returns if a scale is being used by one recordingsbn
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $recordingsbnid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 */
function recordingsbn_scale_used($recordingsbnid, $scaleid) {
    $return = false;

    //$rec = get_record("recordingsbn","id","$recordingsbnid","scale","-$scaleid");
    //
    //if (!empty($rec) && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}


/**
 * Checks if scale is being used by any instance of recordingsbn.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any recordingsbn
 */
function recordingsbn_scale_used_anywhere($scaleid) {
    if ($scaleid and record_exists('recordingsbn', 'grade', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}


/**
 * Execute post-install custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function recordingsbn_install() {
    return true;
}


/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function recordingsbn_uninstall() {
    return true;
}

function recordingsbn_get_coursemodule_info($coursemodule) {
/*
    global $CFG;

    $info = null;
    if ($recordingsbn = get_record('recordingsbn', array('id'=>$coursemodule->instance), 'id, name, intro')) {

        $info = new object();
        $info->name  = $recordingsbn->name.'  '.$recordingsbn->intro;

        //if ( $bigbluebuttonbn->newwindow == 1 ){
            $info->extra = "onclick=\"window.open('http://192.168.0.172/mod/recordingsbn/view.php?id=".$coursemodule->id."&amp;redirect=1'); return false;\"";
            //$info->extra =  urlencode("onclick=\"this.target='bigbluebuttonbn$bigbluebuttonbn->id'; return ".
            //        "openpopup('/mod/bigbluebuttonbn/view.php?inpopup=true&amp;id=".
            //                $coursemodule->id.
            //                "','bigbluebuttonbn$bigbluebuttonbn->id','$bigbluebuttonbn->newwindow');\"");

        //}

    }
*/    
    $info = new object();
    $info->extra = "onclick=\"window.open('http://192.168.0.172/mod/recordingsbn/view.php?id=".$coursemodule->id."&amp;redirect=1'); return false;\"";
    return $info;

}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other recordingsbn functions go here.  Each of them must have a name that
/// starts with recordingsbn_
/// Remember (see note in first lines) that, if this section grows, it's HIGHLY
/// recommended to move all funcions below to a new "localib.php" file.


?>
