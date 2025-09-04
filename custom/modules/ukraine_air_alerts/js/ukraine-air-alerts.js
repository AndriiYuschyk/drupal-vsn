/**
 * @file
 * Ukraine Air Alerts map functionality.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    /**
     * Ukraine Air Alerts Map behavior.
     */
    Drupal.behaviors.ukraineAirAlertsMap = {
        attach: function (context, settings) {
            if (!settings.ukraineAirAlerts) {
                return;
            }

            const $wrapper = $('.ukraine-air-alerts-wrapper', context);
            if ($wrapper.length === 0) {
                return;
            }

            // Initialize the map
            const alertsMap = new UkraineAlertsMap($wrapper, settings.ukraineAirAlerts);
            alertsMap.init();
        }
    };

    /**
     * Ukraine Alerts Map class.
     */
    function UkraineAlertsMap($wrapper, settings) {
        this.$wrapper = $wrapper;
        this.settings = settings;
        this.refreshInterval = null;
        this.tooltip = null;

        // SVG paths for Ukrainian regions (simplified)
        this.regionPaths = {
            'Вінницька область': 'M350,300 L380,290 L390,320 L370,340 L340,330 Z',
            'Волинська область': 'M200,150 L230,140 L240,170 L220,190 L190,180 Z',
            'Дніпропетровська область': 'M450,280 L480,270 L490,300 L470,320 L440,310 Z',
            'Донецька область': 'M520,260 L550,250 L560,280 L540,300 L510,290 Z',
            'Житомирська область': 'M280,200 L310,190 L320,220 L300,240 L270,230 Z',
            'Закарпатська область': 'M150,250 L180,240 L190,270 L170,290 L140,280 Z',
            'Запорізька область': 'M480,320 L510,310 L520,340 L500,360 L470,350 Z',
            'Івано-Франківська область': 'M220,280 L250,270 L260,300 L240,320 L210,310 Z',
            'Київська область': 'M350,220 L380,210 L390,240 L370,260 L340,250 Z',
            'м. Київ': 'M365,235 L375,230 L380,240 L370,245 L360,240 Z',
            'Кіровоградська область': 'M400,300 L430,290 L440,320 L420,340 L390,330 Z',
            'Луганська область': 'M550,240 L580,230 L590,260 L570,280 L540,270 Z',
            'Львівська область': 'M180,220 L210,210 L220,240 L200,260 L170,250 Z',
            'Миколаївська область': 'M380,360 L410,350 L420,380 L400,400 L370,390 Z',
            'Одеська область': 'M320,380 L350,370 L360,400 L340,420 L310,410 Z',
            'Полтавська область': 'M420,240 L450,230 L460,260 L440,280 L410,270 Z',
            'Рівненська область': 'M240,180 L270,170 L280,200 L260,220 L230,210 Z',
            'Сумська область': 'M400,180 L430,170 L440,200 L420,220 L390,210 Z',
            'Тернопільська область': 'M260,240 L290,230 L300,260 L280,280 L250,270 Z',
            'Харківська область': 'M480,200 L510,190 L520,220 L500,240 L470,230 Z',
            'Херсонська область': 'M420,380 L450,370 L460,400 L440,420 L410,410 Z',
            'Хмельницька область': 'M300,240 L330,230 L340,260 L320,280 L290,270 Z',
            'Черкаська область': 'M380,260 L410,250 L420,280 L400,300 L370,290 Z',
            'Чернівецька область': 'M280,320 L310,310 L320,340 L300,360 L270,350 Z',
            'Чернігівська область': 'M380,160 L410,150 L420,180 L400,200 L370,190 Z'
        };
    }

    UkraineAlertsMap.prototype = {

        /**
         * Initialize the map.
         */
        init: function() {
            this.createSVGMap();
            this.bindEvents();
            this.createTooltip();
            this.setupAutoRefresh();
            this.updateDisplay();
        },

        /**
         * Create SVG map with regions.
         */
        createSVGMap: function() {
            const $svg = this.$wrapper.find('.ukraine-map-svg');
            $svg.empty();

            // Add regions to SVG
            Object.entries(this.settings.alerts).forEach(([regionName, alertData]) => {
                if (this.regionPaths[regionName]) {
                    const $path = $(`
            <path 
              class="region ${alertData.status_class}" 
              data-region="${regionName}"
              data-status="${alertData.status_text}"
              data-changed="${alertData.changed || ''}"
              d="${this.regionPaths[regionName]}"
              title="${regionName}: ${alertData.status_text}">
            </path>
          `);
                    $svg.append($path);
                }
            });
        },

        /**
         * Bind event handlers.
         */
        bindEvents: function() {
            const self = this;

            // Refresh button
            this.$wrapper.find('#refresh-alerts').on('click', function() {
                self.refreshData();
            });

            // Region hover events
            this.$wrapper.on('mouseenter', '.region', function(e) {
                self.showTooltip(e, $(this));
            });

            this.$wrapper.on('mouseleave', '.region', function() {
                self.hideTooltip();
            });

            this.$wrapper.on('mousemove', '.region', function(e) {
                self.moveTooltip(e);
            });

            // Region click events
            this.$wrapper.on('click', '.region', function() {
                const regionName = $(this).data('region');
                self.highlightRegionInList(regionName);
            });
        },

        /**
         * Create tooltip element.
         */
        createTooltip: function() {
            this.tooltip = $('<div class="ukraine-tooltip"></div>');
            $('body').append(this.tooltip);
        },

        /**
         * Show tooltip for region.
         */
        showTooltip: function(e, $region) {
            const regionName = $region.data('region');
            const status = $region.data('status');
            const changed = $region.data('changed');

            let content = `<strong>${regionName}</strong><br>${status}`;
            if (changed) {
                content += `<br><small>Оновлено: ${this.formatDate(changed)}</small>`;
            }

            this.tooltip.html(content).addClass('show');
            this.moveTooltip(e);
        },

        /**
         * Hide tooltip.
         */
        hideTooltip: function() {
            this.tooltip.removeClass('show');
        },

        /**
         * Move tooltip to follow cursor.
         */
        moveTooltip: function(e) {
            this.tooltip.css({
                left: e.pageX + 15 + 'px',
                top: e.pageY - 10 + 'px'
            });
        },

        /**
         * Highlight region in the list.
         */
        highlightRegionInList: function(regionName) {
            this.$wrapper.find('.alert-item').removeClass('highlighted');
            this.$wrapper.find(`.alert-item[data-region="${regionName}"]`).addClass('highlighted');

            // Scroll to the item
            const $item = this.$wrapper.find(`.alert-item[data-region="${regionName}"]`);
            if ($item.length) {
                $item[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        },

        /**
         * Setup auto-refresh functionality.
         */
        setupAutoRefresh: function() {
            if (this.settings.refreshInterval > 0) {
                const self = this;
                this.refreshInterval = setInterval(function() {
                    self.refreshData();
                }, this.settings.refreshInterval);
            }
        },

        /**
         * Refresh alerts data from API.
         */
        refreshData: function() {
            const self = this;
            const $button = this.$wrapper.find('#refresh-alerts');
            const $status = this.$wrapper.find('#refresh-status');

            $button.prop('disabled', true);
            $status.text('Оновлення...').removeClass().addClass('refresh-status loading');

            $.ajax({
                url: this.settings.apiEndpoint,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        self.settings.alerts = response.data;
                        self.updateDisplay();
                        $status.text('Оновлено: ' + self.formatDate(new Date())).removeClass().addClass('refresh-status success');
                    } else {
                        $status.text('Помилка оновлення').removeClass().addClass('refresh-status error');
                    }
                },
                error: function() {
                    $status.text('Помилка з\'єднання').removeClass().addClass('refresh-status error');
                },
                complete: function() {
                    $button.prop('disabled', false);
                    setTimeout(function() {
                        $status.text('');
                    }, 5000);
                }
            });
        },

        /**
         * Update display with new data.
         */
        updateDisplay: function() {
            this.createSVGMap();
            this.updateAlertsList();
            this.updateCounter();
            this.updateTimestamp();
        },

        /**
         * Update alerts list.
         */
        updateAlertsList: function() {
            const $list = this.$wrapper.find('.alerts-list');
            $list.empty();

            Object.entries(this.settings.alerts).forEach(([regionName, alertData]) => {
                const changedHtml = alertData.changed ?
                    `<div class="alert-changed">${this.formatDate(alertData.changed)}</div>` : '';

                const $item = $(`
          <div class="alert-item ${alertData.status_class}" data-region="${regionName}">
            <div class="alert-region">${regionName}</div>
            <div class="alert-status">${alertData.status_text}</div>
            ${changedHtml}
          </div>
        `);
                $list.append($item);
            });
        },

        /**
         * Update active alerts counter.
         */
        updateCounter: function() {
            const activeCount = Object.values(this.settings.alerts).filter(alert => alert.alert).length;
            this.$wrapper.find('#active-count').text(activeCount);
        },

        /**
         * Update timestamp.
         */
        updateTimestamp: function() {
            this.$wrapper.find('.timestamp').text(this.formatDate(new Date()));
        },

        /**
         * Format date for display.
         */
        formatDate: function(date) {
            if (typeof date === 'string') {
                date = new Date(date);
            }
            return date.toLocaleString('uk-UA', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        /**
         * Cleanup when destroyed.
         */
        destroy: function() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
            if (this.tooltip) {
                this.tooltip.remove();
            }
        }
    };

    // Add CSS for highlighted items
    $('<style>')
        .text(`
      .alert-item.highlighted {
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        background: #fff3cd !important;
        border-left-color: #ffc107 !important;
      }
    `)
        .appendTo('head');

})(jQuery, Drupal, drupalSettings);