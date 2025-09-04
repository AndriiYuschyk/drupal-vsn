<?php

namespace Drupal\ukraine_air_alerts\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ukraine_air_alerts\Service\AlertsApiClient;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for Ukraine Air Alerts.
 */
class UkraineAirAlertsController extends ControllerBase {

    /**
     * The alerts API client service.
     *
     * @var \Drupal\ukraine_air_alerts\Service\AlertsApiClient
     */
    protected $alertsApiClient;

    /**
     * Constructs a UkraineAirAlertsController object.
     *
     * @param \Drupal\ukraine_air_alerts\Service\AlertsApiClient $alerts_api_client
     *   The alerts API client service.
     */
    public function __construct(AlertsApiClient $alerts_api_client) {
        $this->alertsApiClient = $alerts_api_client;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('ukraine_air_alerts.api_client')
        );
    }

    /**
     * Displays the air alerts map page.
     *
     * @return array
     *   A render array for the map page.
     */
    public function map(): array {
        $alerts = $this->alertsApiClient->getCurrentAlerts();

        $build = [
            '#theme' => 'ukraine_air_alerts_map',
            '#alerts' => $alerts,
            '#attached' => [
                'library' => [
                    'ukraine_air_alerts/map',
                ],
                'drupalSettings' => [
                    'ukraineAirAlerts' => [
                        'alerts' => $alerts,
                        'refreshInterval' => 300000, // 5 minutes in milliseconds
                        'apiEndpoint' => '/api/air-alerts',
                    ],
                ],
            ],
        ];

        return $build;
    }

    /**
     * Returns current alerts data as JSON.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *   JSON response with alerts data.
     */
    public function api(): JsonResponse {
        $alerts = $this->alertsApiClient->getCurrentAlerts();

        return new JsonResponse([
            'status' => 'success',
            'data' => $alerts,
            'timestamp' => time(),
            'last_updated' => date('Y-m-d H:i:s'),
        ]);
    }
}