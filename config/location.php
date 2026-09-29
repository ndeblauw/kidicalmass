<?php

return [
    /*
     | Radius (km) within which rides and groups count as "in de buurt".
     | 5 km = your immediate neighbourhood municipalities.
     */
    'nearby_radius_km' => (float) env('LOCATION_NEARBY_RADIUS_KM', 5),

    /*
     | Radius (km) for "in de regio": the Kalender's second distance band
     | (rides beyond it land in "Verder in België") and the Meehelpen
     | nearby-chapters search (VolunteerController).
     */
    'regio_radius_km' => (float) env('LOCATION_REGIO_RADIUS_KM', 30),

    'cookie' => 'kcm_location',
    'cookie_days' => 365,
];
