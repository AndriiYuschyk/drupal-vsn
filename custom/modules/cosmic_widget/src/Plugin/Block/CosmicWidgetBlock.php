<?php

namespace Drupal\cosmic_widget\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\cosmic_widget\Controller\CosmicWidgetController;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\ClientInterface;

/**
 * Provides a 'Cosmic Widget' Block.
 *
 * @Block(
 *   id = "cosmic_widget_block",
 *   admin_label = @Translation("Cosmic Widget"),
 *   category = @Translation("Custom"),
 * )
 */
class CosmicWidgetBlock extends BlockBase implements ContainerFactoryPluginInterface {

    /**
     * The cosmic widget controller.
     *
     * @var \Drupal\cosmic_widget\Controller\CosmicWidgetController
     */
    protected $cosmicController;

    /**
     * Constructs a new CosmicWidgetBlock instance.
     *
     * @param array $configuration
     *   The plugin configuration.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
     *   The config factory.
     * @param \GuzzleHttp\ClientInterface $http_client
     *   The HTTP client.
     * @param \Drupal\Core\Cache\CacheBackendInterface $cache
     *   The cache backend.
     * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
     *   The logger factory.
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, ClientInterface $http_client, CacheBackendInterface $cache, LoggerChannelFactoryInterface $logger_factory) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->cosmicController = new CosmicWidgetController($config_factory, $http_client, $cache, $logger_factory);
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('config.factory'),
            $container->get('http_client'),
            $container->get('cache.default'),
            $container->get('logger.factory')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build() {
        // Use reflection to access protected method
        $reflection = new \ReflectionClass($this->cosmicController);
        $method = $reflection->getMethod('getCosmicData');
        $method->setAccessible(TRUE);
        $data = $method->invoke($this->cosmicController);

        return [
            '#theme' => 'cosmic_widget',
            '#image_url' => $data['image_url'] ?? '',
            '#image_title' => $data['image_title'] ?? '',
            '#image_explanation' => $data['image_explanation'] ?? '',
            '#date' => $data['date'] ?? '',
            '#attached' => [
                'library' => [
                    'cosmic_widget/cosmic_widget_assets',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheTags() {
        return Cache::mergeTags(parent::getCacheTags(), ['cosmic_widget:data']);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheMaxAge() {
        // Cache for 1 hour
        return 3600;
    }

}