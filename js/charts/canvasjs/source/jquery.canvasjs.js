/**
* @preserve CanvasJS jQuery Chart Plugin - https://canvasjs.com/ 
* Copyright 2024 fenopix
* 
*/

(function ($, window, document, undefined) {

	$.fn.CanvasJSChart = function (options) {

		if (options) {

			var $el = this.first();
			var container = this[0];
			var chart = new CanvasJS.Chart(container, options);

			$el.children(".canvasjs-chart-container").data("canvasjsChartRef", chart);

			chart.render();

			return this;

		} else {

			return this.first().children(".canvasjs-chart-container").data("canvasjsChartRef");

		}
	}

}(jQuery, window, document));