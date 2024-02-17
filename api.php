<?php
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

	if (empty($token)) {
		$tokenText = 'Basic QVBJX0V4cGxvcmVyOjEyMzQ1NmlzQUxhbWVQYXNz';
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	} else {
		$tokenText = 'Bearer ' . $token;
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	}

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

function getAuthorisation()
{
	$apiUrl = "https://api.baubuddy.de/index.php/login";
	$requestData = array(
		"username" => "365",
		"password" => "1"
	);

	$apiResponse = callApi($apiUrl, $requestData);
	// Assuming $apiResponse contains the JSON response
	$apiResponseJson = json_decode($apiResponse, true); // Decoding JSON string into associative array

	// Check if decoding was successful
	return isset($apiResponseJson['oauth']['access_token']) ? $apiResponseJson['oauth']['access_token'] : '';
}

function getData($token)
{
	$apiUrl = "https://api.baubuddy.de/dev/index.php/v1/tasks/select";
	$requestData = array(
		"username" => "365",
		"password" => "1"
	);

	return callApi($apiUrl, $requestData, $token);
}

$token = getAuthorisation();
if (!empty($token)) {
	echo getData($token);
}
