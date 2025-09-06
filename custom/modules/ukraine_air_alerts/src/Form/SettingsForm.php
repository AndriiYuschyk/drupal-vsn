<?php

namespace Drupal\ukraine_air_alerts\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for Ukraine Air Alerts settings.
 */
class SettingsForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return ['ukraine_air_alerts.settings'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'ukraine_air_alerts_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = $this->config('ukraine_air_alerts.settings');

        $form['api_token'] = [
            '#type' => 'textfield',
            '#title' => $this->t('API Token'),
            '#description' => $this->t('Enter your API token from alerts.in.ua'),
            '#default_value' => $config->get('api_token'),
            '#required' => TRUE,
        ];

        $form['refresh_interval'] = [
            '#type' => 'number',
            '#title' => $this->t('Refresh Interval (seconds)'),
            '#description' => $this->t('How often to update the map (recommended: 15-120 seconds)'),
            '#default_value' => $config->get('refresh_interval') ?: 15,
            '#min' => 15,
            '#max' => 300,
            '#required' => TRUE,
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        parent::submitForm($form, $form_state);

        $this->config('ukraine_air_alerts.settings')
            ->set('api_token', $form_state->getValue('api_token'))
            ->set('refresh_interval', $form_state->getValue('refresh_interval'))
            ->save();
    }
}