<?php
/**
 * Standalone dependency tracker for Zend Framework.
 */

error_reporting(E_ALL);

class FileUsageIterator extends ArrayIterator {
    function __construct($file) {
        if(!is_readable($file)) {
            throw new InvalidArgumentException("Can not read file $file");
        }
        preg_match_all('~require_once\s+([\'"])(.*)(\1)~', file_get_contents($file), $m);
        parent::__construct(array_unique($m[2]));
    }
}


class FileDependencyFinder extends IteratorIterator {
    private $_next = false;
    private $_ptr = 0;
    private $_files = null;
    private $_usage = null;

    function rewind() {
        parent::rewind();
        $this->_current = false;
        $this->_usage = new FileUsageIterator(parent::current());
        $this->next();
    }
    
    function current() {
        return $this->_current;
    }
    
    
    function next() {
        if($this->_usage->valid()) {
            $this->_current = $this->_usage->current();
            $this->_usage->next();
        } elseif(parent::valid()) {
            $this->_usage = new FileUsageIterator(parent::current());
            $this->_current = $this->_usage->current();
            $this->_usage->next();
            parent::next();
        } else {
            $this->_current = false;
        }
    }
    
    
    function valid() {
        return $this->_current !== false;
    }
}


class FlattenPackagesIterator extends FilterIterator {
    private $_container = array();
    private $_depth = null;
    
    function __construct(Iterator $i, $ignoreList = array(), $depth = -1) {
        parent::__construct($i);
        $this->_depth = $depth;
    }
    
    
    function accept() {
        return trim(parent::current()) && !in_array($this->_format(parent::current()), $this->_container);
    }

    function current(){
        $org = parent::current();
        $ret = $this->_format($org);
        $this->_container[]= $ret;
        return $ret;
    }
    
    
    private function _format($file) {
        $woSuffix = preg_replace('/\.php$/', '', $file);
        if($this->_depth < 0) {
            return str_replace('/', '_', $woSuffix);
        } else {
            return implode('_', array_slice(explode('/', $woSuffix), 0, $this->_depth));
        }
    }
}


class Zend_DependencyTracker 
{
	function __construct($libPath = '.', $depth = 2, $ignoreList = array()) 
	{
	    $this->_libPath = rtrim($libPath, '/');
	    $this->_depth = 2;
	    $this->_ignoreList = $ignoreList;
	}
	
	
	function findDependencies($packageName, $deep = false) 
	{
	    return new FlattenPackagesIterator(
	        new FileDependencyFinder(
	            $this->getPackageFiles($this->getPackagePath($packageName))
            ), $this->_ignoreList, $this->_depth);
	}
	
	
	
    function getPackageFiles($packagePath) 
	{
	    $files = new AppendIterator();
        if(is_file($packagePath . ".php")) {
	        $files->append(new ArrayIterator(array("$packagePath.php")));
	    }
	    if(is_dir($packagePath)) {
	        $files->append(
	            new RegexIterator(
	                new RecursiveIteratorIterator(
	                    new RecursiveDirectoryIterator($packagePath),
	                    RecursiveIteratorIterator::LEAVES_ONLY
                    ),
                    '/\.php$/'
                )
            );
        }
        return $files;
	}
	
	
	function getPackagePath($packageName) {
	    return $this->_libPath . '/' . str_replace('_', '/', $packageName);
	}
}

$tracker = new Zend_DependencyTracker();
$args = array_slice($_SERVER['argv'], 1);

if(isset($args[0])) {
    foreach($tracker->findDependencies($_SERVER['argv'][1]) as $file) {
        echo $file, "\n";
    }
} else {
    printf("Usage:\n%s packageName [-d]\n   -d Do a deep dependency search\n\n", $_SERVER['argv'][0]);
}


