<?php
/**
 * This code is provided as an example and therefore not considered stable. Use at your own risk.
 * Feel free to copy, modify and redistribute.
 *
 * @author Gerard van Helden <drm@melp.nl>
 */

require_once 'IFilter.php';

/**
 * Abstract base class for filters using SQL tables
 */
abstract class SqlFilter implements IFilter {
    /**
     * @var SqlSelectQuery|string
     */
    private $_query = null;

    /**
     * @var Pdo
     */
    private $_db = null;

    
    /**
     * Constructor stub
     */
    function __construct(Pdo $db) {
        $this->_db = $db;
    }

    /**
     * @see IFilter::getOptions()
     */
    function getOptions() {
        return $this->_db->query((string)$this->getQuery())->fetchAll(Pdo::FETCH_ASSOC);
    }


    /**
     * Returns the select query for this filter
     */
    final function getQuery() {
        if(is_null($this->_query)) {
            $this->_query = $this->_initSelectQuery();
        }
        return $this->_query;
    }


    /**
     * Initializes the select query. Hooks into getQuery()
     */
    abstract protected function _initSelectQuery();
}

