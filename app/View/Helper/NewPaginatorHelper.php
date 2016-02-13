<?php

App::import('Helper', 'PaginatorHelper');
class NewPaginatorHelper extends PaginatorHelper {


    /**
     * Generates a sorting link. Sets named parameters for the sort and direction.  Handles
     * direction switching automatically.
     *
     * ### Options:
     *
     * - `escape` Whether you want the contents html entity encoded, defaults to true
     * - `model` The model to use, defaults to PaginatorHelper::defaultModel()
     *
     * @param string $title Title for the link.
     * @param string $key The name of the key that the recordset should be sorted.  If $key is null
     *   $title will be used for the key, and a title will be generated by inflection.
     * @param array $options Options for sorting link. See above for list of keys.
     * @return string A link sorting default by 'asc'. If the resultset is sorted 'asc' by the specified
     *  key the returned link will sort by 'desc'.
     * @access public
     */
    function sort($title, $key = null, $options = array()) {
	    $options = array_merge(array('url' => array(), 'model' => null), $options);
	    $url = $options['url'];
	    unset($options['url']);

	    if (empty($key)) {
		    $key = $title;
		    $title = __(Inflector::humanize(preg_replace('/_id$/', '', $title)));
	    }
	    $dir = isset($options['direction']) ? $options['direction'] : 'asc';
	    unset($options['direction']);

	    $sortKey = $this->sortKey($options['model']);
	    $defaultModel = $this->defaultModel();
	    $isSorted = (
		    $sortKey === $key ||
		    $sortKey === $defaultModel . '.' . $key ||
		    $key === $defaultModel . '.' . $sortKey
	    );

	    if ($isSorted) {
		    $dir = $this->sortDir($options['model']) === 'asc' ? 'desc' : 'asc';
		    $class = $dir === 'asc' ? 'desc' : 'asc';
		    if (!empty($options['class'])) {
			    $options['class'] .= ' ' . $class;
		    } else {
			    $options['class'] = $class;
		    }
	    }
	    if (is_array($title) && array_key_exists($dir, $title)) {
		    $title = $title[$dir];
	    }

	    $url = array_merge(array('sort' => $key, 'direction' => $dir), $url, array('order' => null));
	    return $this->link($title, $url, $options);
    }
}
?>