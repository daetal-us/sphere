<?php

namespace app\extensions\helper;

class Search extends \lithium\template\Helper {

	public function pagination($results, $options = array()) {
		$defaults = array(
			'url' => null,
			'page' => 1,
			'limit' => 0,
			'count' => 0
		);
		extract($options + $defaults);

		$html = $this->_context->helper('html');
		$out = '<div class="pagination">';

		if ($count) {
			$current = $limit * $page - ($limit - 1);
			$current_page = $page;
			$last = ($limit * $page > $count) ? $count : $limit * $page;
			$pages = ceil($count / $limit);

			$out .= "<div class=\"total\">displaying {$current}-{$last} of {$count} result(s)</div>";

			if (empty($url)) {
				$url = array('controller' => 'search', 'action' => 'index', 'q' => $results->q);
			}

			if ($pages > 1) {
				$links = array();
				if ($pages > 5) {
					$links[]  = $html->link('first', array('page' => 1) + $url, array('class' => 'first'));
				}
				for ($page=1;$page<=$pages;$page++) {
					$class = 'page';
					if ($page == $current_page) {
						$class .= " active";
					}
					$links[] = $html->link($page, compact('page') + $url, compact('class'));
				}

				if ($pages > 5) {
					$links[] = $html->link('last', array('page' => $pages) + $url, array('class' => 'last'));
				}
				$out .= '<div class="pages">' . implode($links, " \n") . '</div>';
			}
		}
		$out .= '</div>';
		return $out;
	}

}

?>