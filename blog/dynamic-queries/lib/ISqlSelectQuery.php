<?php

/**
 * Dynamically build an SQL SELECT query
 * @return ISqlSelectQuery for fluent interfaces.
 */
interface ISqlSelectQuery {
    public function select($fieldsSql);
    public function addSelect($fieldsSql);
    public function from($tableSql);
    public function addFrom($fieldsSql);
    public function join($type, $table, $alias, $condition);
    public function leftJoin($table, $alias, $condition);
    public function innerJoin($table, $alias, $condition);
    public function where($condition);
    public function addWhere($condition);
    public function orderBy($order);
    public function addOrderBy($order);
}


