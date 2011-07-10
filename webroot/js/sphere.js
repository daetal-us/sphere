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
		this.setupMarkdownHelp();
		this.setupPosts();
		this.setupPost();
		this.setupComments();
		this.cleanDates();
		return this;
	},

	setupMarkdownHelp: function() {
		$("#add-post form textarea, #add-comment form textarea, li.comment form textarea").each(function(i,e) {
			var help = 	"<span title=\"headings!\"># header</span> " +
						"<span title=\"create italic text!\"><em>*italic*</em></span> " +
						"<span title=\"bold text - a classic!\"><strong>**bold**</strong></span> " +
						"<span title=\"render an arbitrary list of stuff!\">- unordered list</span> " +
						"<span title=\"create numerically ordered lists!\">1. ordered list</span> " +
						"<span title=\"render text as block-quotes!\"> blockquote</span> " +
						"<span title=\"create hyperlinks!\">[a link](http://example.com)</span> " +
						"<span title=\"render images!\">![image text](http://example.com/image.jpg)</span> " +
						"<span title=\"inline code!\"><code>`inline code`</code></span> " +
						"<pre title=\"share your awesome code!\"><code>\t// precede with `tabs` to render code</code></pre>" +
						"<aside>we attempt to convert stand-alone urls into links and leverage oEmbed linked content when possible.</aside>";
			var html = $("<a class=\"icon show-text toggle-markdown-help\" href=\"#\">markdown help</a><div class=\"markdown-help\" style=\"display:none;\"><h4>markdown help:</h4>"+help+"</div>");
			$(e).after(html);
			$(e).siblings('.toggle-markdown-help').click(function() {
				$(this).siblings('.markdown-help').show('normal');
				$(this).hide();
				return false;
			});
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
		var showText = false;
		if (menu.hasClass('closed')) {
			width = '15em';
			leftMargin = '0';
			timespanLM = '18em';
			showText = true;
		}
		if (showText) {
			menu.find('ul li a span').fadeIn('fast');
		} else {
			menu.find('ul li a span').fadeOut('fast');
		}
		$('.nav.timespan').animate({
			marginLeft: timespanLM
		}, 250);
		menu.animate({
			width: width,
			marginLeft: leftMargin
		}, 250, function() {
			$('.nav.sources').toggleClass('closed');
		});
	},

	setupComments: function() {
		$('.post li.comment').each(function(i,e) {
			$(e).mouseover(function(e) {
				$(this).addClass('focused');
				$(this).children('.post-comment-reply, .endorse-post-comment').show();
				e.stopPropagation();
			});
			$(e).mouseout(function(e) {
				$(this).removeClass('focused');
				$(this).children('.post-comment-reply, .endorse-post-comment').hide();
			});
		});

		//Comment Reply Links
		$('a.post-comment-reply:not(.inactive)').click(function() {
			var form = $(this).siblings('.post-comment-author-content').find('form');
			if (form.length == 0) {
				form = $("#add-comment form").clone(true).attr({
					action: $(this).attr('href')
				}).hide();
				$(this).siblings('.post-comment-author-content').find('.post-comment-content').after(form);
			}
			$(form).animate({
				opacity: "toggle"
			});
			return false;
		});

		//Thread View Links
		$('a.view-post-comment-replies').click(function() {
			var comments = $(this).siblings('ul.comments');
			if ($(this).hasClass('open')) {
				comments.fadeOut();
				$(this).removeClass('open');
				$(this).text($(this).data('original.text'));
			} else {
				comments.fadeIn();
				$(this).addClass('open');
				$(this).data('original.text', $(this).text());
				$(this).text('hide replies');
			}
			return false;
		});

		$('a.view-all-comments').click(function() {
			$('a.view-post-comment-replies').each(function(i,e) {
				if (!$(this).hasClass('open')) {
					$(this).siblings('ul.comments').fadeIn();
					$(this).addClass('open');
					$(this).data('original.text', $(this).text());
					$(this).text('hide replies');
				}
			});
			$(this).fadeOut('fast');
			return false;
		});
	},

	setupPosts: function() {
		$('.posts .post .meta').hide();
		$('.posts .post').bind({
			mouseenter: function() {
				clearTimeout($.data(this, "timeout"));
				var meta = $(this).find('.meta');
				$.data(this, "timeout", setTimeout(function() {
					meta.stop(true, true).show(250);
				}, 200));
			},
			mouseleave: function() {
				clearTimeout($.data(this, "timeout"));
				$(this).find('.meta').stop(true, true).hide(250);
			}
		})
	},

	setupPost: function() {
		this.Post.setup();
		// Post Comment Links
		$("a.post-comment:not(.inactive)").click(function() {
			$("#add-comment").fadeIn('fast');
			$(this).hide();
			return false;
		});
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
	},

	Post: {
		tags: [
			'apps','questions','press','tutorials','code','videos','podcasts','slides','events',
			'docs'
		],
		baseTag: null,
		timeout: null,
		setup: function() {
			$('#add-post .base-tags a').click(function() {
				if ($(this).hasClass('selected')) {
					li3Sphere.Post.deselect(this);
				} else {
					li3Sphere.Post.select(this);
				}
				return false;
			});
			$('#add-post #PostContent').keyup(function() {
				clearTimeout(li3Sphere.Post.timeout);
				$('#add-post #PostTitle').removeClass('loading');
				if ($('#add-post #PostTitle').val() == '') {
					li3Sphere.Post.timeout = setTimeout(function() {
						if ($('#add-post #PostTitle').val() == '') {
							var content = $('#add-post #PostContent').val();
							if (li3Sphere.Post.isUrl(content)) {
								li3Sphere.Post.getUrlTitle(content);
							}
						}
					}, 1500);
				}
			});
			$('#add-post #PostTags').keyup(function() {
				li3Sphere.Post.validateTags();
			});
		},
		validateTags: function() {
			// check for one of the base tags
			var tags = this.getTags();
			for (i in tags) {
				var tag = tags[i];
				if ($.inArray(tag, li3Sphere.Post.tags) != -1) {
					this.select('.base-tags a[data-tag='+tag+']');
				}
			}

			if (this.baseTag && $.inArray(this.baseTag, tags) === -1) {
				this.deselect('.base-tags a[data-tag='+this.baseTag+']');
			}
		},
		tag: function(tag) {
			var tags = this.getTags();
			// check current tags to see if its in there already, or another is
			if (!$.inArray(tag, tags)) {
				// new
			} else {
				// present already
			}
		},
		/**
		 * Get current post tags input, convert to array, and return unique tags
		 */
		getTags: function() {
			var tags = $('#PostTags').val();
			if (tags != '') {
				tags = tags.replace(/\,\s/g, ',').split(',');
			} else {
				tags = [];
			}
			return $.grep(tags, function(v, k) {
				return $.inArray(v, tags) === k;
			});
		},
		select: function(e) {
			var tag = $(e).attr('data-tag');
			if (this.baseTag && this.baseTag != tag) {
				this.deselect('.base-tags a[data-tag='+this.baseTag+']');
			}
			this.baseTag = tag;
			if ($.inArray(tag, this.getTags()) === -1) {
				$('#PostTags').val(tag + ',' + $('#PostTags').val());
			}
			$(e).addClass('selected');
			$('.base-tags a:not(.selected)').hide(1000);
		},
		deselect: function(e) {
			var tag = $(e).attr('data-tag');
			var tags = this.getTags();
			this.baseTag = null;

			for (i in tags) {
				if (tags[i] == tag) {
					tags.splice(i, 1);
				}
			}

			$('#PostTags').val(tags.toString());
			$(e).removeClass('selected');
			$('.base-tags a:not(.selected)').show(1000);
		},
		isUrl: function(string) {
			var pattern = new RegExp();
			pattern.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");
			return pattern.test(string);
		},
		getUrlTitle: function(url) {
			$('#add-post #PostTitle').addClass('loading');
			$.get(url, function(response) {
				var html = response.responseText.replace(/\n/g, ' ');
				var reg = new RegExp(/<title>(.*)<\/title>/i);
				var match = reg.exec(html);
				if (match && match[1]) {
					if ($('#add-post #PostTitle').val() == '') {
						$('#add-post #PostTitle').val($('<div/>').html(match[1]).text());
					}
				}
				$('#add-post #PostTitle').removeClass('loading');
			});
		}
	}
}