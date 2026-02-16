# Telenet Helper

Веб-информационная система для обработки заявок на подключение и ремонт в телекоммуникационной компании.

## Технологии
- PHP 8.1+
- MySQL
- OpenServer
- Bootstrap 5
- PDO
- Без фреймворков

## Структура проекта
```
public/
src/
database/
.env.example
```

`DocumentRoot` должен указывать на `public/`.

## Установка в OpenServer
1. Скопируйте папку проекта в `OpenServer/domains/telenet-helper`.
2. Создайте БД `telenet_helper` в phpMyAdmin.
3. Импортируйте файл `database/schema.sql` в созданную БД.
4. Скопируйте `.env.example` в `.env` и укажите параметры подключения к MySQL:
   - `DB_HOST`
   - `DB_PORT`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
5. В OpenServer укажите для домена `DocumentRoot` = `.../telenet-helper/public`.
6. Перезапустите OpenServer и откройте сайт в браузере.

## Тестовые аккаунты
- admin / admin123
- operator / operator123
- tech / tech123
- client / client123

Тестовые email:
- admin@local.test
- operator@local.test
- tech@local.test
- client@local.test
