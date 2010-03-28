<?php
require_once 'filters.php';
require_once 'lib/FilterRequest.php';
require_once 'CarSelectQuery.php';

// this would be part of your application bootstrap
$db = new Pdo('mysql:host=cliffy;dbname=dynamic_queries', 'gerard', 'test');
$db->setAttribute(Pdo::ATTR_ERRMODE, Pdo::ERRMODE_EXCEPTION);

$searchQuery = new CarSelectQuery();

$request = new FilterRequest(array(
    'type' => new TypeFilter($db),
    'brand' => new BrandFilter($db),
    'accessories' => new AccessoryFilter($db),
    'fuel' => new FuelFilter()
));


// this would be part of your controller
if(isset($_REQUEST['filter'])) {
    $request->setActiveFilters((array)$_REQUEST['filter']);

    foreach($request->getActiveFilters() as $name => $value) {
        $request->getFilter($name)->addToQuery($searchQuery, $value);
    }
}

$results = $db->query((string)$searchQuery)->fetchAll(Pdo::FETCH_ASSOC); 
$filterResults = array();
foreach($request->getAvailableFilters() as $name => $filter) {
    // notify the filter what results we're about to display.
    $filter->setResults($searchQuery, $results);

    $filterResults[$name] = array();
    foreach($filter->getOptions() as $option) {
        $option['url']= 'index.php?' . http_build_query(array('filter' => $request->getFilterParams($name, $option['value'])));
        $option['isActive'] = $request->isFilterActive($name, $option['value']);
        $filterResults[$name][]= $option;
    }
}

// This would go in your view
echo '<table>';
echo '<thead><tr><th>Car</th><td>Brand</td><td>Type</td><td>Accessories</td></tr></thead>';
echo '<tbody>';
foreach($results as $car) {
    printf('<tr><th>%s</th><td>%s</td><td>%s</td><td>%s</td></tr>', $car['title'], $car['brand'], $car['type'], $car['accessories']);
}
echo '</tbody>';
echo '</table>';

foreach($filterResults as $name => $options) {
    echo "<dl><dt>$name</dt>\n";
    foreach($options as $option) {
        if($option['isActive']) {
            printf(
                '<dd>%s <a href="%s">&times;</a></dd>',
                htmlspecialchars($option['label']),
                $option['url']
            );
        } else {
            printf(
                '<dd><a href="%s">%s</a></dd>',
                $option['url'],
                htmlspecialchars($option['label'])
            );
        }
        echo "\n";
    }
    echo '</dl>';
}
