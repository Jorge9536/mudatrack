<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Ruta al archivo JSON de la cuenta de servicio descargado desde
    | Firebase Console → Configuración del proyecto → Cuentas de servicio.
    |
    */
    'credentials' => [
    'file' => str_replace('\\', '/', storage_path('app/firebase/service-account.json')),
    'auto_discovery' => false,
],

    /*
    |--------------------------------------------------------------------------
    | Project ID
    |--------------------------------------------------------------------------
    */
    'project_id' => env('FIREBASE_PROJECT_ID', 'gps1-e12e5'),

    /*
    |--------------------------------------------------------------------------
    | Firestore Database URL
    |--------------------------------------------------------------------------
    | Nombre de la base de datos de Firestore. Por defecto es "(default)".
    |
    */
    'database_url' => env(
        'FIREBASE_DATABASE_URL',
        'https://gps1-e12e5.firebaseio.com'
    ),

    /*
    |--------------------------------------------------------------------------
    | Firestore Database Name
    |--------------------------------------------------------------------------
    */
    'firestore_database' => '(default)',

    /*
    |--------------------------------------------------------------------------
    | Default Storage Bucket
    |--------------------------------------------------------------------------
    */
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', 'gps1-e12e5.firebasestorage.app'),

    /*
    |--------------------------------------------------------------------------
    | Configuración REST (para lo que ya usas con Guzzle)
    |--------------------------------------------------------------------------
    */
    'firestore_base_url' => env(
        'FIREBASE_FIRESTORE_BASE_URL',
        'https://firestore.googleapis.com/v1/projects/gps1-e12e5/databases/(default)/documents'
    ),

    'api_key' => env('FIREBASE_API_KEY'),
];