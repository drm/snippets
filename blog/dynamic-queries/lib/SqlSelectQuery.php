<?php
require_once 'ISqlSelectQuery.php';


/**
 * OO representation of an SQL SELECT query.
 *
 * See http://drm.tweakblogs.net/ for a usage example. This piece of code is intended
 * as an example, and not considered suitable for production
 */

class SqlSelectQuery implements ISqlSelectQuery {
    private $_select = array();
    private $_from = array();
    private $_where = array();
    private $_groupBy = array();
    private $_orderBy = array();
    private $_having = array();


    static function create() {
        return new self();
    }


    function __construct() {
    }

 
    function select($sql) {
        $this->_select = array($sql);
        return $this;
    }


    function addSelect($sql) {
        $this->_select[]= $sql;
        return $this;
    }


    function from($sql) {
        $this->_from = array($sql);
        return $this;
    }


    function addFrom($sql) {
        $this->_from[]= $sql;
        return $this;
    }


    function join($type, $table, $alias, $condition) {
        return $this->addFrom("$type JOIN $table $alias ON($condition)");
    }


    function leftJoin($table, $alias, $condition) {
        return $this->join("LEFT", $table, $alias, $condition);
    }


    function innerJoin($table, $alias, $condition) {
        return $this->join("INNER", $table, $alias, $condition);
    }


    function where($sql) {
        $this->_where = array($sql);
        return $this;
    }


    function addWhere($sql) {
        $this->_where[]= $sql;
        return $this;
    }


    function groupBy($sql) {
        $this->_groupBy = array($sql);
        return $this;
    }


    function addGroupBy($sql) {
        $this->_groupBy[]= $sql;
        return $this;
    }


    function having($sql) {
        $this->_having = array($sql);
        return $this;
    }


    function addHaving($sql) {
        $this->_having[]= $sql;
        return $this;
    }


    function orderBy($sql) {
        $this->_orderBy = array($sql);
        return $this;
    }


    function addOrderBy($sql) {
        $this->_orderBy[]= $sql;
        return $this;
    }

    
    function __toString() {
        $ret = sprintf("SELECT\n%s\nFROM\n%s", implode(', ', $this->_select), implode(' ', $this->_from));

        if($this->_where) {
            $ret .= "\nWHERE ";
            $ret .= implode(" AND ", $this->_where);
        }
        if($this->_groupBy) {
            $ret .= "\nGROUP BY ";
            $ret .= implode(", ", $this->_groupBy);
        }
        if($this->_having) {
            $ret .= "\nHAVING ";
            $ret .= implode(" AND ", $this->_having);
        }
        if($this->_orderBy) {
            $ret .= "\nORDER BY ";
            $ret .= implode(", ", $this->_orderBy);
        }

        return $ret;
    }
}
