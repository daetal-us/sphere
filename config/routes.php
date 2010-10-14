<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \lithium\net\http\Router;
use \lithium\core\Environment;

/**
 * Uncomment the line below to enable routing for admin actions.
 * @todo Implement me.
 */
// Router::namespace('/admin', array('admin' => true));

/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'view', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.html.php)...
 */
Router::connect('/', array('controller' => 'search', 'action' => 'latest'));
Router::connect('/page:{:page}', array('controller' => 'search', 'action' => 'latest'));
Router::connect('/p/{:id}', array('controller' => 'posts', 'action' => 'comment'));
Router::connect('/p/{:id}/{:args}', array('controller' => 'posts', 'action' => 'comment'));
Router::connect('/users/login/{:return}', array('controller' => 'users', 'action' => 'login'));
Router::connect('/search', array('controller' => 'search', 'action' => 'index'));

/**
 * Timespans and Sources are shortcuts to searches, basically
 */
$timespans = array(
	'today' => array(
		' from today',
		date('Y-m-d')
	),
	'yesterday' => array(
		' from yesterday',
		date('Y-m-d', strtotime('yesterday'))
	),
	'1wk' => array(
		' in the last week',
		'['.date('Y-m-d', strtotime('-1 week')).' TO '.date('Y-m-d').']'
	),
	'2wk' => array(
		' in the last two weeks',
		'['.date('Y-m-d', strtotime('-2 weeks')).' TO '.date('Y-m-d').']'
	),
	'1mo' => array(
		' in the last month',
		'['.date('Y-m-d', strtotime('-1 month')).' TO '.date('Y-m-d').']'
	),
	'1yr' => array(
		' in the last year',
		'['.date('Y-m-d', strtotime('-1 year')).' TO '.date('Y-m-d').']',
	),
	'all' => array(
		' since the big bang',
		'[2010-02-09 TO '.date('Y-m-d').']',
	)
);

$tags = array(
	'questions' => 'Questions...',
	'apps' => 'Lithium powered applications',
	'press' => 'Press',
	'tutorials' => 'Tutorials',
	'code' => 'Code',
	'videos' => 'Videos',
	'podcasts' => 'Podcasts',
	'slides' => 'Slides',
	'events' => 'Events',
	'docs' => 'Documentation',
);

foreach ($timespans as $key => $options) {
	$title = "Posts" . $options[0];
	$date = $options[1];
	Router::connect("/{$key}/{:page}", array(
		'controller' => 'search', 'action' => 'filter'
	) + compact('date','title'));
	Router::connect("/{$key}", array(
		'controller' => 'search', 'action' => 'filter'
	) + compact('date','title'));
}

foreach ($tags as $tag => $title) {
	foreach ($timespans as $key => $options) {
		$date = $options[1];
		Router::connect("/{$tag}/{$key}/{:page}", array(
			'controller' => 'search', 'action' => 'filter', 'title' => $title . $options[0]
		) + compact('tag','date','page'));
		Router::connect("/{$tag}/{$key}", array(
			'controller' => 'search', 'action' => 'filter', 'title' => $title . $options[0]
		) + compact('tag','title','date'));
	}
	Router::connect("/{$tag}/{:page}", array(
		'controller' => 'search', 'action' => 'filter'
	) + compact('tag','title'));
	Router::connect("/{$tag}", array(
		'controller' => 'search', 'action' => 'filter'
	) + compact('tag','title'));
}

Router::connect('/s/{:q}/{:page}', array(
	'controller' => 'search', 'action' => 'index'
));

Router::connect('/t/{:tag}/{:page}', array(
	'controller' => 'search', 'action' => 'tag'
));
Router::connect('/t/{:tag}', array(
	'controller' => 'search', 'action' => 'tag'
));

/**
 * Connect the testing routes.
 */
if (!Environment::is('production')) {
	Router::connect('/test/{:args}', array('controller' => '\lithium\test\Controller'));
	Router::connect('/test', array('controller' => '\lithium\test\Controller'));
}

/**
 * Finally, connect the default routes.
 */
Router::connect('/{:controller}/{:action}/{:id:[0-9]+}.{:type}', array('id' => null));
Router::connect('/{:controller}/{:action}/{:id:[0-9]+}');
Router::connect('/{:controller}/{:action}/{:args}');

?>
