@echo off

rem **********************************************************************************
rem Установить кодировку
rem UTF-8
chcp 65001

rem **********************************************************************************
rem Создать ссылку для запуска тестового сайта в Open Server
set pathSite="%HOME%/domains/shasoft-test.ru"
if defined HOME (
rem Удалить
rd %pathSite%
rem Создать
mklink /D %pathSite% "%~dp0test-site"
)

rem **********************************************************************************
rem Выполнить тесты
php %~dp0test-site/index.php
if %errorlevel% neq 0 exit /b %errorlevel%