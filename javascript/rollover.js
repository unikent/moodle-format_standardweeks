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
			console.log(data);
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
});