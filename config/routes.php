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
Router::connect('/', array('controller' => 'posts', 'action' => 'index'));
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
	)
);

$sources = array(
	'sphere' => array(
		'Sphere',
		'sphere'
	),
	'lithium' => array(
		'Lithium Network',
		'(lithify.me OR rad-dev.org)'
	),
);

$tags = array(
	'questions' => array(
		'Questions...',
		'questions'
	),
	'apps' => array(
		'Lithium powered applications',
		'apps'
	),
	'press' => array(
		'Press',
		'press'
	),
	'tutorials' => array(
		'Tutorials',
		'tutorials'
	),
	'code' => array(
		'Code',
		'code'
	),
	'videos' => array(
		'Video',
		'videos'
	),
	'podcasts' => array(
		'Podcasts',
		'podcasts'
	),
	'slides' => array(
		'Slides',
		'slides'
	),
	'events' => array(
		'Events',
		'events'
	),
	'docs' => array(
		'Documentation',
		'docs'
	)
);

foreach ($timespans as $key => $options) {
	array_unshift($options, $key);
	Router::connect("/{$key}", array(
		'controller' => 'search', 'action' => 'filter', 'filter' => array('date' => $options)
	));
}
foreach ($sources as $key => $options) {
	array_unshift($options, $key);
	Router::connect("/{$key}", array(
		'controller' => 'search', 'action' => 'filter', 'filter' => array('source' => $options)
	));
	foreach ($timespans as $timeKey => $timeOptions) {
		array_unshift($timeOptions, $timeKey);
		Router::connect("/{$key}/{$timeKey}", array(
			'controller' => 'search', 'action' => 'filter', 'filter' => array(
				'source' => $options, 'date' => $timeOptions
			)
		));
	}
}

foreach ($tags as $key => $options) {
	array_unshift($options, $key);
	Router::connect("/{$key}", array(
		'controller' => 'search', 'action' => 'filter', 'filter' => array('tag' => $options)
	));
	foreach ($timespans as $timeKey => $timeOptions) {
		array_unshift($timeOptions, $timeKey);
		Router::connect("/{$key}/{$timeKey}", array(
			'controller' => 'search', 'action' => 'filter', 'filter' => array(
				'tag' => $options, 'date' => $timeOptions
			)
		));
	}
}

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

Router::connect('/docs', array('library' => 'li3_docs', 'controller' => 'browser'));
Router::connect('/docs/{:lib}/{:args}', array(
	'library' => 'li3_docs', 'controller' => 'browser', 'action' => 'view'
));

/**
 * Finally, connect the default routes.
 */
Router::connect('/{:controller}/{:action}/{:id:[0-9]+}.{:type}', array('id' => null));
Router::connect('/{:controller}/{:action}/{:id:[0-9]+}');
Router::connect('/{:controller}/{:action}/{:args}');

?>
