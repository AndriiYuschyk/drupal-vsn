<?php

namespace Drupal\ukraine_air_alerts\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Url;

/**
 * Controller for Ukraine Air Alerts.
 */
class AirAlertsController extends ControllerBase {

    /**
     * The HTTP client.
     */
    protected $httpClient;

    /**
     * The cache backend.
     */
    protected $cache;

    /**
     * The logger factory.
     */
    protected $loggerFactory;

    /**
     * The config factory.
     */
    protected $configFactory;

    /**
     * Constructs a new AirAlertsController.
     */
    public function __construct(ClientInterface $http_client, CacheBackendInterface $cache, LoggerChannelFactoryInterface $logger_factory, ConfigFactoryInterface $config_factory) {
        $this->httpClient = $http_client;
        $this->cache = $cache;
        $this->loggerFactory = $logger_factory;
        $this->configFactory = $config_factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('http_client'),
            $container->get('cache.default'),
            $container->get('logger.factory'),
            $container->get('config.factory')
        );
    }

    /**
     * Display the map page.
     */
    public function mapPage() {
        $config = $this->configFactory->get('ukraine_air_alerts.settings');

        if (empty($config->get('api_token'))) {
            return [
                '#markup' => '<div class="messages messages--warning">' .
                    $this->t('Ukraine Air Alerts module is not configured. Please set up your API token in the <a href="@url">configuration</a>.', [
                        '@url' => Url::fromRoute('ukraine_air_alerts.settings')->toString()
                    ]) . '</div>',
            ];
        }

        return [
            '#theme' => 'ukraine_air_alerts_map',
            '#show_legend' => TRUE,
            '#show_status' => TRUE,
            '#compact_mode' => FALSE,
            '#attached' => [
                'library' => [
                    'ukraine_air_alerts/map',
                ],
                'drupalSettings' => [
                    'ukraineAirAlerts' => [
                        'apiUrl' => Url::fromRoute('ukraine_air_alerts.api')->toString(),
                        'refreshInterval' => ($config->get('refresh_interval') ?: 60) * 1000,
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns the air alerts status.
     */
    public function getAlertsStatus() {
        $cache_key = 'ukraine_air_alerts:status';
        $cache_data = $this->cache->get($cache_key);

        // Return cached data if available and not expired (cache for 30 seconds)
        if ($cache_data && $cache_data->expire > \Drupal::time()->getRequestTime()) {
            return new JsonResponse($cache_data->data);
        }

        $config = $this->configFactory->get('ukraine_air_alerts.settings');
        $api_token = $config->get('api_token');

        if (empty($api_token)) {
            return new JsonResponse(['error' => 'API token not configured'], 500);
        }

        try {
            $response = $this->httpClient->request('GET', 'https://api.alerts.in.ua/v1/iot/active_air_raid_alerts_by_oblast.json', [
                'query' => ['token' => $api_token],
                'timeout' => 10,
                'headers' => [
                    'User-Agent' => 'Drupal-Ukraine-Air-Alerts/1.0',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), TRUE);

            if ($data) {
                $processed_data = $this->processAlertsData($data);

                // Cache for 30 seconds
                $this->cache->set($cache_key, $processed_data, \Drupal::time()->getRequestTime() + 30);

                return new JsonResponse($processed_data);
            } else {
                $this->loggerFactory->get('ukraine_air_alerts')->error('Invalid API response format');
                return new JsonResponse(['error' => 'Invalid API response'], 500);
            }

        } catch (\Exception $e) {
            $this->loggerFactory->get('ukraine_air_alerts')->error('API request failed: @message', ['@message' => $e->getMessage()]);
            return new JsonResponse(['error' => 'API request failed'], 500);
        }
    }

    /**
     * Process the alerts data string into structured data.
     */
    private function processAlertsData($alerts_string) {
        $regions = [
            'Автономна Республіка Крим',
            'Волинська область',
            'Вінницька область',
            'Дніпропетровська область',
            'Донецька область',
            'Житомирська область',
            'Закарпатська область',
            'Запорізька область',
            'Івано-Франківська область',
            'м. Київ',
            'Київська область',
            'Кіровоградська область',
            'Луганська область',
            'Львівська область',
            'Миколаївська область',
            'Одеська область',
            'Полтавська область',
            'Рівненська область',
            'м. Севастополь',
            'Сумська область',
            'Тернопільська область',
            'Харківська область',
            'Херсонська область',
            'Хмельницька область',
            'Черкаська область',
            'Чернівецька область',
            'Чернігівська область'
        ];

        $result = [];
        $alerts_array = str_split($alerts_string);

        for ($i = 0; $i < count($regions) && $i < count($alerts_array); $i++) {
            $status = $alerts_array[$i];
            $alert_status = 'none';

            switch ($status) {
                case 'A':
                    $alert_status = 'full';
                    break;
                case 'P':
                    $alert_status = 'partial';
                    break;
                case 'N':
                    $alert_status = 'none';
                    break;
            }

            $result[] = [
                'region' => $regions[$i],
                'status' => $alert_status,
                'code' => $status
            ];
        }

        return [
            'regions' => $result,
            'timestamp' => time(),
            'raw' => $alerts_string
        ];
    }
}