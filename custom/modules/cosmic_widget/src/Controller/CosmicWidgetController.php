<?php

namespace Drupal\cosmic_widget\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Controller for Cosmic Widget.
 */
class CosmicWidgetController extends ControllerBase {

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
     * The logger factory.
     *
     * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
     */
    protected $loggerFactory;

    /**
     * The config factory.
     *
     * @var \Drupal\Core\Config\ConfigFactoryInterface
     */
    protected $configFactory;

    CONST string NASA_DEMO_KEY = 'DEMO_KEY';
    CONST string NASA_DEFAULT_FACT = '✨ Did you know? This image comes from NASA APOD.';

    /**
     * Constructs a CosmicWidgetController object.
     */
    public function __construct(
        ConfigFactoryInterface $config_factory,
        ClientInterface $http_client,
        CacheBackendInterface $cache,
        LoggerChannelFactoryInterface $logger_factory
    ) {
        $this->configFactory = $config_factory;
        $this->httpClient = $http_client;
        $this->cache = $cache;
        $this->loggerFactory = $logger_factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('config.factory'),
            $container->get('http_client'),
            $container->get('cache.default'),
            $container->get('logger.factory')
        );
    }

    /**
     * Displays the cosmic widget page.
     */
    public function page() {
        $config = $this->configFactory->get('cosmic_widget.settings');

        if (empty($config->get('nasa_api_key'))) {
            return [
                '#markup' => '<div class="messages messages--warning">' .
                    $this->t('Віджет NASA «Астрономічна картина дня» з цікавими фактами з Вікіданих не налаштовано. Будь ласка, налаштуйте свій ключ NASA API у <a href="@url">конфігурацію</a>.', [
                        '@url' => Url::fromRoute('cosmic_widget.settings')->toString()
                    ]) . '</div>',
            ];
        }

        $data = $this->getCosmicData();
        return [
            '#theme' => 'cosmic_widget',
            '#image_url' => $data['image_url'] ?? '',
            '#image_title' => $data['image_title'] ?? '',
            '#image_explanation' => $data['image_explanation'] ?? '',
            '#fact' => $data['fact'] ?? '',
            '#date' => $data['date'] ?? '',
            '#attached' => [
                'library' => [
                    'cosmic_widget/cosmic_widget_assets',
                ],
                'drupalSettings' => [
                    'cosmicWidget' => [
                        'nasaApiKey' => $config->get('nasa_api_token'),
                        'nasaApiUrl' => $config->get('nasa_api_url'),
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns new cosmic data via AJAX.
     */
    public function getNewImage() {
        // Clear cache to get fresh data.
        $this->cache->delete('cosmic_widget:data');
        $data = $this->getCosmicData();

        return new JsonResponse($data);
    }

    /**
     * Return generated random date for request
     */
    public function generateRandomDate() {
        return date('Y-m-d', strtotime('-' . rand(0, 365) . ' days'));
    }

    /**
     * Fetches cosmic data from NASA API.
     */
    protected function getCosmicData() {
        // Try to get cached data.
        if ($cache = $this->cache->get('cosmic_widget:data')) {
            return $cache->data;
        }

        $config = $this->configFactory->get('cosmic_widget.settings');
        $api_key = $config->get('nasa_api_key') ?? self::NASA_DEMO_KEY;

        $data = [
            'image_url' => '',
            'image_title' => '',
            'image_explanation' => '',
            'fact' => '',
            'date' => date('Y-m-d'),
        ];

        try {
            $nasa_url = "{$config->get('nasa_api_url')}?api_key={$api_key}&date={$this->generateRandomDate()}";

            $response = $this->httpClient->request('GET', $nasa_url, ['timeout' => 10]);
            $nasa_data = json_decode($response->getBody(), TRUE);

            if (!empty($nasa_data) && isset($nasa_data['url']) && ($nasa_data['media_type'] ?? 'image') === 'image') {
                $data['image_url'] = $nasa_data['url'];
                $data['image_title'] = $nasa_data['title'] ?? '';
                $data['image_explanation'] = $nasa_data['explanation'] ?? '';
                $data['date'] = $nasa_data['date'] ?? date('Y-m-d');
                $data['fact'] = self::NASA_DEFAULT_FACT;
            }
        }
        catch (RequestException $e) {
            $this->loggerFactory
                ->get('cosmic_widget')
                ->error('NASA API error: @message', ['@message' => $e->getMessage()]);
        }

        $this->cache->set('cosmic_widget:data', $data, time() + 3600);

        return $data;
    }

}