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
    'casio' => [
        'product_title' => [
            "//*[contains(@class, 'position-relative')]",
        ],
        'add_to_cart' => "//*[contains(@class, 'whiteColor')]",
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
        write_log("Database connection failed: " . $conn->connect_error);
        return;
    }

    // Fetch non-expired alerts with conditional cooldown for regular (2-hours) and guest users (3-hours)
    $query = "SELECT users.name, users.email, users.is_guest, alerts.url, alerts.id, alerts.alert_expiry, alerts.recent_alert, alerts.alerts_sent
        FROM users JOIN alerts
        ON users.id = alerts.user_id
        WHERE alerts.alert_expiry > NOW()
        AND (
            (users.is_guest = 1 AND (alerts.recent_alert IS NULL OR alerts.recent_alert < NOW() - INTERVAL 3 HOUR))
            OR 
            (users.is_guest = 0 AND (alerts.recent_alert IS NULL OR alerts.recent_alert < NOW() - INTERVAL 2 HOUR))
        )";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $alerts = [];

        while ($row = $result->fetch_assoc()) {
            $url = $row['url'];
            $userName = $row['name'];
            $userEmail = $row['email'];
            $alertId = $row['id'];
            $isGuest = $row['is_guest'];
            $alertsSent = $row['alerts_sent'];
            $recentAlert = $row['recent_alert'];

            // Handle guest user (limit of 3 alerts per product, irrespective of the day)
            if ($isGuest == 1) {
                if ($alertsSent >= 3) {
                    continue;  // Skip if guest user has already received 3 alerts for the product
                }
            }

            // Handle regular user (limit of 4 alerts per day per product)
            if ($isGuest == 0) {
                // Case 1: Same day as recent_alert
                if (date('Y-m-d', strtotime($recentAlert)) == date('Y-m-d')) {
                    // If alerts_sent < 4, send alert and increment alerts_sent
                    if ($alertsSent >= 4) {
                        continue;  // Skip if regular user has already reached their daily limit
                    }
                }
                // Case 2: Different day than recent_alert
                else {
                    // Reset alerts_sent to 1 (new day) and send the alert
                    $alertsSent = 0;
                }
            }

            // Call scraping function to check product availability
            $scrapedData = scrape_product_data($url);

            // Determine product availability
            $productAvailability = false;
            if (strpos($url, 'hmt') !== false) {
                $productAvailability = !$scrapedData['add_to_cart_exists']; // HMT logic
            } else {
                $productAvailability = $scrapedData['add_to_cart_exists']; // Amazon/Meesho/Casio logic
            }

            // If product is available, add to alert list
            if ($productAvailability) {
                $alerts[] = [
                    'id' => $alertId,
                    'name' => $userName,
                    'email' => $userEmail,
                    'product_title' => $scrapedData['title'],
                    'url' => $url,
                ];

                // Increment alerts_sent for the user
                $newAlertsSent = $alertsSent + 1;
                $updateQuery = "UPDATE alerts SET alerts_sent = $newAlertsSent, recent_alert = NOW() WHERE id = $alertId";
                if ($conn->query($updateQuery)) {
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
