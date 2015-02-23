<?php
echo "Check!";
$_fullFileProductsPath = "catalog.xml";
$_updatedFileProductsPath = "updated.xml";
$fullFileConfigurableProducts = array();
$updatedFileConfigurableProducts = array();

if ($_updatedFileProductsPath !== false) {
    $domUpdated = new DOMDocument();
    $stringFromUpdatedFile = file_get_contents($_updatedFileProductsPath);
    $domUpdated->loadXML($stringFromUpdatedFile);
    $xpathUpdated = new DOMXPath($domUpdated);
    $productsUpdated = $xpathUpdated->query('//product');
    foreach ($productsUpdated as $product) {
        if ($mainArticle = _getNodeValue($xpathUpdated, 'main_article', $product)) {
            $productType = _getNodeValue($xpathUpdated, 'product_type', $product);
            $sku = _getNodeValue($xpathUpdated, 'sku', $product);
            if ($productType == 'M') {
                $updatedFileConfigurableProducts[$mainArticle]['configurable'][$sku] = 0;
            } elseif ($productType == 'A') {
                $updatedFileConfigurableProducts[$mainArticle]['simple'][$sku] = 0;
            }
        }
    }

}

echo '<pre>updated';
print_r($updatedFileConfigurableProducts);
echo '</pre>';

if ($_fullFileProductsPath !== false) {
    $domFull = new DOMDocument();
    $stringFromFile = file_get_contents($_fullFileProductsPath);
    $domFull->loadXML($stringFromFile);
    $xpathFull = new DOMXpath($domFull);
    $productsFull = $xpathFull->query('//product');
    foreach ($productsFull as $product) {
        if ($mainArticle = _getNodeValue($xpathFull, 'main_article', $product)) {
            $productType = _getNodeValue($xpathFull, 'product_type', $product);
            $sku = _getNodeValue($xpathFull, 'sku', $product);
            if ($productType == 'M') {
                $fullFileConfigurableProducts[$mainArticle]['configurable'][$sku] = isset($updatedFileConfigurableProducts[$mainArticle]['configurable'][$sku]) ? 1 : 0;
            } elseif ($productType == 'A') {
                $fullFileConfigurableProducts[$mainArticle]['simple'][$sku] = isset($updatedFileConfigurableProducts[$mainArticle]['simple'][$sku]) ? 1 : 0;
            }
        }
    }
}

echo '<pre>full';
print_r($fullFileConfigurableProducts);
echo '</pre>';

foreach ($fullFileConfigurableProducts as $articles => $value) {
    $updated = 0;
    $all = 0;
    foreach ($value as $productType) {
        foreach ($productType as $product) {
            $updated += $product;
            $all++;
        }
    }

    if ($updated === $all) {
        unset($fullFileConfigurableProducts[$articles]);
    }
}

echo '<pre>NotFullyUpdated';
print_r($fullFileConfigurableProducts);
echo '</pre>';

function _getNodeValue(DOMXPath $xpath, $query, $parentNode = null, $key = 0)
{
    return $xpath->query($query, $parentNode)->item($key)->nodeValue;
}

?>