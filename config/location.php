<?php

return [
    /*
     | Radius (km) within which rides and groups count as "in de buurt".
     | 7 km = your adjacent municipalities (Jette -> Schaarbeek ~6 km), or
     | the directly neighbouring towns in Flanders/Wallonia.
     */
    'nearby_radius_km' => (float) env('LOCATION_NEARBY_RADIUS_KM', 7),

    'cookie' => 'kcm_location',
    'cookie_days' => 365,
];
