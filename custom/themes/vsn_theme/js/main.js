(function (Drupal, once) {
    'use strict';

    // Функція для додавання роздільників дат між новинами
    function addDateSeparators() {
        const newsContainer = document.querySelector('.main-page-news-list');
        if (!newsContainer) {
            console.log('Блок з новинами не знайдено');
            return;
        }

        // Спочатку видаляємо всі існуючі роздільники дат (щоб уникнути дублювання)
        const existingDateSeparators = newsContainer.querySelectorAll('.informer-date');
        existingDateSeparators.forEach(separator => separator.remove());

        // Отримуємо всі блоки новин
        const newsItems = newsContainer.querySelectorAll('.inline-news-teaser');
        if (newsItems.length === 0) {
            console.log('Новини не знайдено');
            return;
        }

        // Масив назв місяців українською
        const monthNames = [
            'січня', 'лютого', 'березня', 'квітня', 'травня', 'червня',
            'липня', 'серпня', 'вересня', 'жовтня', 'листопада', 'грудня'
        ];

        // Функція для форматування дати
        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = date.getDate();
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear();
            return `${day} ${month} ${year}`;
        }

        // Функція для отримання дати без часу (для порівняння)
        function getDateOnly(dateString) {
            const date = new Date(dateString);
            return new Date(date.getFullYear(), date.getMonth(), date.getDate());
        }

        let previousDate = null;

        // Проходимося по всіх новинах
        newsItems.forEach((newsItem, index) => {
            const timeElement = newsItem.querySelector('time[datetime]');
            if (!timeElement) {
                console.log(`Елемент time не знайдено в новині ${index + 1}`);
                return;
            }

            const currentDatetime = timeElement.getAttribute('datetime');
            const currentDate = getDateOnly(currentDatetime);
            const currentDateString = currentDate.toISOString();

            // Якщо це перша новина, просто запам'ятовуємо її дату (без додавання роздільника)
            if (index === 0) {
                previousDate = currentDateString;
                return;
            }

            // Порівнюємо дати - якщо дата змінилася, додаємо роздільник перед поточною новиною
            if (previousDate && currentDateString !== previousDate) {
                const dateDiv = document.createElement('div');
                dateDiv.className = 'informer-date';
                dateDiv.textContent = formatDate(currentDatetime);
                newsItem.parentNode.insertBefore(dateDiv, newsItem);
            }

            previousDate = currentDateString;
        });
    }

    // Мобільне меню
    document.addEventListener('DOMContentLoaded', function() {
        addDateSeparators();

        // Створюємо кнопку гамбургер-меню
        const headerContent = document.querySelector('.header-content');
        const mobileToggle = document.createElement('button');
        mobileToggle.className = 'mobile-menu-toggle';
        mobileToggle.innerHTML = '<span></span><span></span><span></span>';
        mobileToggle.setAttribute('aria-label', 'Відкрити меню');

        // Додаємо кнопку до хедера
        headerContent.appendChild(mobileToggle);

        // Отримуємо елементи меню
        const mainNavigation = document.querySelector('.header__main-menu.main-navigation');
        const userTools = document.querySelector('.header__user-tools');

        // Обробник кліку на кнопку меню
        mobileToggle.addEventListener('click', function() {
            const isActive = this.classList.contains('active');

            if (isActive) {
                // Закриваємо меню
                this.classList.remove('active');
                if (mainNavigation) mainNavigation.classList.remove('active');
                if (userTools) userTools.classList.remove('active');
                this.setAttribute('aria-label', 'Відкрити меню');
                document.body.style.overflow = '';
            } else {
                // Відкриваємо меню
                this.classList.add('active');
                if (mainNavigation) mainNavigation.classList.add('active');
                if (userTools) userTools.classList.add('active');
                this.setAttribute('aria-label', 'Закрити меню');
                document.body.style.overflow = 'hidden';
            }
        });

        // Закриваємо меню при кліку на посилання
        const menuLinks = document.querySelectorAll('.main-navigation a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileToggle.classList.remove('active');
                if (mainNavigation) mainNavigation.classList.remove('active');
                if (userTools) userTools.classList.remove('active');
                mobileToggle.setAttribute('aria-label', 'Відкрити меню');
                document.body.style.overflow = '';
            });
        });

        // Закриваємо меню при зміні розміру екрана
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                mobileToggle.classList.remove('active');
                if (mainNavigation) mainNavigation.classList.remove('active');
                if (userTools) userTools.classList.remove('active');
                mobileToggle.setAttribute('aria-label', 'Відкрити меню');
                document.body.style.overflow = '';
            }
        });

        // Закриваємо меню при кліку поза ним
        document.addEventListener('click', function(event) {
            const isClickInsideMenu = event.target.closest('.header__main-menu') ||
                event.target.closest('.header__user-tools') ||
                event.target.closest('.mobile-menu-toggle');

            if (!isClickInsideMenu && mobileToggle.classList.contains('active')) {
                mobileToggle.classList.remove('active');
                if (mainNavigation) mainNavigation.classList.remove('active');
                if (userTools) userTools.classList.remove('active');
                mobileToggle.setAttribute('aria-label', 'Відкрити меню');
                document.body.style.overflow = '';
            }
        });
    });

    // Якщо DOM вже завантажений, запускаємо одразу
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addDateSeparators);
    } else {
        addDateSeparators();
    }

})(Drupal, once);