<?php
echo "Controll check!";
$_updatedProductsPath = "cd_catalog.xml";
$configurableProducts = array();
if ($_updatedProductsPath !== false) {
    $domUpdated = new DOMDocument();
    $stringFromFile = file_get_contents($_updatedProductsPath);
    $domUpdated->loadXML($stringFromFile);
    $xpathUpdated = new DOMXpath($domUpdated);
    $products = $xpathUpdated->query('//product');
    foreach ($products as $product) {
        if ($mainArticle = _getNodeValue($xpathUpdated, 'main_article', $product)) {
            $productType = _getNodeValue($xpathUpdated, 'product_type', $product);
            $sku = _getNodeValue($xpathUpdated, 'sku', $product);
            if ($productType == 'M') {
                $configurableProducts[$mainArticle]['configurable'][$sku] = 1;
            } elseif ($productType == 'A') {
                $configurableProducts[$mainArticle]['simple'][$sku] = 1;
            }
        }
    }
}
echo '<pre>';
print_r($configurableProducts);
echo '</pre>';
//unset($domUpdated);

function _getNodeValue(DOMXPath $xpath, $query, $parentNode = null, $key = 0)
{
    return $xpath->query($query, $parentNode)->item($key)->nodeValue;
}

?>