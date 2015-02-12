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
 * Renderer for outputting the weeks course format.
 *
 * @package    format
 * @subpackage standardweeks
 * @copyright  2014 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/weeks/renderer.php');
require_once($CFG->dirroot . '/course/format/standardweeks/lib.php');


/**
 * Basic renderer for weeks format.
 *
 * @package    format
 * @subpackage standardweeks
 * @copyright  2014 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_standardweeks_renderer extends format_weeks_renderer
{
    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        global $COURSE, $OUTPUT;

        $pre = '';

        // Add error message if we have been scheduled for deletion.
        $cmenabled = get_config("local_catman", "enable");
        if ($cmenabled && \local_catman\core::is_scheduled($COURSE)) {
            $time = \local_catman\core::get_expiration($COURSE);
            $time = strftime("%d/%m/%Y %H:%M", $time);
            $pre .= $OUTPUT->notification("This course has been scheduled for deletion on {$time}.");
        }

        if (!$COURSE->visible) {
            $pre .= $OUTPUT->notification('This course is not currently visible to students.', 'notifywarning');
        }

        return $pre . html_writer::start_tag('ul', array('class' => 'weeks'));
    }
    /**
     * Generate the display of the header part of a section before
     * course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a single-section page
     * @param int $sectionreturn The section to return to after an action
     * @return string HTML to output.
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn=null) {
        $context = \context_course::instance($course->id);
        if (empty($section->name) && has_capability('moodle/course:update', $context)) {
            $section->name = "{$course->shortname}: {$course->fullname}";
        }

        return parent::section_header($section, $course, $onsectionpage, $sectionreturn);
    }

    /**
     * Generate html for a section summary text
     *
     * @param section_info $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function format_summary_text($section) {
        global $COURSE, $PAGE;

        // Don't know whats happening.
        if ($COURSE->id !== $section->course) {
            return parent::format_summary_text($section);
        }

        $context = \context_course::instance($section->course);
        if (!has_capability('moodle/course:update', $context)) {
            return parent::format_summary_text($section);
        }

        if (empty($section->summary)) {
            $summary = '';
            if ($section->section === 0) {
                $summary = get_string('firstsectiondescsuggestion', 'format_standardweeks');
            } elseif ($section->section === 1) {
                // Is the section title 'Assessment info'?
                $assessmenttitle = get_string('assessmentinfotitle', 'format_standardweeks');
                if ($section->name !== $assessmenttitle) {
                    $summary = get_string('assessmentinfosuggestion', 'format_standardweeks');
                }
            } else {
                $summary = get_string('sectiondescsuggestion', 'format_standardweeks');
            }

            $section->summary = "<div class=\"suggestion\">{$summary}</div>";
            $section->summaryformat = FORMAT_HTML;
        }

        if ($section->section === 0 && strpos($section->section, 'How to use this module') === false) {
            $suggestion = get_string('howtousethissuggestion', 'format_standardweeks');
            $section->summary .= "<div class=\"suggestion\">{$suggestion}</div>";
        }

        return parent::format_summary_text($section);
    }
}
