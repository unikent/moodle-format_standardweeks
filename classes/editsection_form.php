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
 * Kent course format.
 *
 * @package    format
 * @subpackage standardweeks
 * @copyright  2016 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_standardweeks;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot. '/course/editsection_form.php');

/**
 * Edit section form.
 *
 * @package    format
 * @subpackage standardweeks
 * @copyright  2016 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editsection_form extends \editsection_form
{
    public function definition() {
        global $DB, $PAGE;

        parent::definition();

        $mform  = $this->_form;
        $course = $this->_customdata['course'];

        $sql = <<<SQL
        SELECT
            cch.synopsis,
            cch.publicationssynopsis as publication_synopsis,
            cch.contacthours as contact_hours,
            cch.learningoutcome as learning_outcome,
            cch.methodofassessment as method_of_assessment,
            cch.preliminaryreading as preliminary_reading,
            cch.availability,
            cch.cost,
            cch.prerequisites,
            cch.progression,
            cch.restrictions
        FROM {connect_course} cc
        INNER JOIN {connect_course_handbook} cch
            ON cch.module_code=cc.module_code
        WHERE cc.mid=:id
        GROUP BY cch.module_code
        LIMIT 1
SQL;
        $result = $DB->get_record_sql($sql, array(
            'id' => $course->id
        ));

        if (!$result) {
            return;
        }

        $PAGE->requires->js_call_amd('format_standardweeks/editsection_form', 'init', array());

        $options = array(null => 'Choose a template');
        foreach ($result as $k => $v) {
            if (!empty($v)) {
                $options[$v] = str_replace('_', ' ', ucwords($k));
            }
        }

        $mform->addElement('select', 'summary_templates', get_string('summary_templates', 'format_standardweeks'), $options);
        $mform->addHelpButton('summary_templates', 'summary_templates', 'format_standardweeks');
        $mform->setType('summary_templates', PARAM_RAW);
    }
}
