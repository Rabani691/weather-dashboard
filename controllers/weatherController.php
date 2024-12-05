<?php
$config = require __DIR__ . '/config.php'; // Charger la configuration

class Weather
{
    private $apiKey;
    private $units;
    private $lang;

    public function __construct($config)
    {
        $this->apiKey = $config['api_key'];
        $this->units = $config['units'];
        $this->lang = $config['lang'];
    }

    /**
     * Obtenir les coordonnées (latitude et longitude) d'une ville.
     */
    public function getCoordinates($city, $country)
    {
        $city = urlencode($city);
        $country = urlencode($country);
        $url = "https://api.openweathermap.org/geo/1.0/direct?q={$city},{$country}&limit=1&appid={$this->apiKey}";

        $response = @file_get_contents($url);

        if ($response === false) {
            return ['error' => 'Unable to fetch coordinates'];
        }

        $data = json_decode($response, true);

        if (empty($data)) {
            return ['error' => 'Location not found'];
        }

        return [
            'lat' => $data[0]['lat'],
            'lon' => $data[0]['lon']
        ];
    }

    /**
     * Obtenir les données météo actuelles.
     */
    public function getWeatherData($lat, $lon)
    {
        $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&units={$this->units}&lang={$this->lang}&appid={$this->apiKey}";

        $response = @file_get_contents($url);

        if ($response === false) {
            return ['error' => 'Unable to fetch weather data'];
        }

        $data = json_decode($response, true);

        if (empty($data)) {
            return ['error' => 'Invalid weather data received'];
        }

        return $data;
    }

    /**
     * Obtenir les prévisions météo pour 5 jours.
     */
    public function get5DayForecast($lat, $lon)
    {
        $url = "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&units={$this->units}&lang={$this->lang}&appid={$this->apiKey}";

        $response = @file_get_contents($url);

        if ($response === false) {
            return ['error' => 'Unable to fetch forecast data'];
        }

        $data = json_decode($response, true);

        if (empty($data['list'])) {
            return ['error' => 'Invalid forecast data received'];
        }

        return $data['list'];
    }
}

class WeatherController
{
    private $weather;

    public function __construct($config)
    {
        $this->weather = new Weather($config);
    }

    public function fetchWeather()
    {
        $coordinates = $this->weather->getCoordinates('Paris', 'FR');
        if (isset($coordinates['error'])) {
            http_response_code(500);
            echo json_encode($coordinates);
            return;
        }

        $lat = $coordinates['lat'];
        $lon = $coordinates['lon'];

        $weatherData = $this->weather->getWeatherData($lat, $lon);
        if (isset($weatherData['error'])) {
            http_response_code(500);
            echo json_encode($weatherData);
            return;
        }

        http_response_code(200);
        echo json_encode($weatherData);
    }

    public function fetch5DayForecast()
    {
        $coordinates = $this->weather->getCoordinates('Paris', 'FR');
        if (isset($coordinates['error'])) {
            http_response_code(500);
            echo json_encode($coordinates);
            return;
        }

        $lat = $coordinates['lat'];
        $lon = $coordinates['lon'];

        $forecastData = $this->weather->get5DayForecast($lat, $lon);
        if (isset($forecastData['error'])) {
            http_response_code(500);
            echo json_encode($forecastData);
            return;
        }

        http_response_code(200);
        echo json_encode($forecastData);
    }
}

// Point d'entrée
$controller = new WeatherController($config);

if ($_GET['action'] === 'forecast') {
    $controller->fetch5DayForecast();
} else {
    $controller->fetchWeather();
}
