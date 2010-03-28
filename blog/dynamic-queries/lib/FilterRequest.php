<?php

class FilterRequest {
    function __construct($availableFilters) {
        $this->_availableFilters = $availableFilters;
        $this->_activeFilters = array();
    }


    function setActiveFilters($activeFilters) {
        foreach($activeFilters as $name => $value) {
            if(!array_key_exists($name, $this->_availableFilters)) {
                throw new InvalidArgumentException("Unknown filter $name");
            }
            if($this->getFilter($name)->isStackable()) {
                $this->_activeFilters[$name] = (array)$value;
            } else {
                $this->_activeFilters[$name] = $value;
            }
        }
    }


    function getFilter($name) {
        return $this->_availableFilters[$name];
    }


    function isFilterActive($name, $value) {
        $ret = false;
        if(array_key_exists($name, $this->_activeFilters)) {
            if($this->getFilter($name)->isStackable()) {
                $ret = in_array($value, $this->_activeFilters[$name]);
            } else {
                $ret = ($value == $this->_activeFilters[$name]);
            }
        }
        return $ret;
    }


    function getFilterParams($name, $value) {
        if($this->isFilterActive($name, $value)) {
            $ret = $this->getRemoveFilterParams($name, $value);
        } else {
            $ret = $this->getAddFilterParams($name, $value);
        }

        return $ret;
    }


    function getRemoveFilterParams($name, $value) {
        $current = $this->_activeFilters;
        if($this->getFilter($name)->isStackable()) {
            unset($current[$name][array_search($value, $current[$name])]);
        } else {
            unset($current[$name]);
        }

        return $current;
    }


    function getAddFilterParams($name, $value) {
        $current = $this->_activeFilters;
        if($this->getFilter($name)->isStackable()) {
            $current[$name][]= $value;
        } else {
            $current[$name] = $value;
        }

        return $current;
    }

    function getAvailableFilters() {
        return $this->_availableFilters;
    }


    function getActiveFilters() {
        return $this->_activeFilters;
    }
}