$(function() {
	var dist = '*';
	var search = '';

	var setRolloverError = function() {
		$("#rollovercontainer").html("<p>Something went wrong! Please refresh and try again or contact your FLT.</p>");
	};

	// Refresh status loop.
	var statusLoop = function(rolloverid) {
		$.ajax({
			url: M.cfg.wwwroot + "/course/format/standardweeks/ajax/rollover.php",
			type: "POST",
			data: {
				'rolloverid': rolloverid,
				'action': 'status',
				'sesskey': M.cfg.sesskey
			},
			success: function(data) {
				var percent = data.progress;
				var status = data.status;

				if (status == 'rollover_error') {
					// Uh oh.
					setRolloverError();
					return;
				}

				if (status == 'rollover_complete') {
					var to = $("#rollovercontainer").attr('data-id');
					window.location = M.cfg.wwwroot + '/course/view.php?id=' + to;
				}

				if (percent > -1) {
					$("#rollovercontainer .progress-bar").attr('aria-valuenow', percent);
				}

				$("#rollovercontainer .progress-bar").html(status);

				setTimeout(function() {
					statusLoop(rolloverid)
				}, 5000);
			}
		});
	};

	var refreshList = function() {
		if (search.length < 2) {
			return;
		}
		
		var to = $("#rollovercontainer").attr('data-id');

		// Call for a list of possible rollovers.
		$.ajax({
			url: M.cfg.wwwroot + "/course/format/standardweeks/ajax/rolloversources.php",
			type: "GET",
			data: {
				'to': to,
				'dist': dist,
				'search': search,
				'sesskey': M.cfg.sesskey
			}
		}).done(function(data) {
			$('#rollover-options').html(data.result);

			// Do a rollover.
			$('#rollover-options td.action button').on('click', function(e) {
				e.preventDefault();

				var to = $("#rollovercontainer").attr('data-id');
				var from = $(this).attr('data-id');

				$("#rollovercontainer").html("<div class=\"text-center\"><i class=\"fa fa-spinner fa-spin\"></i></div>");

				// Schedule the rollover.
				$.ajax({
					url: M.cfg.wwwroot + "/course/format/standardweeks/ajax/rollover.php",
					type: "POST",
					data: {
						'to': to,
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

						$("#rollovercontainer").html('\
							<p>Please wait... your course is being rolled over.</p>\
							<div class="progress">\
								<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">\
									<i class="fa fa-spin fa-spinner"></i>\
								</div>\
							</div>\
						');

						statusLoop(data.rolloverid);
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
});