<?php

namespace Drupal\ukraine_air_alerts\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ukraine_air_alerts\Service\AlertsApiClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form for Ukraine Air Alerts settings.
 */
class UkraineAirAlertsSettingsForm extends ConfigFormBase {

    /**
     * The alerts API client service.
     *
     * @var \Drupal\ukraine_air_alerts\Service\AlertsApiClient
     */
    protected $alertsApiClient;

    /**
     * Constructs a UkraineAirAlertsSettingsForm object.
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
     * {@inheritdoc}
     */
    protected function getEditableConfigNames(): array {
        return ['ukraine_air_alerts.settings'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId(): string {
        return 'ukraine_air_alerts_settings_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state): array {
        $config = $this->config('ukraine_air_alerts.settings');

        $form['api_settings'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('API Settings'),
        ];

        $form['api_settings']['refresh_interval'] = [
            '#type' => 'number',
            '#title' => $this->t('Refresh Interval (seconds)'),
            '#description' => $this->t('How often the map should refresh alerts data automatically.'),
            '#default_value' => $config->get('refresh_interval') ?? 300,
            '#min' => 60,
            '#max' => 3600,
            '#step' => 30,
        ];

        $form['api_settings']['cache_ttl'] = [
            '#type' => 'number',
            '#title' => $this->t('Cache TTL (seconds)'),
            '#description' => $this->t('How long to cache API responses.'),
            '#default_value' => $config->get('cache_ttl') ?? 300,
            '#min' => 60,
            '#max' => 1800,
            '#step' => 30,
        ];

        $form['display_settings'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Display Settings'),
        ];

        $form['display_settings']['show_legend'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Show legend'),
            '#description' => $this->t('Display color legend on the map.'),
            '#default_value' => $config->get('show_legend') ?? TRUE,
        ];

        $form['display_settings']['show_last_updated'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Show last updated time'),
            '#description' => $this->t('Display when the alerts data was last updated.'),
            '#default_value' => $config->get('show_last_updated') ?? TRUE,
        ];

        $form['display_settings']['enable_auto_refresh'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Enable auto-refresh'),
            '#description' => $this->t('Automatically refresh alerts data on the map page.'),
            '#default_value' => $config->get('enable_auto_refresh') ?? TRUE,
        ];

        $form['colors'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Color Settings'),
        ];

        $form['colors']['color_alert'] = [
            '#type' => 'color',
            '#title' => $this->t('Alert Color'),
            '#description' => $this->t('Color for regions with active air alerts.'),
            '#default_value' => $config->get('color_alert') ?? '#ff0000',
        ];

        $form['colors']['color_clear'] = [
            '#type' => 'color',
            '#title' => $this->t('Clear Color'),
            '#description' => $this->t('Color for regions without alerts.'),
            '#default_value' => $config->get('color_clear') ?? '#00ff00',
        ];

        $form['colors']['color_unknown'] = [
            '#type' => 'color',
            '#title' => $this->t('Unknown Color'),
            '#description' => $this->t('Color for regions with unknown status.'),
            '#default_value' => $config->get('color_unknown') ?? '#cccccc',
        ];

        $form['actions']['clear_cache'] = [
            '#type' => 'submit',
            '#value' => $this->t('Clear Cache'),
            '#submit' => ['::clearCache'],
            '#weight' => 10,
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state): void {
        $config = $this->config('ukraine_air_alerts.settings');

        $config
            ->set('refresh_interval', $form_state->getValue('refresh_interval'))
            ->set('cache_ttl', $form_state->getValue('cache_ttl'))
            ->set('show_legend', $form_state->getValue('show_legend'))
            ->set('show_last_updated', $form_state->getValue('show_last_updated'))
            ->set('enable_auto_refresh', $form_state->getValue('enable_auto_refresh'))
            ->set('color_alert', $form_state->getValue('color_alert'))
            ->set('color_clear', $form_state->getValue('color_clear'))
            ->set('color_unknown', $form_state->getValue('color_unknown'))
            ->save();

        parent::submitForm($form, $form_state);
    }

    /**
     * Clear cache submit handler.
     */
    public function clearCache(array &$form, FormStateInterface $form_state): void {
        $this->alertsApiClient->clearCache();
        $this->messenger()->addMessage($this->t('Cache has been cleared.'));
    }
}