/**
 * Found the core of this script on John Resig's blog in the comments, by Dean Landoldt. Made a
 * number of tweaks to work for what I need, and encapsulated the whole thing. Muchas gracias.
 *
 * @see http://deanlandolt.com/
 * @see http://ejohn.org/
 */

var PrettyDate = {
	formats: [
		[60, 'just now', "now"], // 60
		[120, '1 minute ago', '1 minute from now'], // 60*2
		[3600, 'minutes', 60], // 60*60, 60
		[7200, '1 hour ago', '1 hour from now'], // 60*60*2
		[86400, 'hours', 3600], // 60*60*24, 60*60
		[172800, 'yesterday', 'tomorrow'], // 60*60*24*2
		[604800, 'days', 86400], // 60*60*24*7, 60*60*24
		[1209600, 'last week', 'next week'], // 60*60*24*7*4*2
		[2419200, 'weeks', 604800], // 60*60*24*7*4, 60*60*24*7
		[4838400, 'last month', 'next month'], // 60*60*24*7*4*2
		[29030400, 'months', 2419200], // 60*60*24*7*4*12, 60*60*24*7*4
		[58060800, 'last year', 'next year'], // 60*60*24*7*4*12*2
		[2903040000, 'years', 29030400], // 60*60*24*7*4*12*100, 60*60*24*7*4*12
		[5806080000, 'last century', 'next century'], // 60*60*24*7*4*12*100*2
		[58060800000, 'centuries', 2903040000] // 60*60*24*7*4*12*100*20, 60*60*24*7*4*12*100
	],

	convert: function(timestamp){
		var now = new Date().getTime();
		var then = new Date().setTime(timestamp * 1000);
		var seconds = (now - then) / 1000;
		var token = 'ago', list_choice = 1;
		if (seconds < 0) {
			seconds = Math.abs(seconds);
			token = 'from now';
			list_choice = 2;
		}
		var i = 0, format;
		while (format = this.formats[i++]) if (seconds < format[0]) {
			if (typeof format[2] == 'string')
				return format[list_choice];
			else
				return Math.floor(seconds / format[2]) + ' ' + format[1] + ' ' + token;
		}
		return 'Y2K';
	}
}