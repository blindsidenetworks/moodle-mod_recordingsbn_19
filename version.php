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

defined('MOODLE_INTERNAL') || die();

$module->version   = 2012112000;
$module->requires  = 2007101509;
$module->cron      = 0;
$module->component = 'mod_recordingsbn';
$module->maturity = MATURITY_ALPHA;  // [MATURITY_STABLE | MATURITY_RC | MATURITY_BETA | MATURITY_ALPHA]
$module->release  = '1.0.8';
$module->dependencies = array(
    'mod_bigbluebuttonbn' => 2012112010,
);