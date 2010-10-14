<?php

namespace app\extensions\helper;

class Search extends \lithium\template\Helper {

	public function pagination($results, $url = null) {
		$html = $this->_context->helper('html');
		$out = '<div class="pagination">';

		if (!empty($results->rows)) {
			$current = $results->skip + 1;
			$current_page = ceil($current / $results->limit);
			$count = $results->rows->count();
			$last = $results->skip + $count;
			$total = $results->total_rows;
			$pages = ceil($total / $results->limit);

			$out .= "<div class=\"total\">displaying {$current}-{$last} of {$total} result(s)</div>";

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