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

require_once(dirname(__FILE__) . '/../../../config.php');

$id = required_param('id', PARAM_INT);

require_login();

$course = $DB->get_record('course', array(
	'id' => $id
), '*', MUST_EXIST);

$PAGE->set_url('/course/format/standardweeks/merge.php', array(
	'id' => $id
));
$PAGE->set_context(context_course::instance($course->id));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading('Rollover');

echo "This feature has not yet been implemented.";

echo $OUTPUT->footer();