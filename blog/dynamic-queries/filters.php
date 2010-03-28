<?php
/**
 * This code is provided as an example and therefore not considered stable. Use at your own risk.
 * Feel free to copy, modify and redistribute.
 *
 * @author Gerard van Helden <drm@melp.nl>
 */

require_once 'lib/SqlFilter.php';
require_once 'lib/OptionFilter.php';

/**
 * This generic SQL filter simplifies the SQL filter implementation
 * by defining an ID field, a label field and a table to select the options
 * from
 */
abstract class GenericSqlFilter extends SqlFilter {
    function __construct(Pdo $db, $idField, $labelField, $table = null) {
        parent::__construct($db);
        $this->_idField = $idField;
        $this->_labelField = $labelField;
        $this->_table = $table ?: $labelField;
    }


    protected function _initSelectQuery() {
        return SqlSelectQuery::create()
            ->from($this->_table)
            ->select($this->_idField . ' value')
            ->addSelect($this->_labelField . ' label')
        ;
    }
    

    final function setResults(SqlSelectQuery $q, $results) {
        $ret = false;
        $ids = array_filter(array_map(create_function('$r', 'return $r["car_id"];'), $results), 'is_numeric');
        if($ids) {
            $this->getQuery()
                ->addWhere(sprintf('%s.%s IN(%s)', $this->_table, $this->_idField, $this->getResultsInQuery($ids)));
            $ret = true;
        }
        return $ret;
    }


    /**
     * Should return the SELECT or comma-separated values for the remaining available filters,
     * based on the given array of car ids.
     *
     * @abstract
     * @param  $ids
     * @return void
     */
    abstract function getResultsInQuery($ids);
}


/**
 * Filter implementation for types of the cars
 */
class TypeFilter extends GenericSqlFilter {
    function __construct(Pdo $db) {
        parent::__construct($db, 'type_id', 'type');
    }
    

    function _initSelectQuery() {
        return parent::_initSelectQuery()
            ->addSelect('brand')
            ->innerJoin('brand', '', 'type.brand_id=brand.brand_id');
    }


    function getResultsInQuery($ids) {
        return sprintf(
            'SELECT type_id FROM car WHERE car_id IN(%s)',
            implode(',', $ids)
        );
    }


    function isMultiple() {
        return false;
    }


    function addToQuery(SqlSelectQuery $q, $value) {
        $q->addWhere(sprintf('car.type_id=%d', $value));
    }
}

/**
 * Filter implementation for brands of the cars
 */
class BrandFilter extends GenericSqlFilter {
    function __construct(Pdo $db) {
        parent::__construct($db, 'brand_id', 'brand');
    }

    function isMultiple() {
        return false;
    }

    function addToQuery(SqlSelectQuery $q, $value) {
        $q->addWhere(sprintf('type.brand_id=%d', $value));
    }


    function getResultsInQuery($ids) {
        return sprintf(
            'SELECT brand_id FROM car INNER JOIN type USING(type_id) WHERE car_id IN(%s)',
            implode(',', $ids)
        );
    }
}

/**
 * Filter implementation for accessories of the cars
 */
class AccessoryFilter extends GenericSqlFilter {
    function __construct(Pdo $db) {
        parent::__construct($db, 'accessory_id', 'accessory');
    }

    function isMultiple() {
        return true;
    }


    function addToQuery(SqlSelectQuery $q, $value) {
        foreach(array_filter($value, 'is_numeric') as $v) {
            $q->addWhere(sprintf('car.car_id IN(SELECT car_id FROM car_accessory WHERE accessory_id=%d)', $v));
        }
    }

    function getResultsInQuery($ids) {
        return sprintf('SELECT accessory_id FROM car_accessory WHERE car_id IN(%s)', implode(',', $ids));
    }
}

/**
 * Filter implementation for engine's fuel type of the cars
 */
class FuelFilter extends OptionFilter {
    function __construct() {
        parent::__construct(array(
            "diesel" => "Diesel",
            "gas" => "Gas"
        ));
    }


    function isMultiple() {
        return false;
    }


    function addToQuery(SqlSelectQuery $query, $value) {
        if(array_key_exists($value, $this->_options)) {
            $query->addWhere(sprintf("car.fuel='%s'", $value));
            echo $query;
        }
    }
}



