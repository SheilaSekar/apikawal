<?php

return [
    'emergency_banner' => 'Kebakaran sedang berlangsung dan mengancam jiwa? Hubungi 112 atau BPBD/pemadam kebakaran setempat. APIKAWAL adalah alat pelaporan dan koordinasi.',
    'duplicate' => ['radius_km' => 0.3, 'window_hours' => 6, 'closed_statuses' => ['ditolak', 'selesai', 'padam_sendiri', 'duplikat']],
    'verification' => ['confirm_threshold' => 2, 'weights' => ['warga' => 1, 'relawan' => 2, 'petugas' => 3, 'admin' => 3]],
    'priority' => [
        'weights' => ['w_verification' => 30, 'w_landtype' => 20, 'w_hotspot' => 20, 'w_poi' => 15, 'w_support' => 10, 'w_photo' => 5],
        'land_factors' => ['gambut' => 1.0, 'hutan' => 0.8, 'semak' => 0.7, 'kebun' => 0.6, 'lainnya' => 0.3],
        'unverified_cap' => 59,
    ],
    // Bounding box perkiraan untuk demo; bukan batas administratif dan dapat diganti tim.
    'demo_bounds' => ['south' => -3.6, 'north' => -3.0, 'west' => 105.0, 'east' => 105.8, 'grid_degrees' => 0.05],
    'analytics' => ['hotspot_months' => 12, 'hotspot_weight' => 0.7, 'report_weight' => 0.3],
    'team' => [
        'Sheila Sekar Asri Puteri',
        'Myla Melida',
        'Kiki Hardiyanti',
    ],
    'logo' => '/images/logo-placeholder.svg',
];
