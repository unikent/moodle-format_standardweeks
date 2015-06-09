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

$course = $DB->get_record('course', array(
    'id' => $id
), '*', MUST_EXIST);

require_login($course->id);

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
    $buttons[] = "<li><a href=\"#\" data-name=\"{$name}\" id=\"moodle-{$name}\">{$ucname}</a>";
}
$buttons = implode(' ', $buttons);

echo <<<HTML5
<div id="rollovercontainer" class="bootstrap" data-id="{$course->id}">
    <p>Which module would you like to rollover from?</p>

    <form class="form-horizontal">
        <div class="form-group">
            <div class="col-sm-8">
                <div class="input-group">
                    <div class="input-group-btn">
                        <button id="moodle-select-button" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        Any Moodle <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" id="moodle-select">
                            <li><a href="#" data-name="*" id="moodle-any" autocomplete="off">Any</li>
                            $buttons
                        </ul>
                    </div>
                    <input type="text" class="form-control" id="moodle-search" placeholder="Search by module code" autocomplete="off">
                </div>
            </div>
        </div>
    </form>
    <div id="rollover-options">
    </div>
</div>
HTML5;

echo $OUTPUT->footer();
