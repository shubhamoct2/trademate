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
                'minimum_withdraw_amount' => '0.00020000',
                'deposit_fee_percent' => '0.008000',
                'withdrawal_fee_percent' => '0.000000',
                'precision' => 8
            ],
            [
                'id' => 2,
                'type' => 'crypto',
                'currency' => 'ETH',
                'minimum_amount' => '0.01000000',
                'minimum_withdraw_amount' => '0.02000000',
                'deposit_fee_percent' => '0.008000',
                'withdrawal_fee_percent' => '0.000000',
                'precision' => 8
            ],
            [
                'id' => 5,
                'type' => 'crypto',
                'currency' => 'USDT',
                'minimum_amount' => '0.01000000',
                'minimum_withdraw_amount' => '0.02000000',
                'deposit_fee_percent' => '0.008000',
                'withdrawal_fee_percent' => '0.000000',
                'precision' => 8
            ],
        ],
        'withdrawal' => [
            'currencies' => [
                'btc' => 'BTC',
                'eth' => 'ETH',
                'usdt' => 'USDT',
            ],
            'blockchain' => [
                'erc20' => 'ERC-20',
                'trc20' => 'TRC-20',
            ]      
        ]
    ],
    'prod' => [
        'url' => env('SANDBOX_ALPHAPO_API_URL', null),
        'key' => env('SANDBOX_ALPHAPO_API_KEY', null),
        'secret' => env('SANDBOX_ALPHAPO_SECRET_KEY', null),
        'currencies' => [
            [
                'id' => 1,
                'type' => 'crypto',
                'currency' => 'BTC',
                'minimum_amount' => '0.00010000',
                'minimum_withdraw_amount' => '0.00020000',
                'deposit_fee_percent' => '0.008000',
                'withdrawal_fee_percent' => '0.000000',
                'precision' => 8
            ],
            [
                'id' => 2,
                'type' => 'crypto',
                'currency' => 'ETH',
                'minimum_amount' => '0.01000000',
                'minimum_withdraw_amount' => '0.02000000',
                'deposit_fee_percent' => '0.008000',
                'withdrawal_fee_percent' => '0.000000',
                'precision' => 8
            ],
            [
                'id' => 5,
                'type' => 'crypto',
                'currency' => 'USDT',
                'minimum_amount' => '0.01000000',
                'minimum_withdraw_amount' => '0.02000000',
                'deposit_fee_percent' => '0.008000',
                'withdrawal_fee_percent' => '0.000000',
                'precision' => 8
            ],
        ],
        'withdrawal' => [
            'currencies' => [
                'btc' => 'BTC',
                'eth' => 'ETH',
                'usdt' => 'USDT',
            ],
            'blockchain' => [
                'erc20' => 'ERC-20',
                'trc20' => 'TRC-20',
            ]      
        ]
    ]
];