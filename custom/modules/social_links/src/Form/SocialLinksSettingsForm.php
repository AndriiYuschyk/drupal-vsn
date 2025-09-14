<?php

namespace Drupal\social_links\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SocialLinksSettingsForm.
 */
class SocialLinksSettingsForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId(): string {
        return 'social_links_settings_form';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames(): array {
        return ['social_links.settings'];
    }

    /**
     * Default social settings
     */
    private function getSocialNetworks(): array {
        return [
            'facebook' => ['enabled' => false, 'url' => '', 'weight' => 1, 'icon' => 'facebook'],
            'twitter' => ['enabled' => false, 'url' => '', 'weight' => 2, 'icon' => 'twitter'],
            'instagram' => ['enabled' => false, 'url' => '', 'weight' => 3, 'icon' => 'instagram'],
            'youtube' => ['enabled' => false, 'url' => '', 'weight' => 4, 'icon' => 'youtube'],
            'linkedin' => ['enabled' => false, 'url' => '', 'weight' => 5, 'icon' => 'linkedin'],
            'telegram' => ['enabled' => false, 'url' => '', 'weight' => 6, 'icon' => 'telegram'],
            'tiktok' => ['enabled' => false, 'url' => '', 'weight' => 7, 'icon' => 'tiktok'],
            'discord' => ['enabled' => false, 'url' => '', 'weight' => 8, 'icon' => 'discord'],
            'whatsapp' => ['enabled' => false, 'url' => '', 'weight' => 9, 'icon' => 'whatsapp'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = $this->config('social_links.settings');
        $settings = $config->get() ?: [];

        if (isset($settings['links'])) {
            $settings = $settings['links'];
        }

        $social_networks = $this->getSocialNetworks();

        $form['social_networks'] = [
            '#type' => 'container',
            '#tree' => TRUE,
        ];

        foreach ($social_networks as $network => $defaults) {
            $network_settings = $settings[$network] ?? $defaults;

            $form['social_networks'][$network] = [
                '#type' => 'details',
                '#title' => ucfirst($network),
                '#open' => !empty($network_settings['enabled']),
            ];

            $form['social_networks'][$network]['enabled'] = [
                '#type' => 'checkbox',
                '#title' => $this->t('Увімкнути @network', ['@network' => $network]),
                '#default_value' => !empty($network_settings['enabled']) ? 1 : 0,
            ];

            $form['social_networks'][$network]['url'] = [
                '#type' => 'url',
                '#title' => $this->t('URL'),
                '#default_value' => $network_settings['url'] ?? '',
                '#states' => [
                    'required' => [
                        ':input[name="social_networks[' . $network . '][enabled]"]' => ['checked' => TRUE],
                    ],
                ],
            ];

            $form['social_networks'][$network]['icon'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Іконка'),
                '#default_value' => $network_settings['icon'] ?? $defaults['icon'],
                '#description' => $this->t('Назва файлу іконки без розширення (наприклад: facebook)'),
                '#maxlength' => 255,
            ];

            $form['social_networks'][$network]['weight'] = [
                '#type' => 'number',
                '#title' => $this->t('Вага'),
                '#default_value' => $network_settings['weight'] ?? $defaults['weight'],
                '#min' => 0,
                '#max' => 100,
            ];
        }

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $values = $form_state->getValues();

        if (!isset($values['social_networks'])) {
            $form_state->setError($form, $this->t('Помилка: дані соціальних мереж не знайдено.'));
            return;
        }

        $social_networks_data = $values['social_networks'];

        foreach ($social_networks_data as $network => $network_data) {
            if (!empty($network_data['enabled']) && empty($network_data['url'])) {
                $form_state->setError($form['social_networks'][$network]['url'],
                    $this->t('URL є обов\'язковим для увімкненої соціальної мережі @network.',
                        ['@network' => $network]));
            }
        }

        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $config = $this->config('social_links.settings');
        $values = $form_state->getValues();
        $settings = [];

        if (isset($values['social_networks'])) {
            foreach ($values['social_networks'] as $network => $network_data) {
                $settings[$network] = [
                    'enabled' => !empty($network_data['enabled']),
                    'url' => trim($network_data['url'] ?? ''),
                    'icon' => trim($network_data['icon'] ?? $network), // Додаємо icon
                    'weight' => (int)($network_data['weight'] ?? 0),
                ];
            }
        }

        $config_data = [
            'links' => $settings
        ];

        $current_config = $config->get();
        if (isset($current_config['_core'])) {
            $config_data['_core'] = $current_config['_core'];
        }

        $config->setData($config_data)->save();

        $this->messenger()->addMessage($this->t('Налаштування соціальних мереж збережено.'));

        parent::submitForm($form, $form_state);
    }
}