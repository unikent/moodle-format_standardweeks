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
 * This file contains main class for the course format Weeks
 *
 * @package    format
 * @subpackage standardweeks
 * @copyright  2015 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/format/lib.php');
require_once($CFG->dirroot . '/course/format/weeks/lib.php');

/**
 * Main class for the Weeks course format
 *
 * @package    format
 * @subpackage standardweeks
 * @copyright  2015 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_standardweeks extends format_weeks {
    /**
     * Return an instance of moodleform to edit a specified section
     *
     * Default implementation returns instance of editsection_form that automatically adds
     * additional fields defined in {@link format_base::section_format_options()}
     *
     * Format plugins may extend editsection_form if they want to have custom edit section form.
     *
     * @param mixed $action the action attribute for the form. If empty defaults to auto detect the
     *              current url. If a moodle_url object then outputs params as hidden variables.
     * @param array $customdata the array with custom data to be passed to the form
     *     /course/editsection.php passes section_info object in 'cs' field
     *     for filling availability fields
     * @return moodleform
     */
    public function editsection_form($action, $customdata = array()) {
        if (!array_key_exists('course', $customdata)) {
            $customdata['course'] = $this->get_course();
        }

        return new \format_standardweeks\editsection_form($action, $customdata);
    }
}
