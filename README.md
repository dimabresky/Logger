# Logger
Класс логирования различной информации

## Пример использования
```php
<?php

/**
* @var $path2log абсолютный путь к файлу логов (по-умолчанию файл logger_log.txt в корне сайта)
*/
$logger = new \travelsoft\Logger($path2log);
$logger->write("Hello, world!!!");
```
