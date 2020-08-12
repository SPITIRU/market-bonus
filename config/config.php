<?php

    use ArtemiyKudin\Bonus\Models\MarketBonusSetting;
    use ArtemiyKudin\Bonus\Models\MarketStepsPercent;
    use Beauty\Modules\Common\Models\MarketOrder;

    return [
//        'column_names' => [
//            'profiles_key' => 'profileID',
//            'users_key' => 'userID',
//            'logs_key' => 'userID',
//        ],

        'middleware' => [],

        'models' => [
            'stepsPercent' => MarketStepsPercent::class,
            'bonusSettings' => MarketBonusSetting::class,
            'marketOrders' => MarketOrder::class
        ],

        'resolve' => [
            'stepsPercent' => resolve(MarketStepsPercent::class),
            'bonusSettings' => resolve(MarketBonusSetting::class),
            'marketOrders' => resolve(MarketOrder::class)
        ],

        'permission' => 'permission:your-show-orders',

        'prefix' => 'api/crm',

        'routes' => [
            'url' => [
                'bonus' => 'market/bonus',
            ],
        ],

        'table_names' => [
            'stepsPercent' => 'market_steps_percent',
            'bonusSettings' => 'market_bonus_settings',
            'marketOrders' => 'market_orders'
        ],

        'take_logs' => 15
    ];
