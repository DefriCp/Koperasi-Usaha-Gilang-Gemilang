<?php

return [

    // === NAMA RESMI YANG HARUS ADA DI DB ===
    'official' => [
        'KB BANK',
        'KB BANK SYARIAH',
        'BANK MNC',
        'BPR KS',
        'BPR VIMA',
        'BPR INDOMITRA',
        'BPR HOSING',
        'BPR HASAMITRA',
        'BPR PERDANA',
        'BPR RIFI',
        'BPR NBP29',

        // Channeling / Execut / Sub Channeling (dari gambar)
        'CHANNELING BANK BUKOPIN / KB BANK',
        'CHANNELING BANK MNC',
        'CHANNELING BPR ADHIERRESA / VIMA',
        'CHANNELING BPR DHAHA',
        'CHANNELING BPR HASAMITRA',
        'CHANNELING BPR HOSING',
        'CHANNELING BPR INDOMITRA',
        'CHANNELING BPR NBP29',
        'CHANNELING BUKOPIN SYARIAH',
        'CHANNELING KOP SAM',
        'CHANNELING KSP SMS',
        'CHANNELING SSB BPR RIFI',
        'EXECUT EKS PLAT',
        'EXECUT EKS PLAT SSB',
        'EXECUT PLAT SSB',
        'EXECUT PLATINUM',
        'SUB CHANNELING GRAHADI',
        'SUB CHANNELING KOPJAS',
        'SUB CHANNELING KOSPPI BANK BANTEN',
        'SUB CHANNELING SSB BPR PERDANA',
    ],

    // === ALIAS → OFFICIAL ===
    // Kunci dicocokkan menggunakan "contains" (case-insensitive).
    // Semakin spesifik di atas, semakin baik.
    'alias_map' => [
        // Bank inti
        'PLATINUM KB BANK'      => 'KB BANK',
        'KJSB'                  => 'KB BANK',
        'KB BANK'               => 'KB BANK',
        'KB SYARIAH'            => 'KB BANK SYARIAH',
        'BANK MNC'              => 'BANK MNC',
        'BPR KS'                => 'BPR KS',
        'HASAMITRA'             => 'BPR HASAMITRA',
        'VIMA'                  => 'BPR VIMA',
        'INDOMITRA'             => 'BPR INDOMITRA',
        'HOSING'                => 'BPR HOSING',
        'RIFI'                  => 'BPR RIFI',
        'PERDANA'               => 'BPR PERDANA',
        'NBP29'                 => 'BPR NBP29',

        // Channeling / dll (gunakan cocok-mengandung)
        'CHANNELING BANK BUKOPIN'      => 'CHANNELING BANK BUKOPIN / KB BANK',
        'CHANELLING BANK BUKOPIN'      => 'CHANNELING BANK BUKOPIN / KB BANK',
        'CHANNELING BANK MNC'          => 'CHANNELING BANK MNC',
        'CHANNELING BPR ADHIERRESA'    => 'CHANNELING BPR ADHIERRESA / VIMA',
        'CHANNELING BPR DHAHA'         => 'CHANNELING BPR DHAHA',
        'CHANNELING BPR HASAMITRA'     => 'CHANNELING BPR HASAMITRA',
        'CHANNELING BPR HOSING'        => 'CHANNELING BPR HOSING',
        'CHANNELING BPR INDOMITRA'     => 'CHANNELING BPR INDOMITRA',
        'CHANNELING BPR NBP29'         => 'CHANNELING BPR NBP29',
        'CHANNELING BUKOPIN SYARIAH'   => 'CHANNELING BUKOPIN SYARIAH',
        'CHANNELING KOP SAM'           => 'CHANNELING KOP SAM',
        'CHANNELING KSP SMS'           => 'CHANNELING KSP SMS',
        'CHANNELING SSB BPR RIFI'      => 'CHANNELING SSB BPR RIFI',

        'EXECUT EKS PLAT'              => 'EXECUT EKS PLAT',
        'EXECUT EKS PLAT SSB'          => 'EXECUT EKS PLAT SSB',
        'EXECUT PLAT SSB'              => 'EXECUT PLAT SSB',
        'EXECUT PLATINUM'              => 'EXECUT PLATINUM',

        'SUB CHANNELING GRAHADI'               => 'SUB CHANNELING GRAHADI',
        'SUB CHANNELING KOPJAS'                => 'SUB CHANNELING KOPJAS',
        'SUB CHANNELING KOSPPI BANK BANTEN'    => 'SUB CHANNELING KOSPPI BANK BANTEN',
        'SUB CHANNELING SSB BPR PERDANA'       => 'SUB CHANNELING SSB BPR PERDANA',
    ],
];
