<?php
// phpinfo();
// Create a new DOMDocument object to parse the HTML content
$doc = new DOMDocument();

// Set the error handling to ignore any parsing errors
libxml_use_internal_errors(true);

// Load the HTML content from the website URL
$url = "https://yourpetpa.com.au/";

$options = array(
   'http' => array(
      'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
   ),
);
$context = stream_context_create($options);
// $doc->loadHTMLFile($url, 0, $context);

$html = file_get_contents($url, false, $context);
$doc->loadHTML($html);




// $doc->loadHTMLFile($url);

// Create a new DOMXPath object to query the DOMDocument
$xpath = new DOMXPath($doc);

// Find all the product containers
$product_containers = $xpath->query('//div[@class="product-inner"]');

// Initialize an empty array to store the product information
$products = array();

// Loop through each product container and extract the required information
foreach ($product_containers as $product_container) {
   // Extract the product title
   $title = $xpath->query('.//h2[@class="woocommerce-loop-product__title"]/a', $product_container)->item(0)->textContent;

   // Extract the product description
   $description = $xpath->query('.//div[@class="woocommerce-product-details__short-description"]', $product_container)->item(0)->textContent;

   // Extract the product category
   $category = $xpath->query('.//span[@class="posted_in"]/a', $product_container)->item(0)->textContent;

   // Extract the product price
   $price = $xpath->query('.//span[@class="woocommerce-Price-amount amount"]/bdi', $product_container)->item(0)->textContent;

   // Extract the product URL
   $product_url = $xpath->query('.//a[@class="woocommerce-LoopProduct-link woocommerce-loop-product__link"]', $product_container)->item(0)->getAttribute("href");

   // Extract the product image URL
   $image_url = $xpath->query('.//img[@class="attachment-shop_catalog size-shop_catalog wp-post-image"]', $product_container)->item(0)->getAttribute("src");

   // Add the product information to the products array
   $products[] = array(
      "Title" => $title,
      "Description" => $description,
      "Category" => $category,
      "Price" => $price,
      "Product URL" => $product_url,
      "Image URL" => $image_url
   );
}


if (isset($products[0])) {
   // Write the products array to a CSV file
   $file = fopen('product_feed.csv', 'w');
   fputcsv($file, array_keys($products[0])); // Write the headers
   foreach ($products as $product) {
      fputcsv($file, $product); // Write the product information
   }
   fclose($file);
} else {
   echo "you have no data yet!";
}
