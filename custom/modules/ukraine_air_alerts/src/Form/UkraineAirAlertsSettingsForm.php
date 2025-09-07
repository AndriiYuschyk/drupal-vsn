<?php

namespace Drupal\ukraine_air_alerts\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for Ukraine Air Alerts settings.
 */
class AirAlertsSettingsForm extends ConfigFormBase {

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
            '#description' => $this->t('Введіть свій API-токен з alerts.in.ua'),
            '#default_value' => $config->get('api_token'),
            '#required' => TRUE,
        ];

        $form['refresh_interval'] = [
            '#type' => 'number',
            '#title' => $this->t('Refresh Interval (seconds)'),
            '#description' => $this->t('Як часто оновлювати карту (рекомендовано: 15-120 секунд)'),
            '#default_value' => $config->get('refresh_interval') ?: 15,
            '#min' => 15,
            '#max' => 300,
            '#required' => TRUE,
        ];

        $form['help'] = [
            '#type' => 'details',
            '#title' => $this->t('Як це використовувати?'),
            '#open' => FALSE,
        ];

        $form['help']['instructions'] = [
            '#markup' => $this->t('
                <h3>Отримання ключа alerts.in.ua:</h3>
                <ol>
                  <li>Перейдіть <a href="https://devs.alerts.in.ua/" target="_blank">https://devs.alerts.in.ua/</a></li>
                  <li>Потім відкрийте посилання і залиште <a href="https://alerts.in.ua/api-request" target="_blank">Запит на API</a></li>
                  <li>Заповність "Ім\'я", "Email", "Галузь використання", "Згоду на використання" та натисніть "Подати запит"</li>
                  <li>Невдовзі, на пошту прийде відповідь з ключем API для доступу до порталу alerts.in.ua</li>
                  <li>Скопіюйте ключ API та вставте його вище</li>
                </ol>
                <h3>Використання:</h3>
                <ul>
                  <li>Перегляньте віджет за адресою <a href="/ukraine-air-alerts">/ukraine-air-alerts</a></li>
                  <li>Створіть блок для відображення віджета будь-де на вашому сайті</li>
                  <li>Користувачі бачитимуть актуальний стан поширення повітряної тривоги територієї України</li>
                  <li>Дані карти оновлюватимуться кожні 15 секунд (вказані в налаштуваннях за-замовчуванням)</li>
                </ul>
              '),
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