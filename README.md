# Реалізовано в якості тестового завдання для [DevBranch](https://devbranch.ua/)

## Вимоги до проєкту
- Git
- PHP 8.3
- MySQL/MariaDB
- Composer
- Apache/Nginx
- Docker & Docker Compose - для локальної розробки

## Модулі які були використані під час розробки
- [Pathauto](https://www.drupal.org/project/pathauto) - Для генерації людино-зрозумілих посилань
- [Token](https://www.drupal.org/project/token) - Для отримання змінних середовища
- [Layout Builder](https://www.drupal.org/docs/8/core/modules/layout-builder) - Для налаштування структури сторінок
- [Colorbox](https://www.drupal.org/docs/extending-drupal/contributed-modules/contributed-module-archive/contrib-modules-for-building-the-site-functionality/media-and-files/advanced-image-management/colorbox) - Для реалізації ефекту натискання на головне зображення новини і його збільшення
- [Views Infinite Scroll](https://www.drupal.org/project/views_infinite_scroll) - Для реалізації кнопки "Показати ще" - підвантаження новин на головній сторінці (правий блок)
- [Better Exposed Filters](https://www.drupal.org/project/better_exposed_filters) - Для реалізації сторінки "Пошуку новин" за декількома параметрами (Хоча, можливо варто було б викорситати пошук на основі індексів, типу ElasticSearch)

## Кастомні модулі
- **Ukraine Air Alerts 🇺🇦** - Інтерактивна мапа повітряних тривог України, яка почаказує часткову, повну тривогу по областям і оновлюється кожні 15 секунд (Головна сторінка)
- **Cosmic Widget 🪐** - Віджет який відображає випадкову картинку з сайту NASA і текстовий факт про неї (Головна сторінка). За допомогою кнопки "Get New Image" можна оновити картинку 
- **Social Links 📱** - Віджет який відображає іконки соціальних мереж і посилання на них (Хедер) 

## Кастомні теми
- **VSN Theme** - За основу/клона було взято сайт [Волинської Служби Новин](https://vsn.ua/)

## Результат
- [https://andrii-project.org.ua/](https://andrii-project.org.ua)

## Структура проєкту
```angular2html
drupal-vsn/
├── config/                 # Збережена конфігурація Drupal
├── docker/
│   └── drupal/
│       └── Dockerfile      # Конфігурація Drupal в Docker
├── scripts/                # Скрипти для збереження і вивандаження конфігурації Drupal
├── web/                    # Публічна директорія Docker - volumes
│   ├── custom/
│   │   ├── modules/        # Кастомні модулі Drupal
│   │   └── themes/         # Кастомні теми Drupal
│   ├── libraries/          # Бібліотеки, в нашому випадку бібліотека для роботи модуля Colorbox
│   └── sites/              # Налаштування Drupal (підключення до бази)
│       ├── default/        # Налаштування Drupal
│       └── files/          # Медіа файли для сайту Drupal, щоб сайт не був порожній (Теж не дуже так можна робити, але... 🤫)
├── DB_DUMP/                # Dump бази (Таке не можна зберігати 🤫)
├── vendor/                 # Composer залежності .gitignore
├── .gitignore
├── docker-compose.yml      # Файл конфігурації Docker Compose
└── README.md
```

## Розгортання проєкту
1. Клонування репозиторію
```angular2html
git clone [repository_url]

cd drupal-vsn
```
2. Запуск контейнерів
```angular2html
docker-compose up -d --build
```
3. Дочекайтеся завершення збірки і перевірте статус:
```angular2html
docker-compose ps
```
4. Відновлення дампу бази даних за допомогою команди, або через phpmyadmin за допомогою файлу DB_DUMP/drupal.sql:
```angular2html
docker cp DB_DUMP/drupal.sql $(docker-compose ps -q database):/tmp/drupal.sql

docker-compose exec database mysql -u drupaluser -pdrupalpassword -e "DROP DATABASE IF EXISTS drupal; CREATE DATABASE drupal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

docker-compose exec database mysql -u drupaluser -pdrupalpassword drupal -e "source /tmp/drupal.sql"
```
5. Імпорт конфігурації
```angular2html
chmod +x scripts/import-config.sh

scripts/import-config.sh
```
6. Очистіть кеш в контейнері drupal
```angular2html
docker-compose exec drupal drush cache:rebuild
```
7. Доступ до сайту
- Сайт: http://localhost:8080
- Адмінка: http://localhost:8080/user/login
- phpMyAdmin: http://localhost:8081

### Корисні команди
- Очищення кешу Drupal
```angular2html
docker-compose exec drupal drush cache:rebuild
```
- Перезапуск контейнерів
```angular2html
docker-compose restart
```
- Переглянвірка логів
```angular2html
docker-compose logs drupal
```
- Доступ до контейнера Drupal
```angular2html
docker-compose exec drupal bash
```
- Зупинка проєкту
```angular2html
docker-compose down
```