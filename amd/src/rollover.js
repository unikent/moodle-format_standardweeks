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
  * @module format_standardweeks/rollover
  */
define(['core/ajax', 'core/notification'], function(ajax, notification) {
    var dist = '*';
    var search = '';
    var courseid = $("#rollovercontainer").attr('data-id');

    var setRolloverError = function() {
        $("#rollovercontainer").html("<p>Something went wrong! Please refresh and try again or contact your FLT.</p>");
    };

    // Refresh status loop.
    // Constantly checks for rollover status updates.
    var statusLoop = function() {
        var promises = ajax.call([{
            methodname: 'get_rollover_status',
            args: {
                courseid: courseid
            }
        }]);

        promises[0].done(function(response) {
            var percent = response.progress;
            var status = response.status_str;

            if (response.status_code == 'rollover_error') {
                // Uh oh.
                setRolloverError();
                return;
            }

            // 2 = STATUS_COMPLETE
            if (response.status_code == 2) {
                window.location = M.cfg.wwwroot + '/course/view.php?id=' + courseid;
            }

            if (response.progress > -1) {
                $("#rollovercontainer .progress-bar")
                    .attr('aria-valuenow', response.progress)
                    .css('width', response.progress + '%');
            }

            $("#rollovercontainer .progress-bar").html(response.status_str);

            setTimeout(function() {
                statusLoop()
            }, 10000);
        });

        promises[0].fail(notification.exception);
    };

    // Begin checking the status every 10 seconds.
    var beginStatusLoop = function() {
        $("#rollovercontainer").html('\
            <p class="lead">Please wait... your course is being rolled over.</p>\
            <div class="row">\
                <div class="col-xs-8 col-xs-offset-2">\
                    <div class="progress">\
                        <div class="progress-bar" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100" style="width: 15%">\
                            <i class="fa fa-spin fa-spinner"></i>\
                        </div>\
                    </div>\
                </div>\
            </div>\
            <p>Note: you can safely close this page and check back later.</p>\
        ');

        statusLoop();
    };

    // Schedule a rollover..
    var scheduleRollover = function(source, target) {
        var promises = ajax.call([{
            methodname: 'schedule_rollover',
            args: {
                source: source,
                target: target
            }
        }]);

        promises[0].done(function(response) {
            beginStatusLoop();
        });

        promises[0].fail(function(response) {
            setRolloverError();
        });
    };

    var refreshList = function() {
        if (search.length < 2) {
            return;
        }

        var promises = ajax.call([{
            methodname: 'search_rollover_source_list',
            args: {
                search: search,
                target: courseid,
                dist: dist
            }
        }]);

        promises[0].done(function(response) {
            // Build the table.
            var contents = '';
            $.each(response, function(i, result) {
                var button = '<button class="btn btn-default" data-id="' + result['id'] + '">Rollover</button>';
                contents += '<tr><td>' + result['moodle_dist'] + '</td><td>' + result['shortname'] + '</td><td>' + result['fullname'] + '</td><td>' + button + '</td></tr>';
            });
            $('#rollover-options').html('<table><thead><tr><th>Moodle</th><th>Shortname</th><th>Fullname</th><th>Action</th></tr></thead><tbody>'+contents+'</tbody></table>');

            // Do a rollover.
            $('#rollover-options td.action button').on('click', function(e) {
                e.preventDefault();

                $("#rollovercontainer").html("<div class=\"text-center\"><i class=\"fa fa-spinner fa-spin\"></i></div>");

                // Schedule the rollover.
                scheduleRollover($(this).attr('data-id'), courseid);

                return false;
            });
        });

        promises[0].fail(notification.exception);
    };

    return {
        init: function() {
            $("#moodle-select a").on("click", function(e) {
                e.preventDefault();

                $('#moodle-select').dropdown('toggle');

                dist = $(this).attr('data-name');
                $('#moodle-select-button').html($(this).text() + ' <span class="caret"></span>');

                refreshList();

                return false;
            });

            $("#moodle-search").on("keyup", function() {
                search = $(this).val();
                refreshList();
            });

            // On page load, check we aren't in the middle of a rollover.
            if ($("#currentrollover").length > 0) {
                beginStatusLoop();
            }
        }
    };
});
