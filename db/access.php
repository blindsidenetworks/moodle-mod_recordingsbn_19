<?php
/**
 * Capabilities
 *
 * Authors:
 *      Jesus Federico (jesus [at] blindsidenetworks [dt] org)
 *
 * @package   mod_recordingsbn
 * @copyright 2010-2012 Blindside Networks
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

$mod_recordingsbn_capabilities = array(
	
    'mod/recordingsbn:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'guest' => CAP_ALLOW,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    )
);

?>
