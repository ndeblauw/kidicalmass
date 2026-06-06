<?php

return [
    /*
     | Radius (km) within which rides and groups count as "in de buurt".
     | 5 km = your immediate neighbourhood municipalities.
     */
    'nearby_radius_km' => (float) env('LOCATION_NEARBY_RADIUS_KM', 5),

    /*
     | Radius (km) for the "Ruimere regio" tab on the Kalender filter row.
     */
    'regio_radius_km' => (float) env('LOCATION_REGIO_RADIUS_KM', 30),

    'cookie' => 'kcm_location',
    'cookie_days' => 365,
];
