$(function() {
	var dist = '*';
	var search = '';
	var courseid = $("#rollovercontainer").attr('data-id');

	var setRolloverError = function() {
		$("#rollovercontainer").html("<p>Something went wrong! Please refresh and try again or contact your FLT.</p>");
	};

	var getStatus = function(callback) {
		$.ajax({
			url: M.cfg.wwwroot + "/course/format/standardweeks/ajax/rollover.php",
			type: "POST",
			data: {
				'courseid': courseid,
				'action': 'status',
				'sesskey': M.cfg.sesskey
			},
			success: callback
		});
	}

	// Refresh status loop.
	// Constantly checks for rollover status updates.
	var statusLoop = function() {
		getStatus(function(data) {
			var percent = data.progress;
			var status = data.status;

			if (status == 'rollover_error') {
				// Uh oh.
				setRolloverError();
				return;
			}

			if (status == 'rollover_complete') {
				window.location = M.cfg.wwwroot + '/course/view.php?id=' + courseid;
				status = "Rollover complete!";
			}

			if (percent > -1) {
				$("#rollovercontainer .progress-bar")
					.attr('aria-valuenow', percent)
					.css('width', percent + '%');
			}

			$("#rollovercontainer .progress-bar").html(status);

			setTimeout(function() {
				statusLoop()
			}, 5000);
		});
	};

	// Begin checking the status every 5 seconds.
	var beginStatusLoop = function() {
		$("#rollovercontainer").html('\
			<p>Please wait... your course is being rolled over.</p>\
			<div class="progress">\
				<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">\
					<i class="fa fa-spin fa-spinner"></i>\
				</div>\
			</div>\
		');

		statusLoop();
	};

	var refreshList = function() {
		if (search.length < 2) {
			return;
		}

		// Call for a list of possible rollovers.
		$.ajax({
			url: M.cfg.wwwroot + "/course/format/standardweeks/ajax/rolloversources.php",
			type: "GET",
			data: {
				'to': courseid,
				'dist': dist,
				'search': search,
				'sesskey': M.cfg.sesskey
			}
		}).done(function(data) {
			$('#rollover-options').html(data.result);

			// Do a rollover.
			$('#rollover-options td.action button').on('click', function(e) {
				e.preventDefault();

				var from = $(this).attr('data-id');

				$("#rollovercontainer").html("<div class=\"text-center\"><i class=\"fa fa-spinner fa-spin\"></i></div>");

				// Schedule the rollover.
				$.ajax({
					url: M.cfg.wwwroot + "/course/format/standardweeks/ajax/rollover.php",
					type: "POST",
					data: {
						'to': courseid,
						'from': from,
						'action': 'schedule',
						'sesskey': M.cfg.sesskey
					},
					success: function(data) {
						if (typeof data.rolloverid == 'undefined') {
							// Uh oh.
							setRolloverError();
							return;
						}

						beginStatusLoop();
					}
				});

				return false;
			});
		});
	};

	$("#moodle-select input").on("change", function() {
		dist = $(this).attr('data-name');
		refreshList();
	});

	$("#moodle-search").on("keyup", function() {
		search = $(this).val();
		refreshList();
	});

	// On page load, check we aren't in the middle of a rollover.
	getStatus(function(data) {
		if (data.status == 'no_rollover') {
			return;
		}

		beginStatusLoop();
	});
});