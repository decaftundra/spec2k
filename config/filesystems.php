<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
        ],
        
        // Name this disk for your application to reference.
        'azure-file-storage' => [
            // The driver provided by this package.
            'driver' => 'azure-file-storage',

            // Account credentials.
            'storageAccount' => env('AZURE_FILE_STORAGE_ACCOUNT'),
            'storageAccessKey' => env('AZURE_FILE_STORAGE_ACCESS_KEY'),

            // The file share.
            // This driver supports one file share at a time (you cannot
            // copy or move files between shares natively).
            'fileShareName' => env('AZURE_FILE_STORAGE_SHARE_NAME'),

            // Optional settings
            'disableRecursiveDelete' => false,
            'driverOptions' => [],
        ],
        
        'azure-blob-storage-archive' => [
            'driver'    => 'azure',
            'name'      => env('AZURE_BLOB_STORAGE_NAME'),
            'key'       => env('AZURE_BLOB_STORAGE_KEY'),
            'container' => env('AZURE_BLOB_STORAGE_CONTAINER_ARCHIVE'),
        ],
        
        'azure-blob-storage-latest' => [
            'driver'    => 'azure',
            'name'      => env('AZURE_BLOB_STORAGE_NAME'),
            'key'       => env('AZURE_BLOB_STORAGE_KEY'),
            'container' => env('AZURE_BLOB_STORAGE_CONTAINER_LATEST'),
        ],
        
        'singapore-azure-blob-storage-archive' => [
            'driver'    => 'azure',
            'name'      => env('AZURE_BLOB_STORAGE_NAME'),
            'key'       => env('AZURE_BLOB_STORAGE_KEY'),
            'container' => env('SINGAPORE_AZURE_BLOB_STORAGE_CONTAINER_ARCHIVE'),
        ],
        
        'singapore-azure-blob-storage-latest' => [
            'driver'    => 'azure',
            'name'      => env('AZURE_BLOB_STORAGE_NAME'),
            'key'       => env('AZURE_BLOB_STORAGE_KEY'),
            'container' => env('SINGAPORE_AZURE_BLOB_STORAGE_CONTAINER_LATEST'),
        ],

    ],
    
    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
