(function (Drupal, once) {
    'use strict';

    Drupal.behaviors.cosmicWidget = {
        attach: function (context, settings) {
            once('cosmicWidget', '#cosmic-widget', context).forEach(function (widget) {
                const refreshBtn = widget.querySelector('#cosmic-refresh-btn');
                let isLoading = false;

                function loadNewCosmicData() {
                    if (isLoading) return;

                    isLoading = true;
                    const content = widget.querySelector('.cosmic-widget__content');
                    const img = widget.querySelector('#cosmic-image');
                    const title = widget.querySelector('.cosmic-widget__image-title');
                    const date = widget.querySelector('.cosmic-widget__image-date');
                    const desc = widget.querySelector('.cosmic-widget__description .cosmic-widget__text-content');
                    const fact = widget.querySelector('.cosmic-widget__fact p');

                    // Показуємо стан завантаження через opacity
                    if (content) content.style.opacity = '0.5';
                    if (refreshBtn) refreshBtn.disabled = true;

                    fetch('/cosmic-widget/new-image')
                        .then(res => res.json())
                        .then(data => {
                            // Зображення
                            if (data.image_url && img) {
                                const newImg = new Image();
                                newImg.onload = function() {
                                    img.style.transition = 'opacity 0.3s ease';
                                    img.style.opacity = '0';

                                    setTimeout(() => {
                                        img.src = data.image_url;
                                        img.alt = data.image_title || '';
                                        if (title) title.textContent = data.image_title || '';
                                        if (date) date.textContent = formatDate(data.date);
                                        img.style.opacity = '1';
                                    }, 300);
                                };

                                newImg.onerror = function() {
                                    console.error('Failed to load image:', data.image_url);
                                };

                                newImg.src = data.image_url;
                            }

                            // Опис - оновлюємо текстовий слайдер
                            if (data.image_explanation && desc) {
                                updateTextSlider(widget, data.image_explanation);
                            }

                            // Факт
                            if (data.fact && fact) {
                                fact.textContent = data.fact;
                            }

                            // Анімація успішного оновлення
                            widget.classList.add('cosmic-widget--updated');
                            setTimeout(() => widget.classList.remove('cosmic-widget--updated'), 1000);
                        })
                        .catch(err => {
                            console.error('Cosmic Widget Error:', err);
                            showError(widget, 'Failed to load new cosmic data. Please try again.');
                        })
                        .finally(() => {
                            isLoading = false;
                            if (content) content.style.opacity = '1';
                            if (refreshBtn) refreshBtn.disabled = false;
                        });
                }

                // Кнопка оновлення
                if (refreshBtn) {
                    refreshBtn.addEventListener('click', e => {
                        e.preventDefault();
                        loadNewCosmicData();
                    });
                }

                // Автооновлення
                if (settings.cosmicWidget && settings.cosmicWidget.autoRefresh) {
                    setInterval(loadNewCosmicData, 30 * 60 * 1000);
                }

                // Ініціалізація текстового слайдера для початкового контенту
                initializeTextSlider(widget);
            });
        }
    };

    function initializeTextSlider(widget) {
        const textContent = widget.querySelector('.cosmic-widget__text-content');
        const prevBtn = widget.querySelector('#text-prev');
        const nextBtn = widget.querySelector('#text-next');
        const currentSpan = widget.querySelector('#text-current');
        const totalSpan = widget.querySelector('#text-total');

        if (!textContent || !prevBtn || !nextBtn) return;

        // Отримуємо повний текст з data-атрибуту або з поточного контенту
        let fullText = textContent.getAttribute('data-full-text') || textContent.textContent;

        // Якщо текст закінчується на "...", значить він обрізаний, отримуємо оригінал
        if (fullText.endsWith('...')) {
            // Спробуємо отримати повний текст з серверу або використаємо наявний
            fullText = textContent.textContent.replace(/\.\.\.$/, '');
        }

        setupTextSlider(widget, fullText);
    }

    function updateTextSlider(widget, newText) {
        setupTextSlider(widget, newText);
    }

    function setupTextSlider(widget, fullText) {
        const textContent = widget.querySelector('.cosmic-widget__text-content');
        const prevBtn = widget.querySelector('#text-prev');
        const nextBtn = widget.querySelector('#text-next');
        const currentSpan = widget.querySelector('#text-current');
        const totalSpan = widget.querySelector('#text-total');
        const sliderControls = widget.querySelector('.cosmic-widget__slider-controls');

        if (!textContent || !fullText) return;

        // Розділяємо текст на частини по ~300 символів
        const chunkSize = 300;
        const words = fullText.split(' ');
        const chunks = [];
        let currentChunk = '';

        for (let word of words) {
            if ((currentChunk + ' ' + word).length > chunkSize && currentChunk.length > 0) {
                chunks.push(currentChunk.trim());
                currentChunk = word;
            } else {
                currentChunk = currentChunk ? currentChunk + ' ' + word : word;
            }
        }

        if (currentChunk) {
            chunks.push(currentChunk.trim());
        }

        // Якщо тільки один chunk, ховаємо контроли
        if (chunks.length <= 1) {
            if (sliderControls) sliderControls.style.display = 'none';
            textContent.textContent = fullText;
            return;
        }

        // Показуємо контроли
        if (sliderControls) sliderControls.style.display = 'flex';

        let currentIndex = 0;

        function updateDisplay() {
            textContent.textContent = chunks[currentIndex];
            textContent.classList.add('sliding');
            setTimeout(() => textContent.classList.remove('sliding'), 400);

            if (currentSpan) currentSpan.textContent = currentIndex + 1;
            if (totalSpan) totalSpan.textContent = chunks.length;

            if (prevBtn) prevBtn.disabled = currentIndex === 0;
            if (nextBtn) nextBtn.disabled = currentIndex === chunks.length - 1;
        }

        // Події для кнопок
        if (prevBtn) {
            prevBtn.replaceWith(prevBtn.cloneNode(true)); // Видаляємо старі обробники
            const newPrevBtn = widget.querySelector('#text-prev');
            newPrevBtn.addEventListener('click', () => {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateDisplay();
                }
            });
        }

        if (nextBtn) {
            nextBtn.replaceWith(nextBtn.cloneNode(true));
            const newNextBtn = widget.querySelector('#text-next');
            newNextBtn.addEventListener('click', () => {
                if (currentIndex < chunks.length - 1) {
                    currentIndex++;
                    updateDisplay();
                }
            });
        }

        // Початкове відображення
        updateDisplay();
    }

    function showError(widget, message) {
        const existingError = widget.querySelector('.cosmic-widget__error');
        if (existingError) {
            existingError.remove();
        }

        const errorEl = document.createElement('div');
        errorEl.className = 'cosmic-widget__error';
        errorEl.style.cssText = `
            background: rgba(255, 0, 0, 0.1);
            color: #ff6b6b;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 0, 0, 0.2);
        `;
        errorEl.textContent = message;
        widget.prepend(errorEl);

        setTimeout(() => {
            if (errorEl.parentNode) {
                errorEl.remove();
            }
        }, 5000);
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('uk-UA', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
            });
        } catch {
            return dateString;
        }
    }

})(Drupal, once);