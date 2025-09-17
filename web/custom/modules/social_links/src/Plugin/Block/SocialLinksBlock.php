<?php

namespace Drupal\social_links\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\social_links\Enum\SocialNetwork;

/**
 * Provides a Social Links block.
 *
 * @Block(
 *   id = "social_links_block",
 *   admin_label = @Translation("Social Links"),
 *   category = @Translation("Custom")
 * )
 */
class SocialLinksBlock extends BlockBase implements ContainerFactoryPluginInterface {

    /**
     * The config factory service.
     *
     * @var \Drupal\Core\Config\ConfigFactoryInterface
     */
    protected $configFactory;

    /**
     * Constructs a new SocialLinksBlock.
     *
     * @param array $configuration
     *   The plugin configuration.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
     *   The config factory service.
     */
    public function __construct(
        array $configuration,
              $plugin_id,
              $plugin_definition,
        ConfigFactoryInterface $config_factory
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->configFactory = $config_factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('config.factory')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        return [
                'use_global_settings' => TRUE,
                'override_settings' => [],
            ] + parent::defaultConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $config = $this->getConfiguration();
        $global_config = $this->configFactory->get('social_links.settings');

        $global_config_data = $global_config->get() ?: [];
        $global_links = isset($global_config_data['links']) ? $global_config_data['links'] : [];

        $form['use_global_settings'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Використовувати глобальні налаштування'),
            '#default_value' => $config['use_global_settings'] ?? TRUE,
            '#description' => $this->t('Якщо увімкнено, буде використано налаштування з модуля Social Links.'),
        ];

        $form['override_container'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Власні налаштування блоку'),
            '#states' => [
                'visible' => [
                    ':input[name="settings[use_global_settings]"]' => ['checked' => FALSE],
                ],
            ],
        ];

        $networks = SocialNetwork::cases();
        foreach ($networks as $network) {
            $machine_name = $network->value;
            $global_settings = $global_links[$machine_name] ?? [];
            $block_settings = $config['override_settings'][$machine_name] ?? [];

            $form['override_container'][$machine_name] = [
                '#type' => 'details',
                '#title' => $network->label(),
                '#open' => FALSE,
            ];

            $form['override_container'][$machine_name]['enabled'] = [
                '#type' => 'checkbox',
                '#title' => $this->t('Увімкнути'),
                '#default_value' => $block_settings['enabled'] ?? $global_settings['enabled'] ?? FALSE,
            ];

            $form['override_container'][$machine_name]['url'] = [
                '#type' => 'url',
                '#title' => $this->t('URL'),
                '#default_value' => $block_settings['url'] ?? $global_settings['url'] ?? '',
                '#states' => [
                    'visible' => [
                        ':input[name="settings[override_container][' . $machine_name . '][enabled]"]' => ['checked' => TRUE],
                    ],
                ],
            ];

            $form['override_container'][$machine_name]['weight'] = [
                '#type' => 'number',
                '#title' => $this->t('Вага (порядок)'),
                '#default_value' => $block_settings['weight'] ?? $global_settings['weight'] ?? $network->defaultWeight(),
                '#states' => [
                    'visible' => [
                        ':input[name="settings[override_container][' . $machine_name . '][enabled]"]' => ['checked' => TRUE],
                    ],
                ],
            ];
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockValidate($form, FormStateInterface $form_state) {
        $values = $form_state->getValues();

        if (empty($values['use_global_settings'])) {
            $networks = SocialNetwork::cases();
            foreach ($networks as $network) {
                $machine_name = $network->value;
                $network_values = $values['override_container'][$machine_name] ?? [];

                if (!empty($network_values['enabled']) && empty($network_values['url'])) {
                    $form_state->setErrorByName(
                        "override_container][$machine_name][url",
                        $this->t('URL є обов\'язковим для увімкнених соціальних мереж (@network).', [
                            '@network' => $network->label()
                        ])
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $values = $form_state->getValues();

        $this->configuration['use_global_settings'] = !empty($values['use_global_settings']);

        if (empty($values['use_global_settings'])) {
            $override_settings = [];
            $networks = SocialNetwork::cases();

            foreach ($networks as $network) {
                $machine_name = $network->value;
                $network_values = $values['override_container'][$machine_name] ?? [];

                $override_settings[$machine_name] = [
                    'enabled' => !empty($network_values['enabled']),
                    'url' => trim($network_values['url'] ?? ''),
                    'weight' => (int) ($network_values['weight'] ?? $network->defaultWeight()),
                ];
            }

            $this->configuration['override_settings'] = $override_settings;
        } else {
            $this->configuration['override_settings'] = [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function build() {
        $config = $this->getConfiguration();
        $links = [];

        if ($config['use_global_settings']) {
            $global_config = $this->configFactory->get('social_links.settings');
            $global_config_data = $global_config->get() ?: [];

            $global_links = isset($global_config_data['links']) ? $global_config_data['links'] : $global_config_data;

            foreach ($global_links as $network => $settings) {
                if ($network === '_core') {
                    continue;
                }

                if (!empty($settings['enabled']) && !empty($settings['url'])) {
                    $links[] = [
                        'network' => $network,
                        'url' => $settings['url'],
                        'weight' => $settings['weight'] ?? 0,
                        'label' => $this->getNetworkLabel($network),
                        'icon' => $settings['icon'] ?? $network,
                    ];
                }
            }
        } else {
            $override_settings = $config['override_settings'] ?? [];

            foreach ($override_settings as $network => $settings) {
                if (!empty($settings['enabled']) && !empty($settings['url'])) {
                    $links[] = [
                        'network' => $network,
                        'url' => $settings['url'],
                        'weight' => $settings['weight'] ?? 0,
                        'label' => $this->getNetworkLabel($network),
                        'icon' => $settings['icon'] ?? $network,
                    ];
                }
            }
        }

        usort($links, function($a, $b) {
            return $a['weight'] <=> $b['weight'];
        });

        if (empty($links)) {
            return [];
        }

        return [
            '#theme' => 'social_links_block',
            '#links' => $links,
            '#module_path' => '/' . \Drupal::service('extension.list.module')->getPath('social_links'),
            '#cache' => [
                'tags' => [
                    'config:social_links.settings',
                    'block:' . $this->getPluginId(),
                ],
            ],
        ];
    }

    /**
     * Get network label by machine name.
     */
    private function getNetworkLabel($network) {
        $networks = SocialNetwork::cases();
        foreach ($networks as $social_network) {
            if ($social_network->value === $network) {
                return $social_network->label();
            }
        }
        return ucfirst($network);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheContexts() {
        return ['url.path'];
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheTags() {
        return [
            'config:social_links.settings',
            'block:' . $this->getPluginId(),
        ];
    }
}