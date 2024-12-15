<?php
include 'connection.php';
include 'email_alerts/alert_handler.php';
include 'logs/log_handler.php';

// 1. Configuration: XPath Selectors for Different Websites
$default_selectors_xpath = [
    'hmt' => [
        'product_title' => [
            "//*[contains(@class, 'product-title')]",
            "//*[contains(@class, 'product-description')]"
        ],
        'add_to_cart' => "//*[@id='notifyMe']",
        'product_price' => "//*[contains(@class, 'discountPrice')]",
    ],
    'amazon' => [
        'product_title' => [
            "//*[@id='productTitle']",
            "//*[contains(@class, 'product-title-word-break')]",
            "//*[contains(@class, 'a-size-large')]"
        ],
        'add_to_cart' => "//*[@id='trigger_emioptions']",
        'product_price' => "//*[contains(@class, 'a-price-whole')]",
    ],
    'meesho' => [
        'product_title' => [
            "//*[contains(@class, 'fhfLdV')]",
        ],
        'add_to_cart' => "//*[contains(@class, 'eEiZjr')]",
        'product_price' => [
            "/html/body/div/div[3]/div/div[2]/div[1]/div/div[1]/div/h4/text()[2]",
            "/html/body/div/div[3]/div/div[2]/div[1]/div/div[1]/h4/text()[2]"
        ]
    ],
    'casio' => [
        'product_title' => [
            "//*[contains(@class, 'position-relative')]",
        ],
        'add_to_cart' => "//*[contains(@class, 'whiteColor')]",
        'product_price' => [
            "/html/body/div[2]/div[7]/div/div[2]/div/div[1]/div[2]/div/span[2]/span[2]/text()[2]",
            "/html/body/div[2]/div[7]/div/div[2]/div/div[1]/div[2]/div/span[2]/span[1]/text()",
        ]
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
        'product_price' => [],
    ];
}

// 3. Function to fetch Tata Cliq product data using the API
function fetch_tata_cliq_product_data($url)
{
    // Extract product ID from the URL
    $product_id = extract_product_id_from_url($url);

    // If the product ID is not found, return null
    if (!$product_id) {
        return null;
    }

    // Construct the full API URL
    $api_url = "https://www.tatacliq.com/marketplacewebservices/v2/mpl/products/productDetails/$product_id?isPwa=true&isMDE=true&isDynamicVar=true";

    // Fetch data from the API using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);  // Set the target URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the result as a string
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (useful for testing)
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36',
    ]);

    // Execute cURL request
    $response = curl_exec($ch);

    // If cURL failed, return null
    if (!$response) {
        curl_close($ch);
        return null;
    }

    curl_close($ch);

    // Decode the JSON response
    $product_data = json_decode($response, true);

    // Check if JSON decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }

    // Check if the status is 'SUCCESS' before proceeding
    if (isset($product_data['status']) && $product_data['status'] === 'SUCCESS') {
        // Extract product details
        $productTitle = $product_data['productTitle'] ?? null;
        $availableStock = $product_data['winningSellerAvailableStock'] ?? 0;
        $productPrice = $product_data['winningSellerPrice']['value'] ?? null;

        if ($productTitle === null || $productPrice === null) {
            return null;
        }

        // Determine if the product is available for adding to cart
        $addToCartExists = ($availableStock > 0); // true if stock > 0, false otherwise

        // Return the structured data
        return [
            'title' => $productTitle,
            'add_to_cart_exists' => $addToCartExists,
            'price' => $productPrice,
        ];
    } else {
        return null;
    }
}

// 4. Extract Product ID from URL (for Tata Cliq)
function extract_product_id_from_url($url)
{
    // Extract product ID from URL using regex
    $pattern = '/\/p-([a-z0-9\-]+)/';
    preg_match($pattern, $url, $matches);
    return isset($matches[1]) ? $matches[1] : null;
}

// 5. Scraping Function
function scrape_product_data($url)
{
    // Get the XPath selectors for the URL (non-Tata Cliq URLs)
    $selectors = get_selectors_for_url($url);

    // Initialize variables for product title and add to cart button
    $productTitle = '';
    $addToCartButton = '';
    $productPrice = '';

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);  // Set the target URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the result as a string
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if any
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (useful for testing)
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36',
    ]);

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

    // Check if selectors are defined for 'product_title' and loop through them
    if (!empty($selectors['product_title'])) {
        if (is_array($selectors['product_title'])) {
            // Loop through multiple selectors
            foreach ($selectors['product_title'] as $selector) {
                if (empty($selector) || !is_string($selector)) {
                    continue; // Skip invalid or empty XPath expressions
                }

                $titleElements = $xpath->query($selector);
                if ($titleElements->length > 0) {
                    $productTitle = trim($titleElements->item(0)->nodeValue);
                    break; // Stop after the first successful match
                }
            }
        } elseif (is_string($selectors['product_title'])) {
            // Handle single selector
            $titleElements = $xpath->query($selectors['product_title']);
            if ($titleElements->length > 0) {
                $productTitle = trim($titleElements->item(0)->nodeValue);
            }
        }
    }

    // Check if selectors are defined for 'add_to_cart' and loop through them
    if (!empty($selectors['add_to_cart'])) {
        if (is_array($selectors['add_to_cart'])) {
            // Loop through multiple selectors
            foreach ($selectors['add_to_cart'] as $selector) {
                if (empty($selector) || !is_string($selector)) {
                    continue;
                }
                $buttonElements = $xpath->query($selector);
                if ($buttonElements->length > 0) {
                    $addToCartButton = trim($buttonElements->item(0)->nodeValue);
                    break; // Stop after the first successful match
                }
            }
        } elseif (is_string($selectors['add_to_cart'])) {
            // Handle single selector
            $buttonElements = $xpath->query($selectors['add_to_cart']);
            if ($buttonElements->length > 0) {
                $addToCartButton = trim($buttonElements->item(0)->nodeValue);
            }
        }
    }

    // Check if selectors are defined for 'product_price' and loop through them
    if (!empty($selectors['product_price'])) {
        if (is_array($selectors['product_price'])) {
            // Loop through multiple selectors
            foreach ($selectors['product_price'] as $selector) {
                if (empty($selector) || !is_string($selector)) {
                    continue; // Skip invalid or empty XPath expressions
                }

                $priceElements = $xpath->query($selector);
                if ($priceElements->length > 0) {
                    $productPrice = trim($priceElements->item(0)->nodeValue);
                    break; // Stop after the first successful match
                }
            }
        } elseif (is_string($selectors['product_price'])) {
            // Handle single selector
            $priceElements = $xpath->query($selectors['product_price']);
            if ($priceElements->length > 0) {
                $productPrice = trim($priceElements->item(0)->nodeValue);
            }
        }
    }

    // Filter out non-numeric characters, keeping digits and decimal point
    if (!empty($productPrice)) {
        // Remove currency symbols and other characters
        $productPrice = preg_replace('/[^\d.]/', '', $productPrice);
        $productPrice = (float) $productPrice;
    }


    // Return the scraped data (title and add to cart button)
    return [
        'title' => $productTitle,
        'add_to_cart_exists' => !empty($addToCartButton), // True if button exists
        'price' => $productPrice,
    ];
}

// 6. Fetch URLs from the 'alerts' Table and Scrape Data
function scrape_data_from_alerts($conn)
{
    // Check if connection is successful
    if ($conn->connect_error) {
        write_log("Database connection failed: " . $conn->connect_error);
        return;
    }

    $current_time = date('Y-m-d H:i:s');

    // Fetch non-expired alerts with conditional cooldown for all users (55-mins)
    $query = "SELECT users.name, users.email, users.is_guest, alerts.url, alerts.id, alerts.alert_expiry, alerts.recent_alert, alerts.alerts_sent, alerts.price
        FROM users JOIN alerts
        ON users.id = alerts.user_id
        WHERE alerts.alert_expiry > CONVERT_TZ(NOW(), '+00:00', '+05:30')
        AND (
            (users.is_guest = 1 AND alerts.alerts_sent < 3 AND (alerts.recent_alert IS NULL OR alerts.recent_alert < CONVERT_TZ(NOW(), '+00:00', '+05:30') - INTERVAL 115 MINUTE))
            OR
            (users.is_guest = 0 AND alerts.alerts_sent < 4 AND (alerts.recent_alert IS NULL OR alerts.recent_alert < CONVERT_TZ(NOW(), '+00:00', '+05:30') - INTERVAL 55 MINUTE))
        )
        ";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $alerts = [];

        while ($row = $result->fetch_assoc()) {
            $url = $row['url'];
            $userName = $row['name'];
            $userEmail = $row['email'];
            $alertId = $row['id'];
            $alertsSent = $row['alerts_sent'];
            $desiredPrice = $row['price'];

            // Call scraping function to check product availability
            $scrapedData = scrape_product_data($url);

            // Determine product availability
            $productAvailability = false;
            if (strpos($url, 'hmt') !== false) {
                $productAvailability = !$scrapedData['add_to_cart_exists']; // HMT logic
            } elseif (strpos($url, 'tatacliq') !== false) {
                $scrapedData = fetch_tata_cliq_product_data($url);
                $productAvailability = $scrapedData['add_to_cart_exists']; // Tata Cliq logic
            } else {
                $productAvailability = $scrapedData['add_to_cart_exists']; // Amazon/Meesho/Casio logic
            }

            // If product is available, add to alert list
            if ($productAvailability && ($desiredPrice === null || $scrapedData['price'] <= $desiredPrice)) {
                $alerts[] = [
                    'id' => $alertId,
                    'name' => $userName,
                    'email' => $userEmail,
                    'product_title' => $scrapedData['title'],
                    'url' => $url,
                    'product_price' => $scrapedData['price'],
                ];

                // Increment alerts_sent
                $alertsSent += 1;

                // Update alerts_sent and recent_alert
                $updateQuery = $conn->prepare("UPDATE alerts SET alerts_sent = ?, recent_alert = ? WHERE id = ?");
                $updateQuery->bind_param('isi', $alertsSent, $current_time, $alertId);
                if ($updateQuery->execute()) {
                    write_log("Updated alerts for ID $alertId.");
                } else {
                    write_log("Failed to update alerts for ID $alertId: " . $conn->error);
                }
            }
        }

        // Trigger email alerts for all available products
        if (!empty($alerts)) {
            // write_log("Triggering alerts for " . count($alerts) . " products to " . $userEmail);
            trigger_email_alerts($alerts);
        } else {
            write_log("No products available for alert.");
        }
    } else {
        write_log("No valid alerts found in database.");
    }
}

// Call the function to start scraping
scrape_data_from_alerts($conn);
