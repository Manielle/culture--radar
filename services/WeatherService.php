<?php
/**
 * Weather Service for Culture Radar
 * Supports both OpenWeatherMap and Open-Meteo APIs
 */

require_once __DIR__ . '/../config.php';

class WeatherService {
    private $openWeatherApiKey;
    private $openWeatherBaseUrl;
    private $openMeteoBaseUrl;
    private $cacheDirectory;
    
    public function __construct() {
        $this->openWeatherApiKey = Config::get('OPENWEATHER_API_KEY');
        $this->openWeatherBaseUrl = Config::get('OPENWEATHER_BASE_URL', 'https://api.openweathermap.org/data/2.5');
        $this->openMeteoBaseUrl = Config::get('OPEN_METEO_BASE_URL', 'https://api.open-meteo.com/v1');
        $this->cacheDirectory = __DIR__ . '/../cache/weather/';
        
        // Create cache directory if it doesn't exist
        if (!file_exists($this->cacheDirectory)) {
            mkdir($this->cacheDirectory, 0755, true);
        }
    }
    
    /**
     * Get weather for an event
     */
    public function getWeatherForEvent($eventData) {
        $city = $eventData['city'] ?? 'Paris';
        $eventDate = $eventData['date_start'] ?? null;
        
        // Use cache key based on city and date
        $cacheKey = 'weather_' . strtolower($city) . '_' . date('Y-m-d', strtotime($eventDate ?? 'now'));
        $cachedData = $this->getFromCache($cacheKey);
        
        if ($cachedData) {
            return $cachedData;
        }
        
        try {
            // Try OpenWeatherMap first if API key is available
            if (!empty($this->openWeatherApiKey)) {
                $weatherData = $this->getOpenWeatherMapData($city, $eventDate);
            } else {
                // Fallback to Open-Meteo (free, no API key required)
                $weatherData = $this->getOpenMeteoData($city, $eventDate);
            }
            
            $weatherData['recommendations'] = $this->getWeatherRecommendations($weatherData);
            
            // Cache for 2 hours
            $this->saveToCache($cacheKey, $weatherData, 7200);
            
            return $weatherData;
            
        } catch (Exception $e) {
            error_log("Weather API error: " . $e->getMessage());
            return $this->getDefaultWeatherData();
        }
    }
    
    /**
     * Get weather from OpenWeatherMap
     */
    private function getOpenWeatherMapData($city, $eventDate = null) {
        if ($eventDate && strtotime($eventDate) > strtotime('+5 days')) {
            throw new Exception("OpenWeatherMap forecast only available for 5 days");
        }
        
        $endpoint = $eventDate && strtotime($eventDate) > strtotime('+1 day') ? 'forecast' : 'weather';
        $url = $this->openWeatherBaseUrl . '/' . $endpoint;
        
        $params = [
            'q' => $city . ',FR',
            'appid' => $this->openWeatherApiKey,
            'units' => 'metric',
            'lang' => 'fr'
        ];
        
        $response = $this->makeApiRequest($url . '?' . http_build_query($params));
        
        if ($endpoint === 'forecast') {
            return $this->parseOpenWeatherForecast($response, $eventDate);
        } else {
            return $this->parseOpenWeatherCurrent($response);
        }
    }
    
    /**
     * Get weather from Open-Meteo (free alternative)
     */
    private function getOpenMeteoData($city, $eventDate = null) {
        // First, get coordinates for the city
        $coordinates = $this->getCityCoordinates($city);
        
        if (!$coordinates) {
            throw new Exception("Could not find coordinates for city: " . $city);
        }
        
        $url = $this->openMeteoBaseUrl . '/forecast';
        $params = [
            'latitude' => $coordinates['lat'],
            'longitude' => $coordinates['lng'],
            'current' => 'temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m',
            'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max',
            'timezone' => 'Europe/Paris',
            'forecast_days' => 7
        ];
        
        $response = $this->makeApiRequest($url . '?' . http_build_query($params));
        
        return $this->parseOpenMeteoData($response, $eventDate);
    }
    
    /**
     * Get city coordinates (simplified - in production use Google Geocoding API)
     */
    private function getCityCoordinates($city) {
        $cities = [
            'paris' => ['lat' => 48.8566, 'lng' => 2.3522],
            'lyon' => ['lat' => 45.7640, 'lng' => 4.8357],
            'marseille' => ['lat' => 43.2965, 'lng' => 5.3698],
            'toulouse' => ['lat' => 43.6047, 'lng' => 1.4442],
            'nice' => ['lat' => 43.7102, 'lng' => 7.2620],
            'nantes' => ['lat' => 47.2184, 'lng' => -1.5536],
            'strasbourg' => ['lat' => 48.5734, 'lng' => 7.7521],
            'montpellier' => ['lat' => 43.6110, 'lng' => 3.8767],
            'bordeaux' => ['lat' => 44.8378, 'lng' => -0.5792],
            'lille' => ['lat' => 50.6292, 'lng' => 3.0573]
        ];
        
        return $cities[strtolower($city)] ?? $cities['paris'];
    }
    
    /**
     * Parse OpenWeatherMap current weather
     */
    private function parseOpenWeatherCurrent($data) {
        return [
            'source' => 'openweathermap',
            'type' => 'current',
            'temperature' => round($data['main']['temp']),
            'description' => ucfirst($data['weather'][0]['description']),
            'icon' => $this->getWeatherIcon($data['weather'][0]['icon']),
            'humidity' => $data['main']['humidity'],
            'wind_speed' => round($data['wind']['speed'] * 3.6), // Convert m/s to km/h
            'pressure' => $data['main']['pressure'],
            'visibility' => isset($data['visibility']) ? round($data['visibility'] / 1000, 1) : null,
            'rain_probability' => 0 // Not available in current weather
        ];
    }
    
    /**
     * Parse OpenWeatherMap forecast
     */
    private function parseOpenWeatherForecast($data, $eventDate) {
        $eventTimestamp = strtotime($eventDate);
        $closestForecast = null;
        $minDifference = PHP_INT_MAX;
        
        foreach ($data['list'] as $forecast) {
            $forecastTimestamp = $forecast['dt'];
            $difference = abs($eventTimestamp - $forecastTimestamp);
            
            if ($difference < $minDifference) {
                $minDifference = $difference;
                $closestForecast = $forecast;
            }
        }
        
        if (!$closestForecast) {
            throw new Exception("No suitable forecast found");
        }
        
        return [
            'source' => 'openweathermap',
            'type' => 'forecast',
            'temperature' => round($closestForecast['main']['temp']),
            'description' => ucfirst($closestForecast['weather'][0]['description']),
            'icon' => $this->getWeatherIcon($closestForecast['weather'][0]['icon']),
            'humidity' => $closestForecast['main']['humidity'],
            'wind_speed' => round($closestForecast['wind']['speed'] * 3.6),
            'pressure' => $closestForecast['main']['pressure'],
            'rain_probability' => isset($closestForecast['pop']) ? round($closestForecast['pop'] * 100) : 0,
            'visibility' => null
        ];
    }
    
    /**
     * Parse Open-Meteo data
     */
    private function parseOpenMeteoData($data, $eventDate = null) {
        $current = $data['current'];
        
        return [
            'source' => 'open-meteo',
            'type' => $eventDate ? 'forecast' : 'current',
            'temperature' => round($current['temperature_2m']),
            'description' => $this->getWeatherDescription($current['weather_code']),
            'icon' => $this->getOpenMeteoIcon($current['weather_code']),
            'humidity' => $current['relative_humidity_2m'],
            'wind_speed' => round($current['wind_speed_10m']),
            'pressure' => null, // Not available in free tier
            'rain_probability' => isset($data['daily']['precipitation_probability_max'][0]) ? $data['daily']['precipitation_probability_max'][0] : 0,
            'visibility' => null
        ];
    }
    
    /**
     * Get weather recommendations
     */
    private function getWeatherRecommendations($weatherData) {
        $temp = $weatherData['temperature'];
        $rainProb = $weatherData['rain_probability'];
        $windSpeed = $weatherData['wind_speed'];
        
        $recommendations = [];
        
        // Temperature recommendations
        if ($temp < 5) {
            $recommendations[] = "🧥 Habillez-vous chaudement, il fait très froid";
        } elseif ($temp < 15) {
            $recommendations[] = "🧥 Prévoyez une veste, il fait frais";
        } elseif ($temp > 25) {
            $recommendations[] = "👕 Habillez-vous léger, il fait chaud";
            $recommendations[] = "💧 N'oubliez pas de vous hydrater";
        }
        
        // Rain recommendations
        if ($rainProb > 70) {
            $recommendations[] = "☔ Risque de pluie élevé, prenez un parapluie";
        } elseif ($rainProb > 30) {
            $recommendations[] = "🌦️ Possibilité de pluie, prévoyez un imperméable";
        }
        
        // Wind recommendations
        if ($windSpeed > 20) {
            $recommendations[] = "💨 Vent fort prévu, attention aux objets légers";
        }
        
        return !empty($recommendations) ? implode('. ', $recommendations) : "Conditions météo favorables pour sortir !";
    }
    
    /**
     * Get weather icon (unified system)
     */
    private function getWeatherIcon($iconCode) {
        $iconMap = [
            '01d' => '☀️', '01n' => '🌙',
            '02d' => '⛅', '02n' => '☁️',
            '03d' => '☁️', '03n' => '☁️',
            '04d' => '☁️', '04n' => '☁️',
            '09d' => '🌧️', '09n' => '🌧️',
            '10d' => '🌦️', '10n' => '🌧️',
            '11d' => '⛈️', '11n' => '⛈️',
            '13d' => '❄️', '13n' => '❄️',
            '50d' => '🌫️', '50n' => '🌫️'
        ];
        
        return $iconMap[$iconCode] ?? '🌤️';
    }
    
    /**
     * Get Open-Meteo weather icon
     */
    private function getOpenMeteoIcon($weatherCode) {
        $iconMap = [
            0 => '☀️',   // Clear sky
            1 => '🌤️',   // Mainly clear
            2 => '⛅',   // Partly cloudy
            3 => '☁️',   // Overcast
            45 => '🌫️',  // Fog
            48 => '🌫️',  // Depositing rime fog
            51 => '🌦️',  // Light drizzle
            53 => '🌦️',  // Moderate drizzle
            55 => '🌧️',  // Dense drizzle
            61 => '🌦️',  // Slight rain
            63 => '🌧️',  // Moderate rain
            65 => '🌧️',  // Heavy rain
            71 => '❄️',  // Slight snow
            73 => '❄️',  // Moderate snow
            75 => '❄️',  // Heavy snow
            95 => '⛈️',  // Thunderstorm
            96 => '⛈️',  // Thunderstorm with hail
            99 => '⛈️'   // Severe thunderstorm
        ];
        
        return $iconMap[$weatherCode] ?? '🌤️';
    }
    
    /**
     * Get weather description for Open-Meteo codes
     */
    private function getWeatherDescription($weatherCode) {
        $descriptions = [
            0 => 'Ciel dégagé',
            1 => 'Plutôt dégagé',
            2 => 'Partiellement nuageux',
            3 => 'Couvert',
            45 => 'Brouillard',
            48 => 'Brouillard givrant',
            51 => 'Bruine légère',
            53 => 'Bruine modérée',
            55 => 'Bruine dense',
            61 => 'Pluie légère',
            63 => 'Pluie modérée',
            65 => 'Pluie forte',
            71 => 'Neige légère',
            73 => 'Neige modérée',
            75 => 'Neige forte',
            95 => 'Orage',
            96 => 'Orage avec grêle',
            99 => 'Orage violent'
        ];
        
        return $descriptions[$weatherCode] ?? 'Conditions variables';
    }
    
    /**
     * Get default weather data when APIs fail
     */
    private function getDefaultWeatherData() {
        return [
            'source' => 'default',
            'type' => 'unavailable',
            'temperature' => null,
            'description' => 'Données météo indisponibles',
            'icon' => '🌤️',
            'humidity' => null,
            'wind_speed' => null,
            'pressure' => null,
            'rain_probability' => null,
            'visibility' => null,
            'recommendations' => 'Vérifiez la météo sur votre application préférée'
        ];
    }
    
    /**
     * Make API request with error handling
     */
    private function makeApiRequest($url) {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 10,
                'header' => [
                    'User-Agent: CultureRadar/1.0'
                ]
            ]
        ]);
        
        $response = file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception('Failed to fetch weather data');
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from weather API');
        }
        
        return $data;
    }
    
    /**
     * Cache management
     */
    private function getFromCache($key) {
        $cacheFile = $this->cacheDirectory . md5($key) . '.json';
        
        if (file_exists($cacheFile)) {
            $cacheData = json_decode(file_get_contents($cacheFile), true);
            
            if ($cacheData && $cacheData['expires'] > time()) {
                return $cacheData['data'];
            }
        }
        
        return null;
    }
    
    private function saveToCache($key, $data, $duration) {
        $cacheFile = $this->cacheDirectory . md5($key) . '.json';
        $cacheData = [
            'data' => $data,
            'expires' => time() + $duration
        ];
        
        file_put_contents($cacheFile, json_encode($cacheData));
    }
}
?>