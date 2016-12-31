<?php

/*
|--------------------------------------------------------------------------
| Subscription Plans Factory
|--------------------------------------------------------------------------
*/

$factory->define(\Quarx\Modules\Hadron\Models\Plan::class, function (Faker\Generator $faker) {
    return [

        'id' => 1,
        'name' => 'cheap hosting',
        'amount' => 9999,
        'interval' => 'monthly',
        'currency' => 'usd',
        'enabled' => true,
        'stripe_name' => 'cheap-package',
        'trial_days' => 30,
        'subscription_name' => 'default',
        'descriptor' => 'dumb is dumb',
        'description' => $faker->paragraph().' '.$faker->paragraph(),
        'updated_at' => $faker->datetime(),
        'created_at' => $faker->datetime(),

    ];
});
