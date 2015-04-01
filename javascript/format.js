$(function() {
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
});

