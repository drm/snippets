<?php
/**
 * This code is provided as an example and therefore not considered stable. Use at your own risk.
 * Feel free to copy, modify and redistribute.
 *
 * @author Gerard van Helden <drm@melp.nl>
 */
/**
 * Base interface for the drill down filters
 */
interface IFilter {
    /**
     * @return bool
     */
    function isMultiple();

    /**
     * @param SqlSelectQuery $q     The query the filter is applied to
     * @param mixed $value          The filter's values
     * @return void
     */
    function addToQuery(SqlSelectQuery $q, $value);

    /**
     * @param SqlSelectQuery $q     The generated query
     * @param mixed $results        The result set the query generated
     * @return void
     */
    function setResults(SqlSelectQuery $q, $results);

    /**
     * Returns available options for the filter. Each option should have
     * a label and a value property.
     *
     * @return mixed
     */
    function getOptions();
}

