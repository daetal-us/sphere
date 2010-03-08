<?php

namespace app\extensions\command;

use \app\models\SphereView;

/**
 * Command to assist in setup and management of Sphere
 *
 */
class Sphere extends \lithium\console\Command {

	/**
	 * Run the install method to create database and views
	 *
	 * @return boolean
	 */
	public function install() {
		$this->header('Sphere');
		foreach (SphereView::$views as $key => $view) {
			SphereView::create($view)->save();
			$this->_check('\app\models\SphereView', $key);
		}
	}

	protected function _check($model, $name = null) {
		if (!$name) {
			return null;
		}

		$view = $model::find("_design/{$name}");

		if (!empty($view->reason)) {
			switch($view->reason) {
				case 'no_db_file':
					$this->out(array(
						'Database does not exist.',
						'Please make sure CouchDB is running and refresh to try again.'
					));
				break;
				case 'missing':
					$this->out(array(
						'Database created.', 'Design views were not created.',
						'Please run the command again.'
					));
				break;
			}
		}
		if (isset($view->id) && $view->id == "_design/{$name}") {
			$this->out("{$model} {$name} view created.");
			return true;
		}
	}
}

?>