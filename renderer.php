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
 * @copyright  2015 Skylar Kelty <S.Kelty@kent.ac.uk>
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
 * @copyright  2015 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_standardweeks_renderer extends format_weeks_renderer
{
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
        if ($section->section == 0) {
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

        // We are not ready!
        return parent::format_summary_text($section);

        // Don't know whats happening.
        if ($COURSE->id !== $section->course) {
            return parent::format_summary_text($section);
        }

        $context = \context_course::instance($section->course);
        if (!$PAGE->user_is_editing() || !has_capability('moodle/course:update', $context)) {
            return parent::format_summary_text($section);
        }

        if (empty($section->summary)) {
            $summary = '';
            if ($section->section === 0) {
                $summary = get_string('firstsectiondescsuggestion', 'format_standardweeks');
            } else if ($section->section === 1) {
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

    /**
     * This course, is empty.
     */
    public function print_empty($course, $modinfo) {
        echo \html_writer::tag('h2',  get_string('emptytitle', 'format_standardweeks'), array(
            'class' => 'nopad'
        ));

        echo \html_writer::start_tag('div', array('id' => 'formatbuttons'));
        echo \html_writer::tag('p',  get_string('emptydesc', 'format_standardweeks'));

        echo \html_writer::start_tag('div', array('class' => 'row'));

        // The Start Fresh button.
        echo \html_writer::start_tag('div', array('class' => 'col-md-6'));
        echo \html_writer::tag('button', 'Start fresh', array(
            'id' => 'action-fresh',
            'class' => 'btn btn-default btn-lg btn-block',
            'data-id' => $course->id
        ));
        echo \html_writer::end_tag('div');

        // The Rollover button.
        echo \html_writer::start_tag('div', array('class' => 'col-md-6'));
        echo \html_writer::tag('button', 'Rollover from a previous module', array(
            'id' => 'action-rollover',
            'class' => 'btn btn-default btn-lg btn-block',
            'data-id' => $course->id
        ));
        echo \html_writer::end_tag('div');

        echo \html_writer::end_tag('div');

        echo \html_writer::end_tag('div');
    }
}
