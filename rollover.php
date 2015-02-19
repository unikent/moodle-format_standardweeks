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

$PAGE->set_url('/course/format/standardweeks/rollover.php', array(
	'id' => $id
));
$PAGE->set_context(context_course::instance($course->id));
$PAGE->set_pagelayout('admin');
$PAGE->requires->js('/course/format/standardweeks/javascript/rollover.js');

echo $OUTPUT->header();
echo $OUTPUT->heading('Rollover');

$buttons = array();
$options = $CFG->kent->paths;
foreach ($options as $name => $url) {
	$ucname = ucwords($name);
	$buttons[] = "<label class=\"btn btn-default\"><input type=\"radio\" name=\"moodle\" data-uri=\"{$url}\" id=\"moodle-{$name}\" autocomplete=\"off\"> {$ucname}</label>";
}
$buttons = implode(' ', $buttons);

echo <<<HTML5
<div class="bootstrap">
	<form class="form-horizontal">
		<p>Which Moodle would you like to rollover from?</p>
		<div id="moodle-select" class="btn-group" data-toggle="buttons">
			$buttons
		</div>
		<div class="form-group">
			<div class="col-sm-4">
				<input type="email" class="form-control" id="moodle-search" placeholder="Search by module code">
			</div>
		</div>
		<div id="rollover-options">
			<p>Too many results! Try to use the options above to narrow it down a bit.</p>
		</div>
	</form>
</div>
HTML5;

echo $OUTPUT->footer();
