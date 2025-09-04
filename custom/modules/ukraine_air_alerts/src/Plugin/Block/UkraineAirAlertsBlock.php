<?php

namespace Drupal\ukraine_air_alerts\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ukraine_air_alerts\Service\AlertsApiClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Ukraine Air Alerts Map' Block.
 *
 * @Block(
 *   id = "ukraine_air_alerts_map",
 *   admin_label = @Translation("Ukraine Air Alerts Map"),
 *   category = @Translation("Ukraine Air Alerts"),
 * )
 */
class UkraineAirAlertsBlock extends BlockBase implements ContainerFactoryPluginInterface {

    /**
     * The alerts API client service.
     *
     * @var \Drupal\ukraine_air_alerts\Service\AlertsApiClient
     */
    protected $alertsApiClient;

    /**
     * Constructs a UkraineAirAlertsBlock object.
     *
     * @param array $configuration
     *   The plugin configuration.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param \Drupal\ukraine_air_alerts\Service\AlertsApiClient $alerts_api_client
     *   The alerts API client service.
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, AlertsApiClient $alerts_api_client) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->alertsApiClient = $alerts_api_client;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('ukraine_air_alerts.api_client')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(): array {
        $alerts = $this->alertsApiClient->getCurrentAlerts();

        return [
            '#theme' => 'ukraine_air_alerts_map',
            '#alerts' => $alerts,
            '#attached' => [
                'library' => [
                    'ukraine_air_alerts/map',
                ],
                'drupalSettings' => [
                    'ukraineAirAlerts' => [
                        'alerts' => $alerts,
                        'refreshInterval' => 300000, // 5 minutes
                        'apiEndpoint' => '/api/air-alerts',
                    ],
                ],
            ],
            '#cache' => [
                'max-age' => 300, // 5 minutes
                'tags' => ['ukraine_air_alerts'],
            ],
        ];
    }
}