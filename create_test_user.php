<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Creating test user...\n";

try {
    // Check if user already exists
    $existingUser = User::where('email', 'test@example.com')->first();
    
    if ($existingUser) {
        echo "✅ Test user already exists: test@example.com\n";
        echo "Password: password\n";
    } else {
        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        echo "✅ Test user created successfully!\n";
        echo "Email: test@example.com\n";
        echo "Password: password\n";
        echo "User ID: " . $user->id . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error creating user: " . $e->getMessage() . "\n";
}