<?php 
/**
 * Restore for recordingsbn.
 *
 * Authors:
 *      Jesus Federico (jesus [at] blindsidenetworks [dt] org)
 *
 * @package   mod_recordingsbn
 * @copyright 2010-2012 Blindside Networks
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */


//This php script contains all the stuff to backup/restore
//recordingsbn mods

//This is the "graphical" structure of the recordingsbn mod:
//
//                       recordingsbn
//                    (CL,pk->id)
//
// Meaning: pk->primary key field of the table
//          fk->foreign key to link with parent
//          nt->nested field (recursive data)
//          CL->course level info
//          UL->user level info
//          files->table may have files)
//
//-----------------------------------------------------------

//This function executes all the restore procedure about this mod
function recordingsbn_restore_mods($mod,$restore) {

    global $CFG;

    $status = true;

    //Get record from backup_ids
    $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

    if ($data) {
        //Now get completed xmlized object
        $info = $data->info;
        //traverse_xmlize($info);                                                                     //Debug
        //print_object ($GLOBALS['traverse_array']);                                                  //Debug
        //$GLOBALS['traverse_array']="";                                                              //Debug

        //Now, build the LABEL record structure
        $recordingsbn->course = $restore->course_id;
        $recordingsbn->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
        $recordingsbn->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

        //The structure is equal to the db, so insert the recordingsbn
        $newid = insert_record ("recordingsbn",$recordingsbn);

        //Do some output
        if (!defined('RESTORE_SILENTLY')) {
            echo "<li>".get_string("modulename","recordingsbn")." \"".format_string(stripslashes($recordingsbn->name),true)."\"</li>";
        }
        backup_flush(300);

        if ($newid) {
            //We have the newid, update backup_ids
            backup_putid($restore->backup_unique_code,$mod->modtype,
                    $mod->id, $newid);
             
        } else {
            $status = false;
        }
    } else {
        $status = false;
    }

    return $status;
}

function recordingsbn_decode_content_links_caller($restore) {
    global $CFG;
    $status = true;

    return $status;
}

//This function returns a log record with all the necessay transformations
//done. It's used by restore_log_module() to restore modules log.
function recordingsbn_restore_logs($restore,$log) {

    $status = false;

    //Depending of the action, we recode different things
    switch ($log->action) {
        case "add":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "update":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        default:
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            }
            break;
    }

    if ($status) {
        $status = $log;
    }
    return $status;
}
?>
