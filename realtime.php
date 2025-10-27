<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Access-Control-Allow-Origin: *');

set_time_limit(0);
ignore_user_abort(true);

$apiKey = getenv('f7e6f474fdac69a6ccf7fba90690bbf5');
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

while (true) {
    if (connection_aborted()) break;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    $flights = [];

    if (!empty($data['data'])) {
        foreach ($data['data'] as $flight) {
            $status = strtolower($flight['flight_status'] ?? 'unknown');

            if ($statusFilter && $status !== $statusFilter) continue;

            $departureDate = isset($flight['departure']['scheduled']) ? date('Y-m-d', strtotime($flight['departure']['scheduled'])) : '';
            $arrivalDate = isset($flight['arrival']['scheduled']) ? date('Y-m-d', strtotime($flight['arrival']['scheduled'])) : '';

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
    }

    echo "data: " . json_encode($flights) . "\n\n";
    @ob_flush();
    flush();

    sleep(30); // refresh interval
}
