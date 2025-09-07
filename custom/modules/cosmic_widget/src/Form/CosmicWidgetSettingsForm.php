<?php

namespace Drupal\cosmic_widget\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Cosmic Widget settings for this site.
 */
class CosmicWidgetSettingsForm extends ConfigFormBase {

    CONST string NASA_API_URL = 'https://api.nasa.gov/planetary/apod';

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'cosmic_widget_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return ['cosmic_widget.settings'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = $this->config('cosmic_widget.settings');

        $form['nasa_api_key'] = [
            '#type' => 'textfield',
            '#title' => $this->t('NASA API KEY:'),
            '#description' => $this->t('Введіть свій ключ API NASA. Ви можете отримати його за адресою <a href="@url" target="_blank">@url</a>. Залиште поле порожнім, щоб використовувати демо-ключ - "DEMO_KEY" (обмежена кількість запитів).', [
                '@url' => 'https://api.nasa.gov/',
            ]),
            '#default_value' => $config->get('nasa_api_key') ?? "DEMO_KEY",
            '#size' => 60,
            '#maxlength' => 128,
            '#required' => TRUE,
        ];

        $form['nasa_api_url'] = [
            '#type' => 'textfield',
            '#title' => $this->t('NASA API Url:'),
            '#description' => $this->t('Посилання на API куди будуть здійснюватися запити'),
            '#default_value' => $config->get('nasa_api_url') ?? static::NASA_API_URL,
            '#min' => 10,
            '#max' => 130,
            '#required' => TRUE,
        ];

        $form['help'] = [
            '#type' => 'details',
            '#title' => $this->t('Як це використовувати?'),
            '#open' => FALSE,
        ];

        $form['help']['instructions'] = [
            '#markup' => $this->t('
                <h3>Отримання ключа API NASA:</h3>
                <ol>
                  <li>Перейдіть <a href="https://api.nasa.gov/" target="_blank">NASA API Portal</a></li>
                  <li>Заповність "First Name", "Last Name" і "Email", та натисніть "Sing up"</li>
                  <li>Невдовзі, на пошту прийде відповідь з ключем API для доступу до порталу NASA</li>
                  <li>Скопіюйте ключ API та вставте його вище</li>
                </ol>
                <h3>Використання:</h3>
                <ul>
                  <li>Перегляньте віджет за адресою <a href="/cosmic-widget">/cosmic-widget</a></li>
                  <li>Створіть блок для відображення віджета будь-де на вашому сайті</li>
                  <li>Користувачі можуть натиснути кнопку «Отримати нове зображення»,</li>
                  <li>щоб завантажити інше зображення на будь-яку випадково згенеровану дату</li>
                </ul>
              '),
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $nasa_api_key = $form_state->getValue('nasa_api_key');

        // Basic validation of NASA API key format
        if (!empty($nasa_api_key) && !preg_match('/^[a-zA-Z0-9]{40}$/', $nasa_api_key)) {
            $form_state->setErrorByName('nasa_api_key', $this->t('Ключ API NASA повинен містити 40 символів і лише літери та цифри.'));
        }

        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $this->config('cosmic_widget.settings')
            ->set('nasa_api_key', $form_state->getValue('nasa_api_key'))
            ->set('nasa_api_url', $form_state->getValue('nasa_api_url'))
            ->save();

        // Clear cache when settings are updated
        \Drupal::cache()->delete('cosmic_widget:data');

        $this->messenger()->addMessage($this->t('Налаштування космічного віджета успішно збережено!'));

        parent::submitForm($form, $form_state);
    }
}