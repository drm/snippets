<?php
/**
 * This code is provided as an example and therefore not considered stable. Use at your own risk.
 * Feel free to copy, modify and redistribute.
 *
 * @author Gerard van Helden <drm@melp.nl>
 */

/**
 * Straight-forward unit test for SqlSelectQuery class
 */
class SqlSelectQueryTestCase extends PHPUnit_Framework_TestCase {
    function setUp() {
        require_once 'SqlSelectQuery.php';
    }


    function tearDown() {
    }


    function testCreate() {
        $this->assertEquals(new SqlSelectQuery(), SqlSelectQuery::create());
    }


    function testAllChain() {
        $q = new SqlSelectQuery();
        foreach(array('select', 'from', 'where', 'groupBy', 'having', 'orderBy') as $part) {
            foreach(array($part, 'add' . ucfirst($part)) as $method) {
                $this->assertType('SqlSelectQuery', $q->$method('dummy'), 'Testing method ' . $method . ' for return type');
            }
        }
    }


    function testSelectFrom() {
        $this->assertEquals('SELECT a FROM b', $this->_stripSpace(SqlSelectQuery::create()->select('a')->from('b')));
        $this->assertEquals('SELECT a FROM b', $this->_stripSpace(SqlSelectQuery::create()->from('b')->select('a')));
        $this->assertEquals('SELECT a, b FROM c', $this->_stripSpace(SqlSelectQuery::create()->select('a, b')->from('c')));
        $this->assertEquals('SELECT a, b FROM c', $this->_stripSpace(SqlSelectQuery::create()->select('a')->addSelect('b')->from('c')));
        $this->assertEquals('SELECT a FROM b c', $this->_stripSpace(SqlSelectQuery::create()->select('a')->from('b')->addFrom('c')));
    }


    function testJoins() {
        $this->assertEquals('SELECT a FROM b INNER JOIN c ON(1)', $this->_stripSpace(SqlSelectQuery::create()->select('a')->from('b')->innerJoin('c', '', '1')));
        $this->assertEquals('SELECT a FROM b LEFT JOIN c ON(1)', $this->_stripSpace(SqlSelectQuery::create()->select('a')->from('b')->leftJoin('c', '', '1')));
        $this->assertEquals('SELECT a FROM b CROSS JOIN c ON(1)', $this->_stripSpace(SqlSelectQuery::create()->select('a')->from('b')->join('CROSS', 'c', '', '1')));

        $this->assertEquals('SELECT a FROM b INNER JOIN c x ON(1)', $this->_stripSpace(SqlSelectQuery::create()->select('a')->from('b')->innerJoin('c', 'x', '1')));
        $this->assertEquals('SELECT a FROM b LEFT JOIN c x ON(1)', $this->_stripSpace(SqlSelectQuery::create()->select('a')->from('b')->leftJoin('c', 'x', '1')));
        $this->assertEquals('SELECT a FROM b CROSS JOIN c x ON(1)', $this->_stripSpace(SqlSelectQuery::create()->select('a')->from('b')->join('CROSS', 'c', 'x', '1')));
    }


    function testWhere() {
        $base = SqlSelectQuery::create()->select('a')->from('b');
        $this->assertEquals('SELECT a FROM b WHERE 1', $this->_stripSpace($base->where('1')));
        $this->assertEquals('SELECT a FROM b WHERE 1 AND 2', $this->_stripSpace($base->addWhere('2')));
        $this->assertEquals('SELECT a FROM b WHERE 1', $this->_stripSpace($base->where('1')));
    }


    function testOrderBy() {
        $base = SqlSelectQuery::create()->select('a')->from('b');
        $this->assertEquals('SELECT a FROM b ORDER BY 1', $this->_stripSpace($base->orderBy('1')));
        $this->assertEquals('SELECT a FROM b ORDER BY 1, 2', $this->_stripSpace($base->addOrderBy('2')));
        $this->assertEquals('SELECT a FROM b ORDER BY 1', $this->_stripSpace($base->orderBy('1')));
    }


    function testGroupBy() {
        $base = SqlSelectQuery::create()->select('a')->from('b');
        $this->assertEquals('SELECT a FROM b GROUP BY 1', $this->_stripSpace($base->groupBy('1')));
        $this->assertEquals('SELECT a FROM b GROUP BY 1, 2', $this->_stripSpace($base->addGroupBy('2')));
        $this->assertEquals('SELECT a FROM b GROUP BY 1', $this->_stripSpace($base->groupBy('1')));
    }


    function testHaving() {
        $base = SqlSelectQuery::create()->select('a')->from('b');
        $this->assertEquals('SELECT a FROM b HAVING 1', $this->_stripSpace($base->having('1')));
        $this->assertEquals('SELECT a FROM b HAVING 1 AND 2', $this->_stripSpace($base->addHaving('2')));
        $this->assertEquals('SELECT a FROM b HAVING 1', $this->_stripSpace($base->having('1')));
    }



    private function _stripSpace($str) {
        return trim(preg_replace('/\s+/', ' ', $str));
    }
}
