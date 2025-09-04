<?php

namespace Drupal\ukraine_air_alerts\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Logger\LoggerChannelInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Service for fetching air alerts data from alerts.in.ua API.
 */
class AlertsApiClient {

    /**
     * The HTTP client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $httpClient;

    /**
     * The cache backend.
     *
     * @var \Drupal\Core\Cache\CacheBackendInterface
     */
    protected $cache;

    /**
     * The logger channel.
     *
     * @var \Drupal\Core\Logger\LoggerChannelInterface
     */
    protected $logger;

    /**
     * API base URL.
     *
     * @var string
     */
    protected $apiBaseUrl = 'https://alerts.in.ua/api';

    /**
     * Cache TTL in seconds (5 minutes).
     *
     * @var int
     */
    protected $cacheTtl = 300;

    /**
     * Ukrainian regions mapping.
     *
     * @var array
     */
    protected $regions = [
        'Вінницька область' => ['id' => 5, 'name' => 'Вінницька область'],
        'Волинська область' => ['id' => 3, 'name' => 'Волинська область'],
        'Дніпропетровська область' => ['id' => 4, 'name' => 'Дніпропетровська область'],
        'Донецька область' => ['id' => 9, 'name' => 'Донецька область'],
        'Житомирська область' => ['id' => 18, 'name' => 'Житомирська область'],
        'Закарпатська область' => ['id' => 21, 'name' => 'Закарпатська область'],
        'Запорізька область' => ['id' => 14, 'name' => 'Запорізька область'],
        'Івано-Франківська область' => ['id' => 26, 'name' => 'Івано-Франківська область'],
        'Київська область' => ['id' => 32, 'name' => 'Київська область'],
        'Кіровоградська область' => ['id' => 35, 'name' => 'Кіровоградська область'],
        'Луганська область' => ['id' => 13, 'name' => 'Луганська область'],
        'Львівська область' => ['id' => 15, 'name' => 'Львівська область'],
        'Миколаївська область' => ['id' => 19, 'name' => 'Миколаївська область'],
        'Одеська область' => ['id' => 23, 'name' => 'Одеська область'],
        'Полтавська область' => ['id' => 25, 'name' => 'Полтавська область'],
        'Рівненська область' => ['id' => 56, 'name' => 'Рівненська область'],
        'Сумська область' => ['id' => 59, 'name' => 'Сумська область'],
        'Тернопільська область' => ['id' => 61, 'name' => 'Тернопільська область'],
        'Харківська область' => ['id' => 64, 'name' => 'Харківська область'],
        'Херсонська область' => ['id' => 65, 'name' => 'Херсонська область'],
        'Хмельницька область' => ['id' => 68, 'name' => 'Хмельницька область'],
        'Черкаська область' => ['id' => 71, 'name' => 'Черкаська область'],
        'Чернівецька область' => ['id' => 73, 'name' => 'Чернівецька область'],
        'Чернігівська область' => ['id' => 74, 'name' => 'Чернігівська область'],
        'м. Київ' => ['id' => 31, 'name' => 'м. Київ'],
    ];

    /**
     * Constructs an AlertsApiClient object.
     *
     * @param \GuzzleHttp\ClientInterface $http_client
     *   The HTTP client.
     * @param \Drupal\Core\Cache\CacheBackendInterface $cache
     *   The cache backend.
     * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
     *   The logger channel.
     */
    public function __construct(ClientInterface $http_client, CacheBackendInterface $cache, LoggerChannelInterface $logger) {
        $this->httpClient = $http_client;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * Get current air alerts status.
     *
     * @return array
     *   Array of alerts data or empty array on failure.
     */
    public function getCurrentAlerts(): array {
        $cache_key = 'ukraine_air_alerts:current';

        // Try to get from cache first
        if ($cached = $this->cache->get($cache_key)) {
            return $cached->data;
        }

        try {
            $response = $this->httpClient->request('GET', $this->apiBaseUrl . '/states');
            $data = json_decode($response->getBody()->getContents(), TRUE);

            if (!$data || !isset($data['states'])) {
                $this->logger->error('Invalid API response structure');
                return [];
            }

            $alerts_data = $this->processAlertsData($data['states']);

            // Cache the result
            $this->cache->set($cache_key, $alerts_data, time() + $this->cacheTtl);

            return $alerts_data;

        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch alerts data: @message', ['@message' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Process raw alerts data from API.
     *
     * @param array $states
     *   Raw states data from API.
     *
     * @return array
     *   Processed alerts data.
     */
    protected function processAlertsData(array $states): array {
        $processed = [];

        foreach ($states as $state) {
            if (!isset($state['id']) || !isset($state['name'])) {
                continue;
            }

            $region_name = $state['name'];
            $alert_status = $state['alert'] ?? false;
            $changed = $state['changed'] ?? null;

            $processed[$region_name] = [
                'id' => $state['id'],
                'name' => $region_name,
                'alert' => $alert_status,
                'changed' => $changed,
                'status_class' => $alert_status ? 'alert-active' : 'alert-clear',
                'status_text' => $alert_status ? 'Повітряна тривога' : 'Відбій',
            ];
        }

        return $processed;
    }

    /**
     * Get historical alerts data.
     *
     * @param string $date
     *   Date in Y-m-d format.
     *
     * @return array
     *   Historical alerts data.
     */
    public function getHistoricalAlerts(string $date): array {
        $cache_key = 'ukraine_air_alerts:history:' . $date;

        if ($cached = $this->cache->get($cache_key)) {
            return $cached->data;
        }

        try {
            $response = $this->httpClient->request('GET', $this->apiBaseUrl . '/history/' . $date);
            $data = json_decode($response->getBody()->getContents(), TRUE);

            if (!$data) {
                return [];
            }

            // Cache historical data for longer (1 hour)
            $this->cache->set($cache_key, $data, time() + 3600);

            return $data;

        } catch (GuzzleException $e) {
            $this->logger->error('Failed to fetch historical alerts data: @message', ['@message' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get all Ukrainian regions.
     *
     * @return array
     *   Array of regions.
     */
    public function getRegions(): array {
        return $this->regions;
    }

    /**
     * Clear alerts cache.
     */
    public function clearCache(): void {
        $this->cache->deleteMultiple([
            'ukraine_air_alerts:current',
        ]);
    }
}