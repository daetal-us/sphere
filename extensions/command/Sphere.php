<?php

namespace app\extensions\command;

use \app\models\SphereView;
use \lithium\data\Connections;

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
		$this->header('Lithium Sphere Installer');
		$this->hr();

		if (!$this->_database()) {
			$this->out('The database could not be created.');
			return false;
		}

		foreach (SphereView::$views as $key => $view) {
			if (!$existing = SphereView::first($view['id'])) {
				$view = SphereView::create($view);
				$view->save();
				if ($this->_check($view->model(), $key)) {
					$this->out("`{$view->id}` created.");
				}
			} else {
				$this->out("View `{$existing->id}` already exists.");
				$choice = $this->in('Would you like to update this view?', array(
					'choices' => array('n', 'y')
				));
				if ($choice == 'y') {
					$existing->set($view + $existing->to('array'));
					$existing->save();
					if ($this->_check($existing->model(), $key)) {
						$this->out("`{$existing->id}` updated.");
					}
				} else {
					$this->out("`{$existing->id}` skipped!");
				}
			}
			$this->out();
		}
	}

	protected function _database() {
		$connection = SphereView::meta('connection');
		if (!$config = Connections::config($connection)) {
			$this->out("Database connection `{$connection}` is not configured.");
			return false;
		}
		$database = $config['database'];
		$this->out("Verifying database `{$database}`...");
		$this->out();
		Connections::get($connection)->describe($database);
		return true;
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