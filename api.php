<?php

/**
 * Function: callApi
 * Description: Makes an API call using cURL.
 * 
 * @param string $apiUrl The URL of the API endpoint.
 * @param array $requestData The data to be sent with the API request.
 * @param string $token (Optional) The authorization token for the API request.
 * @return string The response from the API call.
 */
function callApi($apiUrl, $requestData, $token = '')
{
	// Initialize cURL session
	$ch = curl_init();

	// Set the request data
	$requestDataJson = json_encode($requestData);

	// Set the cURL options
	curl_setopt($ch, CURLOPT_URL, $apiUrl);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $requestDataJson);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

	// Set token and request type based on the presence of token
	if (empty($token)) {
		$tokenText = 'Basic QVBJX0V4cGxvcmVyOjEyMzQ1NmlzQUxhbWVQYXNz';
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	} else {
		$tokenText = 'Bearer ' . $token;
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	}

	// Set headers
	$headers = [
		"Authorization: " . $tokenText,
		"Content-Type: application/json"
	];
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	// Execute the cURL session
	$response = curl_exec($ch);

	// Check for errors
	if (curl_errno($ch)) {
		throw new Exception('Error occurred while calling API: ' . curl_error($ch));
	}

	// Close cURL session
	curl_close($ch);

	// Return the API response
	return $response;
}

/**
 * Function: getAuthorisation
 * Description: Retrieves the authorization token for accessing the API.
 * 
 * @return string The authorization token.
 */
function getAuthorisation()
{
	// API endpoint for authentication
	$apiUrl = "https://api.baubuddy.de/index.php/login";
	// Request data for authentication
	$requestData = array(
		"username" => "365",
		"password" => "1"
	);

	// Call API to authenticate
	$apiResponse = callApi($apiUrl, $requestData);
	// Assuming $apiResponse contains the JSON response
	$apiResponseJson = json_decode($apiResponse, true); // Decoding JSON string into associative array

	// Check if decoding was successful and return token
	return isset($apiResponseJson['oauth']['access_token']) ? $apiResponseJson['oauth']['access_token'] : '';
}

/**
 * Function: getData
 * Description: Retrieves data from the API using the provided authorization token.
 * 
 * @param string $token The authorization token.
 * @return string The data fetched from the API.
 */
function getData($token)
{
	// API endpoint for fetching data
	$apiUrl = "https://api.baubuddy.de/dev/index.php/v1/tasks/select";
	// Request data for fetching data
	$requestData = array(
		"username" => "365",
		"password" => "1"
	);

	// Call API to fetch data
	return callApi($apiUrl, $requestData, $token);
}

// Get authorization token
$token = getAuthorisation();
// If token is not empty, fetch data and echo it
if (!empty($token)) {
	echo getData($token);
}
