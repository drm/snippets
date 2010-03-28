<?php
/**
 * This code is provided as an example and therefore not considered stable. Use at your own risk.
 * Feel free to copy, modify and redistribute.
 *
 * @author Gerard van Helden <drm@melp.nl>
 */

/**
 * IFilter implementation for using predefined options.
 */

require_once 'IFilter.php';

abstract class OptionFilter implements IFilter {
    protected $_options = null;

    function __construct($options) {
        $this->_options = $options;
    }


    function getOptions() {
        $ret = array();
        foreach($this->_options as $value => $label) {
            $ret[]= compact('value', 'label');
        }
        return $ret;
    }



    function setResults(SqlSelectQuery $q, $results) {
        $available = array();
        foreach($results as $r) {
            $available[] = $r['fuel'];
        }

        foreach(array_diff(array_keys($this->_options), $available) as $unavailable) {
            unset($this->_options[$unavailable]);
        }
    }
}
