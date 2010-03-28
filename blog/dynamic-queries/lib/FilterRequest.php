<?php
/**
 * This code is provided as an example and therefore not considered stable. Use at your own risk.
 * Feel free to copy, modify and redistribute.
 *
 * @author Gerard van Helden <drm@melp.nl>
 */
/**
 * The Filter request maps filters to internal values, and determines url parameters for each consecutive filter.
 */
class FilterRequest {
    /**
     * Contains available filters mapped by name
     *
     * @var array
     */
    private $_availableFilters = array();

    /**
     * Contains active filter values mapped by name
     *
     * @var array
     */
    private $_activeFilters = array();

    /**
     *
     * @param  array $availableFilters Array of IFilter implementations
     */
    function __construct($availableFilters) {
        $this->_availableFilters = $availableFilters;
        $this->_activeFilters = array();
    }



    /**
     * Set the currently active filters from the request.
     *
     * @throws InvalidArgumentException
     * @param  $activeFilters
     * @return void
     */
    function setActiveFilters($activeFilters) {
        foreach($activeFilters as $name => $value) {
            if(!array_key_exists($name, $this->_availableFilters)) {
                throw new InvalidArgumentException("Unknown filter $name");
            }
            if($this->getFilter($name)->isMultiple()) {
                $this->_activeFilters[$name] = (array)$value;
            } else {
                $this->_activeFilters[$name] = $value;
            }
        }
    }


    /**
     * Returns a filter by its name
     *
     * @param  $name
     * @return IFilter
     */
    function getFilter($name) {
        return $this->_availableFilters[$name];
    }


    /**
     * Checks if the given value is a currently active filter for the given name.
     *
     * @param  string $name The filter's name
     * @param  mixed  $value The value to check for
     * @return bool
     */
    function isFilterActive($name, $value) {
        $ret = false;
        if(array_key_exists($name, $this->_activeFilters)) {
            if($this->getFilter($name)->isMultiple()) {
                $ret = in_array($value, $this->_activeFilters[$name]);
            } else {
                $ret = ($value == $this->_activeFilters[$name]);
            }
        }
        return $ret;
    }


    /**
     * Returns the parameters to pass in an URL for the given filter name and value.
     * If the current name/value combination is active, the filter is removed from the currently
     * active filters, otherwise it is added.
     *
     * @param  $name
     * @param  $value
     * @return array
     */
    function getFilterParams($name, $value) {
        if($this->isFilterActive($name, $value)) {
            $ret = $this->getRemoveFilterParams($name, $value);
        } else {
            $ret = $this->getAddFilterParams($name, $value);
        }

        return $ret;
    }


    /**
     * Returns the currently active filters with the given parameter name and value removed.
     * The filter's isMultiple setting is considered. 
     *
     * @param  $name
     * @param  $value
     * @return array
     */
    function getRemoveFilterParams($name, $value) {
        $current = $this->_activeFilters;
        if($this->getFilter($name)->isMultiple()) {
            unset($current[$name][array_search($value, $current[$name])]);
        } else {
            unset($current[$name]);
        }

        return $current;
    }


    /**
     * Returns the currently active filters with the given parameter name and value added
     * The filter's isMultiple setting is considered.
     *
     * @param  $name
     * @param  $value
     * @return array
     */
    function getAddFilterParams($name, $value) {
        $current = $this->_activeFilters;
        if($this->getFilter($name)->isMultiple()) {
            $current[$name][]= $value;
        } else {
            $current[$name] = $value;
        }

        return $current;
    }


    /**
     * Returns all available filters
     * 
     * @return array
     */
    function getAvailableFilters() {
        return $this->_availableFilters;
    }


    /**
     * Returns all currently active filters
     * @return array
     */
    function getActiveFilters() {
        return $this->_activeFilters;
    }
}