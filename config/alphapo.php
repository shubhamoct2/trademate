<?php

return [
    'sandbox' => [
        'url' => env('SANDBOX_ALPHAPO_API_URL', null),
        'key' => env('SANDBOX_ALPHAPO_API_KEY', null),
        'secret' => env('SANDBOX_ALPHAPO_SECRET_KEY', null),
        'currencies' => [
            [
                'id' => 1,
                'type' => 'crypto',
                'currency' => 'BTC',
                'minimum_amount' => '0.00010000',
                'deposit_fee_percent' => '0.008000',
                'withdrawal_fee_percent' => '0.000000',
                'precision' => 8
            ],
            [
                'id' => 2,
                'type' => 'crypto',
                'currency' => 'ETH',
                'minimum_amount' => '0.01000000',
                'deposit_fee_percent' => '0.008000',
                'withdrawal_fee_percent' => '0.000000',
                'precision' => 8
            ],
            // [
            //     'id' => 3,
            //     'type' => 'fiat',
            //     'currency' => 'EUR',
            //     'minimum_amount' => '0.00000000',
            //     'deposit_fee_percent' => '0.000000',
            //     'withdrawal_fee_percent' => '0.000000',
            //     'precision' => 8
            // ],
            // [
            //     'id' => 4,
            //     'type' => 'fiat',
            //     'currency' => 'USD',
            //     'minimum_amount' => '0.00000000',
            //     'deposit_fee_percent' => '0.000000',
            //     'withdrawal_fee_percent' => '0.000000',
            //     'precision' => 8
            // ],
            // [
            //     'id' => 5,
            //     'type' => 'crypto',
            //     'currency' => 'USDTE',
            //     'minimum_amount' => '0.01000000',
            //     'deposit_fee_percent' => '0.008000',
            //     'withdrawal_fee_percent' => '0.000000',
            //     'precision' => 8
            // ],
            // [
            //     'id' => 45,
            //     'type' => 'crypto',
            //     'currency' => 'USDTT',
            //     'minimum_amount' => '1.00000000',
            //     'deposit_fee_percent' => '0.008000',
            //     'withdrawal_fee_percent' => '0.000000',
            //     'precision' => 8
            // ]
        ]
    ]
];