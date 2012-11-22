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

//This php script contains all the stuff to backup/restore
//recordingsbn mods

//This is the "graphical" structure of the BigBlueButton mod:
//
//                       recordingsbn
//                     (CL,pk->id)
//
// Meaning: pk->primary key field of the table
//          fk->foreign key to link with parent
//          nt->nested field (recursive data)
//          CL->course level info
//          UL->user level info
//          files->table may have files)
//
//-----------------------------------------------------------

//This function executes all the backup procedure about this mod
function recordingsbn_backup_mods($bf,$preferences) {
    global $CFG;

    $status = true;

    ////Iterate over recordingsbn table
    if ($recordingsbns = get_records ("recordingsbn","course", $preferences->backup_course,"id")) {
        foreach ($recordingsbns as $recordingsbn) {
            if (backup_mod_selected($preferences,'recordingsbn',$recordingsbn->id)) {
                $status = recordingsbn_backup_one_mod($bf,$preferences,$recordingsbn);
            }
        }
    }
    return $status;
}
 
function recordingsbn_backup_one_mod($bf,$preferences,$recordingsbn) {

    global $CFG;

    if (is_numeric($recordingsbn)) {
        $recordingsbn = get_record('recordingsbn','id',$recordingsbn);
    }

    $status = true;

    //Start mod
    fwrite ($bf,start_tag("MOD",3,true));
    //Print assignment data
    fwrite ($bf,full_tag("ID",4,false,$recordingsbn->id));
    fwrite ($bf,full_tag("MODTYPE",4,false,"recordingsbn"));
    fwrite ($bf,full_tag("NAME",4,false,$recordingsbn->name));
    fwrite ($bf,full_tag("TIMECREATED",4,false,$recordingsbn->timecreated));
    fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$recordingsbn->timemodified));
    //End mod
    $status = fwrite ($bf,end_tag("MOD",3,true));

    return $status;
}

////Return an array of info (name,value)
function recordingsbn_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
    if (!empty($instances) && is_array($instances) && count($instances)) {
        $info = array();
        foreach ($instances as $id => $instance) {
            $info += recordingsbn_check_backup_mods_instances($instance,$backup_unique_code);
        }
        return $info;
    }

    //First the course data
    $info[0][0] = get_string("modulenameplural","recordingsbn");
    $info[0][1] = count_records("recordingsbn", "course", "$course");
    return $info;
}

////Return an array of info (name,value)
function recordingsbn_check_backup_mods_instances($instance,$backup_unique_code) {
    //First the course data
    $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
    $info[$instance->id.'0'][1] = '';
    return $info;
}

?>
