<?php

namespace Drupal\ukraine_air_alerts\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a Ukraine Air Alerts Map block.
 *
 * @Block(
 *   id = "ukraine_air_alerts_map",
 *   admin_label = @Translation("Ukraine Air Alerts Map"),
 *   category = @Translation("Custom")
 * )
 */
class UkraineAirAlertsBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        return [
                'show_legend' => TRUE,
                'show_status' => TRUE,
                'compact_mode' => FALSE,
            ] + parent::defaultConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $form = parent::blockForm($form, $form_state);
        $config = $this->getConfiguration();

        $form['show_legend'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Show legend'),
            '#default_value' => $config['show_legend'],
            '#description' => $this->t('Display the color legend for alert statuses.'),
        ];

        $form['show_status'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Show status bar'),
            '#default_value' => $config['show_status'],
            '#description' => $this->t('Display connection status and refresh countdown.'),
        ];

        $form['compact_mode'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Compact mode'),
            '#default_value' => $config['compact_mode'],
            '#description' => $this->t('Display a smaller, more compact version of the map.'),
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        parent::blockSubmit($form, $form_state);
        $this->configuration['show_legend'] = $form_state->getValue('show_legend');
        $this->configuration['show_status'] = $form_state->getValue('show_status');
        $this->configuration['compact_mode'] = $form_state->getValue('compact_mode');
    }

    /**
     * {@inheritdoc}
     */
    public function build() {
        $config = \Drupal::config('ukraine_air_alerts.settings');

        if (empty($config->get('api_token'))) {
            return [
                '#markup' => '<div class="messages messages--warning">' .
                    $this->t('Ukraine Air Alerts модуль не налаштовано. Будь ласка, налаштуйте свій токен API у <a href="@url">конфігурації</a>.', [
                        '@url' => Url::fromRoute('ukraine_air_alerts.settings')->toString()
                    ]) . '</div>',
            ];
        }

        return [
            '#theme' => 'ukraine_air_alerts_map',
            '#show_legend' => $this->configuration['show_legend'],
            '#show_status' => $this->configuration['show_status'],
            '#compact_mode' => $this->configuration['compact_mode'],
            '#attached' => [
                'library' => [
                    'ukraine_air_alerts/map',
                ],
                'drupalSettings' => [
                    'ukraineAirAlerts' => [
                        'apiUrl' => Url::fromRoute('ukraine_air_alerts.api')->toString(),
                        'refreshInterval' => ($config->get('refresh_interval') ?: 60) * 1000, // Convert to milliseconds
                        'compactMode' => $this->configuration['compact_mode'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheMaxAge() {
        // Cache for 30 seconds to ensure fresh data
        return 30;
    }
}