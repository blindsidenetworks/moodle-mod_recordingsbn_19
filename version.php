<?php
/**
 * View and administrate BigBlueButton playback recordings
 *
 * Authors:
 *    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)    
 *
 * @package   mod_recordingsbn
 * @copyright 2012-2013 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();

$module->version   = 2013071001;
$module->requires  = 2007101591.12;
$module->cron      = 0;
$module->component = 'mod_recordingsbn';
$module->release  = '1.0.9';
$module->dependencies = array(
    'mod_bigbluebuttonbn' => 2013071000,
);
