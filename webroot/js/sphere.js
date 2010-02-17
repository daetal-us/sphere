/**
 * This is the master object for the Sphere application
 *
 * This application requires jQuery 1.4.1
 */
var li3Sphere = {
	/**
	 * Options container for the application
	 *
	 * @param object options
	 */
	options: {},

	/**
	 * Setup method assigns `options` and performs some other tasks needed to startup.
	 *
	 * @param object options
	 * @return object this
	 */
	setup: function(options) {
		$.extend(this.options, options);
		this.setupSourcesMenu();
		return this;
	},

	/**
	 * Setup the Sources Menu
	 *
	 * This method seeks the `<span id="sources-icon" />` and assigns the
	 * `li3Sphere::toggleSourcesMenu()` method to its' `click` event.
	 *
	 * @return object this
	 */
	setupSourcesMenu: function() {
		$('#sources-icon').click(li3Sphere.toggleSourcesMenu);
		return this;
	},

	toggleSourcesMenu: function() {
		var menu = $('.nav.sources');
		var width = '2em';
		var leftMargin = '7em';
		var timespanLM = '12em';
		if (menu.hasClass('closed')) {
			width = '15em';
			leftMargin = '0';
			timespanLM = '18em';
		}
		$('.nav.timespan').animate({
			marginLeft: timespanLM
		}, 250);
		menu.animate({
			width: width,
			marginLeft: leftMargin
		}, 250, function() {
			$('.nav.sources').toggleClass('closed');
		})

	}
}