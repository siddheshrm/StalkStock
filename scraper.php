<?php
include 'connection.php';

// 1. Configuration: XPath Selectors for Different Websites
$default_selectors_xpath = [
    'hmt' => [
        'product_title' => [
            "//*[contains(@class, 'product-title')]",
            "//*[contains(@class, 'product-description')]"
        ],
        'add_to_cart' => "//*[@id='notifyMe']",
    ],
    'amazon' => [
        'product_title' => [
            "//*[@id='productTitle']",
            "//*[contains(@class, 'product-title-word-break')]",
            "//*[contains(@class, 'a-size-large product-title-word-break')]"
        ],
        'add_to_cart' => "//*[@id='trigger_emioptions']",
    ],
    'meesho' => [
        'product_title' => [
            "//*[contains(@class, 'fhfLdV')]",
        ],
        'add_to_cart' => "//*[contains(@class, 'eEiZjr')]",
    ],
];

// 2. Function to Get Selectors for the URL
function get_selectors_for_url($url)
{
    global $default_selectors_xpath;

    // Try to match the URL to the defined sites
    foreach ($default_selectors_xpath as $key => $selectors) {
        if (strpos($url, $key) !== false) {
            return $selectors;
        }
    }

    // Return a generic fallback structure to avoid undefined errors
    return [
        'product_title' => [],
        'add_to_cart' => '',
    ];
}

// 3. Scraping Function
function scrape_product_data($url)
{
    // Get the XPath selectors for the URL
    $selectors = get_selectors_for_url($url);

    // Initialize variables for product title and add to cart button
    $productTitle = '';
    $addToCartButton = '';

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);  // Set the target URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the result as a string
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if any
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (useful for testing)

    // Execute cURL request
    $htmlContent = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo 'Error fetching page: ' . curl_error($ch);
        curl_close($ch);
        return;
    }

    // Close cURL
    curl_close($ch);

    // Load the HTML content into DOMDocument
    $dom = new DOMDocument();
    @$dom->loadHTML($htmlContent);  // Suppress warnings due to malformed HTML

    // Initialize XPath
    $xpath = new DOMXPath($dom);

    // Fetch the product title using multiple XPath selectors
    foreach ($selectors['product_title'] as $selector) {
        $titleElements = $xpath->query($selector);
        if ($titleElements->length > 0) {
            $productTitle = trim($titleElements->item(0)->nodeValue);
            break; // Stop after the first successful match
        }
    }

    // Fetch the add-to-cart button using XPath
    $buttonElements = $xpath->query($selectors['add_to_cart']);
    if ($buttonElements->length > 0) {
        $addToCartButton = trim($buttonElements->item(0)->nodeValue);
    }

    // Return the scraped data (title and add to cart button)
    return [
        'title' => $productTitle,
        'add_to_cart_exists' => !empty($addToCartButton) // True if button exists
    ];
}

// 4. Fetch URLs from the 'alerts' Table and Scrape Data
function scrape_data_from_alerts($conn)
{
    // Check if connection is successful
    if ($conn->connect_error) {
        echo "Connection failed: " . $conn->connect_error;
        return;
    }

    try {
        // Fetch all URLs from the 'alerts' table
        $query = "SELECT url FROM alerts";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            // Loop through each URL and scrape data
            while ($row = $result->fetch_assoc()) {
                $url = $row['url'];

                // Call the scraping function for each URL
                $scrapedData = scrape_product_data($url);

                // Output or process the scraped data
                echo "Product Title: " . $scrapedData['title'] . "\n";

                // Handle product availability logic
                if (strpos($url, 'hmt') !== false) {
                    // HMT: Product does not exist if add-to-cart button exists
                    echo "Product availability: " . ($scrapedData['add_to_cart_exists'] ? 'No' : 'Yes') . "\n\n";
                } else {
                    // Amazon and Meesho: Product exists if add-to-cart button exists
                    echo "Product availability: " . ($scrapedData['add_to_cart_exists'] ? 'Yes' : 'No') . "\n\n";
                }
            }
        } else {
            echo "No URLs found in the 'alerts' table.";
        }
    } catch (Exception $e) {
        // Handle any exceptions
        echo "Error: " . $e->getMessage();
    }
}

// Call the function to start scraping
scrape_data_from_alerts($conn);
