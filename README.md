# Task 1

Реализовано бэкэнд-приложение на PHP (в виде Rest API сервиса), которое принимает данные из веб-формы и сохраняет их в таблицу БД.

Сама веб-форма, состоит из 3 полей: email*, телефон*, сообщение - именно их обрабатывает бэкэнд. (* помечены обязательные поля)

Были использованы пакеты composer
- [x] PHP 7.4
- [x] Composer
- [x] Docker
- [x] PHP cтандарты PSR-4, PSR-12

# Установка
## Команды для разворота окружения
```shell
docker-compose build
docker-compose up -d
docker-compose exec php_apache composer install
```
## Входные данные
Данные берутся от формы с методом POST `<form method="POST">`
[форма для проверки](http://localhost/test)

## Выходные данные
Вывод всех данных в формате [JSON](http://localhost)
[Вывод в таблицу](http://localhost/test)

## Настройки
Файл настройки БД `config/database.php`
Файл настройки роутинга `config/routes.yml`

## Примеры
**Payload:**

```
email=test%40mail.ru&phone=87777717771&message=message
```
**Response:**
```json
{"status":"success"}
```

**Payload:**
```
email=test%40mail.ru&phone=87777717771&message=
```
**Response:**
```json
{"status":"success"}
```

**Payload:**
```
email=test%40mail&phone=87777717771&message=message
```
**Response:**
```json
{"status":"error","messages":[{"property":"[email]","value":"test@mail","message":"This value is not a valid email address."}]}
```
**Payload:**
```
email=test%40mail.ru&phone=877777177&message=message
```
**Response:**
```json
{"status":"error","messages":[{"property":"[phone]","value":"877777177","message":"This value is not valid."}]}
```
**Payload:**
```
email=test%40mail&phone=8777771777&message=message
```
**Response:**
```json
{"status":"error","messages":[{"property":"[email]","value":"test@mail","message":"This value is not a valid email address."},{"property":"[phone]","value":"8777771777","message":"This value is not valid."}]}
```