<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
use Laminas\Cache\Storage\Adapter\Filesystem;
use Laminas\Session;

return array (
    'db' => array(
        'driver' => 'PDO',
        'dsn' => 'mysql:dbname=przychodnia; host=localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        )
    ),
    
    // Cache configuration.
    'caches' => [
        'KonfiguracjaPamieciCache' => [
            'adapter' => [
                'name'    => Filesystem::class,
                'options' => [
                    // Store cached data in this directory.
                    'cache_dir' => './data/cache',
                    // Store cached data for 1 hour.
                    'ttl' => 60*60*1 ,
                    'namespace' => 'przychodnia',
                ],
            ],
            'plugins' => [
                [
                    'name' => 'serializer',
                    'options' => [                        
                    ],
                ],
            ],
        ],
    ],
    //... 
   ////konfiguracja danych sesji... 
    'session_manager' => [
        'config' => [
            'class' => Session\Config\SessionConfig::class,
            'options' => [
                'name' => 'przychodnia_leszka',
                 ],
             ],
        'storage' => Session\Storage\SessionArrayStorage::class,
        'validators' => [
            Session\Validator\RemoteAddr::class,
            Session\Validator\HttpUserAgent::class,
        ],
    ],
     'session_storage' => [
        'type' => Laminas\Session\Storage\SessionArrayStorage::class,
    ],
    'session_config'  => [
         // Session cookie will expire in 1 hour.
                'cookie_lifetime' => 60*60*1,     
                // Session data will be stored on server maximum for 30 days.
                'gc_maxlifetime'     => 60*60*24*30,     
    ],
    
     'session_containers' => [
      Laminas\Session\Container::class, 
    ],
   ////////////////////////koniec konfiguraja dla Sesji 
);
