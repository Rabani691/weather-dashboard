<?php
$config = require_once __DIR__ . '/config/config.php'; // Charger la configuration

require_once __DIR__ . '/controllers/weatherController.php'; // Inclure le contrôleur

$controller = new WeatherController($config);
$controller->fetchWeather();
