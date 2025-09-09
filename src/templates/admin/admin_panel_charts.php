<div class="chart-stuff">
	<canvas id="countryPieChart" width="400px" height="400px"></canvas>
</div>
<style>
	.chart-stuff{
		display: flex;
	}

	#countryPieChart {
		max-width: 400px;
		max-height: 400px;
		width: 100%;
		height: auto;
		margin: 0 auto;
	}
</style>
<script>
	function generateColors(num) {
		const colors = [];
		const hueStep = 360 / num;

		for (let i = 0; i < num; i++) {
			const hue = i * hueStep;
			colors.push(`hsl(${hue}, 70%, 60%)`);
		}

		return colors;
	}

	document.addEventListener('DOMContentLoaded', function () {
		const ctx = document.getElementById('countryPieChart').getContext('2d');
		const chartLabels = <?php echo json_encode(array_keys($country_counts)); ?>;
		const chartData = <?php echo json_encode(array_values($country_counts)); ?>;
		const backgroundColors = generateColors(chartLabels.length);
		const data = {
			labels: chartLabels,
			datasets: [{
				label: 'Country Distribution',
				data: chartData,
				backgroundColor: backgroundColors
			}]
		};

		const config = {
			type: 'pie',
			data: data,
			options: {
				responsive: true,
				plugins: {
					legend: {
						position: 'left'
					},
					tooltip: {
						callbacks: {
							label: function (context) {
								const label = context.label || '';
								const value = context.parsed;
								return `${label}: ${value}`;
							}
						}
					}
				}
			}
		};

		new Chart(ctx, config);
	});
	</script>