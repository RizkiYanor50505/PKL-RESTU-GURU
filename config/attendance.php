<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Titik Kantor & Aturan Validasi Presensi
    |--------------------------------------------------------------------------
    | Atur koordinat kantor lewat .env agar validasi Haversine memakai lokasi nyata.
    */

    'timezone' => env('ATTENDANCE_TIMEZONE', 'Asia/Jakarta'),

    'shift_start' => env('ATTENDANCE_SHIFT_START', '08:00'),

    'late_tolerance' => env('ATTENDANCE_LATE_TOLERANCE', '08:15'),

    'checkout_opens_at' => env('ATTENDANCE_CHECKOUT_OPENS_AT', '17:00'),

    'qr_token' => env('ATTENDANCE_QR_TOKEN', 'LOC-PST-99A'),

    'office_latitude' => (float) env('ATTENDANCE_OFFICE_LAT', -6.1753924),

    'office_longitude' => (float) env('ATTENDANCE_OFFICE_LNG', 106.8271528),

    'radius_meters' => (float) env('ATTENDANCE_RADIUS_METERS', 100),

    'max_accuracy_meters' => (float) env('ATTENDANCE_MAX_ACCURACY_METERS', 50),

];
