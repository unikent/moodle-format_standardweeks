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
 * Standard weeks course format.
 * Display the whole course as "weeks" made of modules with a standard layout forced on the user.
 *
 * @package    format
 * @subpackage standardweeks
 * @copyright  2015 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/completionlib.php');

// Make sure all sections are created.
$course = course_get_format($course)->get_course();
course_create_sections_if_missing($course, range(0, $course->numsections));

// Render the page.
$renderer = $PAGE->get_renderer('format_standardweeks');

if (!empty($displaysection)) {
    $renderer->print_single_section_page($course, null, null, null, null, $displaysection);
} else {
    $modinfo = get_fast_modinfo($course);
    if (empty($modinfo->get_cms()) && !$PAGE->user_is_editing()) {
    	// Do we have an active rollover?
    	$rollover = new \local_rollover\Course($course->id);
		if ($rollover->has_active_rollover()) {
			redirect(new \moodle_url('/course/format/standardweeks/rollover.php', array(
				'id' => $course->id
			)));
			die;
		}

        $renderer->print_empty($course, $modinfo);
    } else {
        $renderer->print_multiple_section_page($course, null, null, null, null);
    }
}

$PAGE->requires->js('/course/format/standardweeks/javascript/format.js');
$PAGE->requires->js('/course/format/weeks/format.js');
