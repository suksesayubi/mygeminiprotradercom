<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
            'name' => 'Basic Plan',
            'description' => 'Perfect for beginners',
            'price' => 29.99,
            'currency' => 'USD',
            'billing_period' => 'monthly',
            'features' => [
                'Gemini RealTime Signal',
                'Basic BOT Trading',
                'Email Support',
                '5 Active Signals'
            ],
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 1,
        ]);

        Plan::create([
            'name' => 'Pro Plan',
            'description' => 'Most popular choice',
            'price' => 59.99,
            'currency' => 'USD',
            'billing_period' => 'monthly',
            'features' => [
                'Gemini RealTime Signal',
                'Gemini Expert Signal',
                'Advanced BOT Trading',
                'Priority Support',
                'Unlimited Signals',
                'Custom Strategies'
            ],
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        Plan::create([
            'name' => 'Enterprise Plan',
            'description' => 'For professional traders',
            'price' => 99.99,
            'currency' => 'USD',
            'billing_period' => 'monthly',
            'features' => [
                'All Pro Features',
                'White-label BOT',
                'API Access',
                'Dedicated Support',
                'Custom Development',
                'Multi-exchange Support'
            ],
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 3,
        ]);
    }
}
