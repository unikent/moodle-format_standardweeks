$(function() {
	var dist = '*';
	var search = '';

	var refreshList = function() {
		if (search.length < 2) {
			return;
		}

		// Call for a list of possible rollovers.
		$.ajax({
			url: M.cfg.wwwroot + "/course/format/standardweeks/ajax/rolloversources.php",
			type: "GET",
			data: {
				'dist': dist,
				'search': search,
				'sesskey': M.cfg.sesskey
			}
		}).done(function(data) {
			$('#rollover-options').html(data.result);
		});
	};

	$("#moodle-select input").on("change", function() {
		dist = $(this).attr('data-uri');
		refreshList();
	});

	$("#moodle-search").on("keyup", function() {
		search = $(this).val();
		refreshList();
	});

	// Do a rollover.
	$('#rollover-options td.action button').on('click', function() {
		var to = $("#rollovercontainer").attr('data-id');
		var from = $(this).attr('data-id');

		$("#rollovercontainer").html("<div class=\"text-center\"><i class=\"fa fa-spinner fa-spin\"></i></div>");

		$.ajax({
			url: M.cfg.wwwroot + "/course/format/standardweeks/ajax/rollover.php",
			type: "POST",
			data: {
				'to': to,
				'from': from,
				'action': 'schedule',
				'sesskey': M.cfg.sesskey
			},
			success: function() {
				window.location = window.location;
			}
		});
	});
});