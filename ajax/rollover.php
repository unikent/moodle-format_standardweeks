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
$PAGE->set_url('/course/format/standardweeks/ajax/rollover.php');

require_login();
require_sesskey();

$action = required_param('action', PARAM_ALPHA);

// Right, this is where it gets complicated (actually it never gets complicated).
// We want an AJAX script that can be called, and re-called
// and the last message we send is one saying "Yep! All done."
// But we do a complete rollover here.
// The first request, we schedule a rollover.
// Then we respond "rollover_waiting".
// Then we will be constantly asked for a status, which we basically echo
// from the SHAREDB.
// Then, when the rollover is finish we echo back "rollover_complete" or
// "rollover_error".

// Easy! Just schedule it.
if ($action == 'schedule') {
	$to = required_param('to', PARAM_INT);
	$from = required_param('from', PARAM_INT);

	$from = $SHAREDB->get_record('shared_courses', array(
		'id' => $from
	), '*', MUST_EXIST);

	// Undo any existing, completed rollover.
	$course = new \local_rollover\Course($to);
	$course->undo_rollovers();

	$id = \local_rollover\Rollover::schedule($from->moodle_dist, $from->moodle_id, $to);
	if (!$id) {
	    print_error("Error creating rollover entry (unknown error).");
	}

	echo $OUTPUT->header();
	echo json_encode(array(
	    'rolloverid' => $id
	));
	die;
}

// They want a status update eh? I'll give em a status update....
if ($action == 'status') {
	$courseid = required_param('courseid', PARAM_INT);
	$course = new \local_rollover\Course($courseid);

	$progress = -1; // -1 means do not update.
	$status = '';
	switch ($course->get_status()) {
		case \local_rollover\Rollover::STATUS_SCHEDULED:
			$progress = 25;
			$status = 'Creating backup';
		break;

		case \local_rollover\Rollover::STATUS_BACKED_UP:
			$progress = 50;
			$status = 'Backup complete';
		break;

		case \local_rollover\Rollover::STATUS_RESTORE_SCHEDULED:
			$progress = 75;
			$status = 'Restore Scheduled';
		break;

		case \local_rollover\Rollover::STATUS_IN_PROGRESS:
			$status = 'Processing rollover';
		break;

		case \local_rollover\Rollover::STATUS_WAITING_SCHEDULE:
			$progress = 15;
			$status = 'Scheduling';
		break;

		case \local_rollover\Rollover::STATUS_COMPLETE:
			$progress = 100;
			$status = 'rollover_complete';
		break;

		case \local_rollover\Rollover::STATUS_ERROR:
			$status = 'rollover_error';
		break;

		case \local_rollover\Rollover::STATUS_DELETED:
		case \local_rollover\Rollover::STATUS_NONE:
		default:
			$status = 'no_rollover';
		break;

	}

	echo $OUTPUT->header();
	echo json_encode(array(
	    'progress' => $progress,
	    'status' => $status
	));
	die;
}