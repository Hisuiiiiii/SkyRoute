<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

$apiKey = 'f7e6f474fdac69a6ccf7fba90690bbf5'; 
$baseUrl = 'http://api.aviationstack.com/v1/flights';

$origin = $_GET['origin'] ?? '';
$destination = $_GET['destination'] ?? '';
$statusFilter = strtolower(trim($_GET['status'] ?? ''));
$dep_date = $_GET['dep_date'] ?? '';
$arr_date = $_GET['arr_date'] ?? '';

$params = [
    'access_key' => $apiKey,
    'dep_iata' => $origin,
    'arr_iata' => $destination,
    'limit' => 50
];

$url = $baseUrl . '?' . http_build_query($params);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo json_encode(['success' => false, 'message' => 'cURL Error: ' . curl_error($ch)]);
    exit;
}

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['success' => false, 'message' => 'API connection failed. HTTP Code: ' . $http_code]);
    exit;
}

$data = json_decode($response, true);

if (!empty($data['data'])) {
    $flights = [];
    foreach ($data['data'] as $flight) {
        $status = strtolower($flight['flight_status'] ?? 'unknown');

        // Filter by status
        if ($statusFilter && $status !== $statusFilter) continue;

        // Get formatted departure and arrival dates
        $departureDate = isset($flight['departure']['scheduled']) ? date('Y-m-d', strtotime($flight['departure']['scheduled'])) : '';
        $arrivalDate = isset($flight['arrival']['scheduled']) ? date('Y-m-d', strtotime($flight['arrival']['scheduled'])) : '';

        // Filter by date (if provided)
        if ($dep_date && $departureDate !== $dep_date) continue;
        if ($arr_date && $arrivalDate !== $arr_date) continue;

        $flights[] = [
            'airline' => $flight['airline']['name'] ?? 'N/A',
            'flight' => $flight['flight']['iata'] ?? 'N/A',
            'departure' => $flight['departure']['airport'] ?? 'N/A',
            'departure_time' => isset($flight['departure']['scheduled'])
                ? date('Y-m-d H:i', strtotime($flight['departure']['scheduled'])) . ' UTC'
                : 'N/A',
            'arrival' => $flight['arrival']['airport'] ?? 'N/A',
            'arrival_time' => isset($flight['arrival']['scheduled'])
                ? date('Y-m-d H:i', strtotime($flight['arrival']['scheduled'])) . ' UTC'
                : 'N/A',
            'status' => ucfirst($flight['flight_status'] ?? 'Unknown'),
        ];
    }

    echo json_encode(['success' => true, 'flights' => $flights]);
} else {
    echo json_encode(['success' => false, 'message' => 'No flights found.']);
}
?>
