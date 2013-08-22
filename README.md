# bubujka/vkdr

Враппер для api vk.com

## Установка

Из консоли:
```bash
$ composer require bubujka/vkdr=dev-master
```

Или в файле composer.json:
```json
"require": {
    "bubujka/vkdr": "dev-master"
}
```


## Настройка

Где-то в коде приложения:
```php
<?php
vkdr\app_secret('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
vkdr\app_id(0000000000);

vkdr\app_host('http://dev.domain.tld');
vkdr\app_oauth_url('vk_oauth_code.php'); 
vkdr\app_scope(array('ads', 'friends')); 

vkdr\token('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
```

Для авторизации в vk есть пара вспомогательных методов

Один для генерации ссылки на страницу авторизации:
```php
<?php
echo vkdr\oauth_url()."\n";
// https://oauth.vk.com/authorize?client_id=0000000&scope=ads...
```

Другой для получения `user_id` и `access_token`:
```php
<?php
echo vkdr\get_token_obj('xxxxxxxxxxxxxxxxxxxxxx');
/*
Array
(
    [access_token] => xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    [expires_in] => 85690
    [user_id] => xxxxxxxxx
)
*/
```

## Использование

Для каждого метода из vk.api есть своя функция
```txt
users.get -> vkdr\users_get();
users.search -> vkdr\users_search();
users.isAppUser -> vkdr\users_isAppUser();
```

Если нравятся нэймспэйсы - так и вызываем:
```php
<?php
$r = vkdr\photos_getAlbums();
```

Если не нравятся - пересоздаём функции в глобальном нэймспэйсе и вызываем напрямую:
```php
<?php
vkdr\create_wrappers(true); # вызвать однократно
$r = photos_getAlbums();
```
