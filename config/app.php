<?php

return [
    'providers' => [
        Nip\AutoLoader\AutoLoaderServiceProvider::class,
        Nip\Inflector\InflectorServiceProvider::class,
        Nip\Config\ConfigServiceProvider::class,
        Nip\Logger\LoggerServiceProvider::class,
        Nip\Debug\DebugServiceProvider::class,
        Nip\Locale\LocaleServiceProvider::class,
        Nip\Mail\MailServiceProvider::class,
        Nip\Mvc\MvcServiceProvider::class,
        Nip\Dispatcher\DispatcherServiceProvider::class,
        Nip\Staging\StagingServiceProvider::class,
        Nip\Router\RouterServiceProvider::class,
        Nip\Router\RoutesServiceProvider::class,
        Nip\Database\DatabaseServiceProvider::class,
        Nip\I18n\TranslatorServiceProvider::class,
        Nip\FlashData\FlashServiceProvider::class,
        Nip\Filesystem\FilesystemServiceProvider::class,
        Nip\Cache\CacheServiceProvider::class
    ]
];
