<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Sets up the course for "fresh" use.
 *
 * @package    format
 * @subpackage standardweeks
 * @copyright  2014 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/aspirelists/lib.php');
require_once($CFG->dirroot . '/mod/forum/lib.php');

$course = required_param('id', PARAM_INT);

require_sesskey();
require_login();

$course = $DB->get_record('course', array(
	'id' => $course
), '*', MUST_EXIST);

$PAGE->set_context(context_course::instance($course->id));
$PAGE->set_url('/course/format/standardweeks/ajax/fresh.php');

// Add the forum.
forum_get_course_forum($course->id, 'news');

// Setup an aspire lists instance.
$module = $DB->get_record('modules', array(
    'name' => 'aspirelists'
));

// Create a data container.
$rl = new \stdClass();
$rl->course     = $course->id;
$rl->name       = 'Reading list';
$rl->intro      = '';
$rl->introformat  = 1;
$rl->category     = 'all';
$rl->timemodified = time();

// Create the instance.
$instance = aspirelists_add_instance($rl, new \stdClass());

// Find the first course section.
$section = $DB->get_record_sql("SELECT id, sequence FROM {course_sections} WHERE course=:cid AND section=0", array(
    'cid' => $course->id
));

// Create a module container.
$cm = new \stdClass();
$cm->course     = $course->id;
$cm->module     = $module->id;
$cm->instance   = $instance;
$cm->section    = $section->id;
$cm->section 	= 0;
$cm->visible    = 1;

// Create the module.
$cm = add_course_module($cm);
course_add_cm_to_section($course->id, $cm, 0);

// Finish up.
echo $OUTPUT->header();
echo json_encode(array(
	"result" => "success"
));
