<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $weatherApiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $weatherApiKey;
    }

    /**
     * Récupère la météo pour une ville et une date
     */
    public function getWeatherForEvent(string $city, \DateTimeInterface $eventDate): ?array
    {
        try {
            // Calculer le nombre de jours jusqu'à l'événement
            $now = new \DateTime();
            $now->setTime(0, 0, 0);
            $eventDateOnly = clone $eventDate;
            $eventDateOnly->setTime(0, 0, 0);
            $diff = $now->diff($eventDateOnly);
            $daysUntilEvent = (int) $diff->format('%R%a'); // %R pour avoir le signe
            
            // Si l'événement est dans plus de 5 jours OU dans le passé, utiliser la météo actuelle
            if ($daysUntilEvent > 5 || $daysUntilEvent < 0) {
                // Appel API pour la météo actuelle
                $response = $this->httpClient->request('GET', 'https://api.openweathermap.org/data/2.5/weather', [
                    'query' => [
                        'q' => $city,
                        'appid' => $this->apiKey,
                        'units' => 'metric',
                        'lang' => 'en'
                    ],
                    'timeout' => 5
                ]);
                
                if ($response->getStatusCode() !== 200) {
                    return null;
                }
                
                $data = $response->toArray();
                
                return [
                    'available' => true,
                    'is_forecast' => false,
                    'temperature' => round($data['main']['temp']),
                    'feels_like' => round($data['main']['feels_like']),
                    'description' => ucfirst($data['weather'][0]['description']),
                    'icon' => $data['weather'][0]['icon'],
                    'humidity' => $data['main']['humidity'],
                    'wind_speed' => round($data['wind']['speed'] * 3.6, 1),
                    'city' => $data['name']
                ];
            }
            
            // Pour les événements dans les 5 prochains jours, utiliser les prévisions
            $response = $this->httpClient->request('GET', 'https://api.openweathermap.org/data/2.5/forecast', [
                'query' => [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'lang' => 'en'
                ],
                'timeout' => 5
            ]);
            
            if ($response->getStatusCode() !== 200) {
                return null;
            }
            
            $data = $response->toArray();
            
            // Trouver la prévision la plus proche de la date de l'événement
            $targetTimestamp = $eventDate->getTimestamp();
            $closestForecast = null;
            $minDiff = PHP_INT_MAX;
            
            foreach ($data['list'] as $forecast) {
                $forecastTimestamp = $forecast['dt'];
                $diff = abs($targetTimestamp - $forecastTimestamp);
                
                if ($diff < $minDiff) {
                    $minDiff = $diff;
                    $closestForecast = $forecast;
                }
            }
            
            if (!$closestForecast) {
                return null;
            }
            
            return [
                'available' => true,
                'is_forecast' => true,
                'temperature' => round($closestForecast['main']['temp']),
                'feels_like' => round($closestForecast['main']['feels_like']),
                'description' => ucfirst($closestForecast['weather'][0]['description']),
                'icon' => $closestForecast['weather'][0]['icon'],
                'humidity' => $closestForecast['main']['humidity'],
                'wind_speed' => round($closestForecast['wind']['speed'] * 3.6, 1),
                'city' => $data['city']['name']
            ];
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner un tableau avec l'erreur pour debug
            return [
                'available' => false,
                'error' => true,
                'message' => 'Unable to fetch weather data: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Retourne l'emoji correspondant à l'icône météo
     */
    public function getWeatherEmoji(string $icon): string
    {
        $emojiMap = [
            '01d' => '☀️', '01n' => '🌙',
            '02d' => '⛅', '02n' => '☁️',
            '03d' => '☁️', '03n' => '☁️',
            '04d' => '☁️', '04n' => '☁️',
            '09d' => '🌧️', '09n' => '🌧️',
            '10d' => '🌦️', '10n' => '🌧️',
            '11d' => '⛈️', '11n' => '⛈️',
            '13d' => '❄️', '13n' => '❄️',
            '50d' => '🌫️', '50n' => '🌫️',
        ];
        
        return $emojiMap[$icon] ?? '🌤️';
    }
}
