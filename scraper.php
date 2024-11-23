<?php
include 'connection.php';
include 'email_alerts/alert_handler.php';

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
        // Log the connection error (you can implement a logging function here)
        error_log("Connection failed: " . $conn->connect_error);
        return;
    }

    try {
        // Reset 'is_guest' to 0 for all users at the beginning of the scraping cycle
        $resetQuery = "UPDATE alerts SET alert_sent = 0";
        if (!$conn->query($resetQuery)) {
            error_log("Failed to reset is_guest flag: " . $conn->error);
        }

        // Fetch unsent alerts
        $query = "SELECT users.name, users.email, alerts.url, alerts.id 
                  FROM users 
                  JOIN alerts ON users.id = alerts.user_id 
                  WHERE alerts.alert_sent = 0";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $alerts = []; // Prepare an array to store alerts for processing

            while ($row = $result->fetch_assoc()) {
                $url = $row['url'];
                $userName = $row['name'];
                $userEmail = $row['email'];
                $alertId = $row['id'];

                // Call scraping function
                $scrapedData = scrape_product_data($url);

                // Determine product availability
                $productAvailability = false;
                if (strpos($url, 'hmt') !== false) {
                    $productAvailability = !$scrapedData['add_to_cart_exists']; // HMT logic
                } else {
                    $productAvailability = $scrapedData['add_to_cart_exists']; // Amazon/Meesho logic
                }

                // If available, add alert details to the array
                if ($productAvailability) {
                    $alerts[] = [
                        'id' => $alertId,
                        'name' => $userName,
                        'email' => $userEmail,
                        'product_title' => $scrapedData['title'],
                        'url' => $url,
                    ];
                }
            }

            // Trigger email alerts for all available products
            if (!empty($alerts)) {
                trigger_email_alerts($alerts);

                // Update alerts in the database
                foreach ($alerts as $alert) {
                    $alertId = $alert['id'];
                    $updateQuery = "UPDATE alerts SET alert_sent = 1 WHERE id = $alertId";
                    if (!$conn->query($updateQuery)) {
                        error_log("Failed to update alert for ID $alertId: " . $conn->error);
                    }
                }
            } else {
                error_log("No products available for alert.");
            }
        } else {
            error_log("No pending alerts found.");
        }
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
    }
}

// Call the function to start scraping
scrape_data_from_alerts($conn);
