<?php
require_once __DIR__ . '/controllers/weatherController.php'; // Inclure le contrôleur

$controller = new WeatherController();
$controller->fetchWeather();
