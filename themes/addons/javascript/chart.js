(function($) {
	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);

	function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('date', 'Date');
		data.addColumn('number', 'New Submissions');

		$('.chart-data').each(function() {
			data.addRow([
				new Date($(this).data('x')), $(this).data('y')
			]);
		});

		var options = {
			title : '',
			pointSize : 3,
			colors : ['#058dc7'],
			legend : {position: 'top', alignment: 'center'},
			vAxis : {minValue: 0}
		};

		var chart = new google.visualization.LineChart(document.getElementById('chart-canvas'));
		chart.draw(data, options);
	}
})(jQuery);