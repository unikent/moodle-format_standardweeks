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

/*
 * @package    format_standardweeks
 * @copyright  2015 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * @module format_standardweeks/format
  */
define([], function() {
    return {
        init: function() {
			$("#action-fresh").on("click", function() {
				$("#formatbuttons").html("<div class=\"text-center\"><i class=\"fa fa-spinner fa-spin\"></i></div>");

				var id = $(this).attr('data-id');

				$.ajax({
					url: M.cfg.wwwroot + "/course/format/standardweeks/ajax/fresh.php",
					type: "POST",
					data: {
						'id': id,
						'sesskey': M.cfg.sesskey
					},
					success: function() {
						window.location = window.location;
					}
				});
			});

			$("#action-rollover").on("click", function() {
				var id = $(this).attr('data-id');
				window.location = M.cfg.wwwroot + "/course/format/standardweeks/rollover.php?id=" + id;
			});

			$(".cnid-dismiss").on("click", function() {
				var id = $(this).attr('data-id');
				$.ajax({
					url: M.cfg.wwwroot + "/course/format/standardweeks/ajax/dismiss.php",
					type: "POST",
					data: {
						'id': id,
						'sesskey': M.cfg.sesskey
					}
				});
			});
        }
    };
});