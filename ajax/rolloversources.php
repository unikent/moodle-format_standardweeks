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

define('AJAX_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../../../config.php');

$PAGE->set_context(\context_system::instance());
$PAGE->set_url('/course/format/standardweeks/ajax/rolloversources.php');

require_login();
require_sesskey();

$dist = required_param('dist', PARAM_RAW_TRIMMED);
$search = required_param('search', PARAM_RAW_TRIMMED);

$sources = \local_rollover\Sources::get_course_list($dist, "%{$search}%");

$table = new html_table();
$table->head = array('Moodle', 'Shortname', 'Fullname', 'Action');
$table->colclasses = array('moodle', 'shortname', 'fullname', 'action');
$table->id = 'rolloversources';
$table->attributes['class'] = 'admintable generaltable';
$table->data = array();

foreach ($sources as $course) {
	$table->data[] = array(
        $course->moodle_dist,
        $course->shortname,
        $course->fullname,
        '<button class="btn btn-default" data-id="' . $course->id . '">Rollover</button>'
    );
}

echo $OUTPUT->header();
echo json_encode(array(
	'result' => \html_writer::table($table)
));