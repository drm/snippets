<?php
require_once 'lib/SqlSelectQuery.php';

class CarSelectQuery extends SqlSelectQuery {
    function __construct() {
        parent::__construct();
        $this
            ->select('car.car_id, title, type, brand, fuel, price, GROUP_CONCAT(accessory SEPARATOR ", ") accessories')
            ->groupBy('car_id, title, type, brand, fuel, price')
            ->from('car')
            ->innerJoin('type', '', 'car.type_id=type.type_id')
            ->innerJoin('brand', '', 'type.brand_id=brand.brand_id')
            ->leftJoin('car_accessory', 'ca', 'car.car_id=ca.car_id')
            ->leftJoin('accessory', '', 'ca.accessory_id=accessory.accessory_id')
        ;
    }
}

