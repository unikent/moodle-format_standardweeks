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
$PAGE->set_course($course);
$PAGE->navbar->add('Rollover');
$PAGE->requires->css('/course/format/standardweeks/styles.css');
$PAGE->requires->js_call_amd('format_standardweeks/rollover', 'init', array());

echo $OUTPUT->header();
echo $OUTPUT->heading('Rollover');

echo '<div id="rollovercontainer" class="bootstrap" data-id="' . $course->id . '">';

$rollover = new \local_rollover\Course($course->id);
if ($rollover->has_active_rollover()) {
    // We... already have a rollover in progress..
    // Javascript will take care of this.
    echo '<p class="text-center"><i class="fa fa-spin fa-spinner"></i></p><div id="currentrollover"></div></div>';
    echo $OUTPUT->footer();
    die;
}

$buttons = array();
$options = $CFG->kent->paths;
foreach ($options as $name => $url) {
    $ucname = ucwords($name);
    $buttons[] = "<label class=\"btn btn-default\"><input type=\"radio\" name=\"moodle\" data-name=\"{$name}\" id=\"moodle-{$name}\" autocomplete=\"off\"> {$ucname}</label>";
}
$buttons = implode(' ', $buttons);

echo <<<HTML5
<div id="rollovercontainer" class="bootstrap" data-id="{$course->id}">
    <p>Which Moodle would you like to rollover from?</p>

    <div id="moodle-select" class="btn-group" data-toggle="buttons">
        <label class="btn btn-default active"><input type="radio" name="moodle" data-name="*" id="moodle-any" autocomplete="off"> Any</label>
        $buttons
    </div>

    <form class="form-horizontal">
        <div class="form-group">
            <div class="col-sm-4">
                <input type="text" class="form-control" id="moodle-search" placeholder="Search by module code" autocomplete="off">
            </div>
        </div>
    </form>
    <div id="rollover-options">
        <p>Too many results! Try to use the options above to narrow it down a bit.</p>
    </div>
</div>
HTML5;

echo $OUTPUT->footer();
