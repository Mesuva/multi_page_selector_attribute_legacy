multi_page_selector_attribute
=============================

A concrete5 attribute to select multiple pages in an sortable list

Once installed, you can fetch the attribute in a page template one of two ways:

```php
$products = $c->getAttribute('related_products', 'pageArray');
// $products now contains an array of collection (page) objects

// or 
$products = $c->getAttribute('related_products', 'pageLinkArray');
// $products now contains an array of arrays, each containing 'cID' 'url', and 'name', meaning you can do:

if (!empty($products)) { 
    echo '<ul>';
    foreach($products as $prod) {
        echo '<li><a href="' . $prod['url'] . '">'. $prod['name']. '</a></li>';
    }
    echo '</ul>';
}
```
