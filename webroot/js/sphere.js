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
	 * Reserved for Showdown object
	 * @param object Showdown
	 * @see http://attacklab.net/showdown/
	 */
	Showdown: {},

	/**
	 * Setup method assigns `options` and performs some other tasks needed to startup.
	 *
	 * @param object options
	 * @return object this
	 */
	setup: function(options) {
		$.extend(this.options, options);
		this.setupSourcesMenu();
		//this.setupCommentThreads();
		this.setupShowdownHelp();
		this.setupShowdown();
		this.setupComments();
		this.cleanDates();
		return this;
	},

	/**
	 * Setup Showdown and convert applicable element contents.
	 * @return object this
	 */
	setupShowdown: function() {
		this.Showdown = new Showdown.converter();
		$('pre.markdown').each(function(i,e) {
			$(e).replaceWith(
				li3Sphere.Showdown.makeHtml($(e).text())
			);
		});
		return this;
	},

	setupShowdownHelp: function() {
		$("#add-comment form, li.comment form").each(function(i,e) {
			var help = 	"# header &nbsp; &nbsp; " +
						"<em>*italic*</em> &nbsp; &nbsp; " +
						"<strong>**bold**</strong> &nbsp; &nbsp; " +
						"- unordered list &nbsp; &nbsp; " +
						"1. ordered list &nbsp; &nbsp; " +
						"> blockquote &nbsp; &nbsp; " +
						"[a link](http://example.com) &nbsp; &nbsp; " +
						"![image text](http://example.com/image.jpg) &nbsp; &nbsp; " +
						"<code>`code`</code> &nbsp; &nbsp; " +
						"<pre><code>{{{ code }}}</code></pre>";
			var html = $("<div class=\"markdown-help\"><h4>markdown help:</h4>"+help+"</div>");
			$(e).append(html);
		});
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
	},

	setupComments: function() {
		$('.post li.comment').each(function(i,e) {
			$(e).mouseover(function(e) {
				$(this).addClass('focused');
				$(this).children('.post-comment-reply').show();
				e.stopPropagation();
			});
			$(e).mouseout(function(e) {
				$(this).removeClass('focused');
				$(this).children('.post-comment-reply').hide();
			});
		});
	},

	setupCommentThreads: function() {
		$('.post > ul.comments > li.comment > ul.comments').each(function(i,e) {
			var reply = $(e).siblings('a.post-comment-reply');
			var viewThread = $('<a class="post-comment-thread">thread</a>').click(function() {
				$(this).siblings('ul.comments').animate({
					opacity: "toggle"
				});
			});
			reply.after(viewThread);
			$(e).hide();
		})
	},

	/**
	 * This method requires instantiation of PrettyDate. Using it, it converts the time to a
	 * relative time (i.e. `3 minutes ago`).
	 *
	 * To utilize this conversion, this method seeks out elements as follows:
	 * `<element class="pretty-date">text <element class="timestamp">timestamp</element></element>`
	 *
	 * Then, replaces the text with the relative time. Then, setting a timeout for 15 seconds to run
	 * again.
	 *
	 */
	cleanDates: function() {
		$('.pretty-date').each(function(i,e) {
			var timestamp = $(e).children('.timestamp');
			var time = PrettyDate.convert(timestamp.text());
			$(e).html(time).append(timestamp);
		});
		window.setTimeout(li3Sphere.cleanDates, 15000);
	}
}