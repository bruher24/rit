# rit
## Инструкция
Для развертывания проекта необходим PHP 8+ и Composer
1. Клонировать репозиторий
```bash
git clone https://github.com/bruher24/rit.git
```
2. Установить зависимости
```bash
cd rit
```
```bash
composer install
```
3. Развернуть проект
```bash
composer start
```
>[!NOTE]
>Проект разворачивается на встроенном локальном сервере php по адресу localhost:8080.
>Изменить адрес и порт можно в **composer.json** в поле _"scripts":{"start":}_
