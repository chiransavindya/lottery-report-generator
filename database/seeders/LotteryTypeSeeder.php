<?php

namespace Database\Seeders;

use App\Models\LotteryType;
use Illuminate\Database\Seeder;

class LotteryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lotteryTypes = [
            [
                'name' => 'Ada Kotipathi',
                'code' => 'AK',
                'description' => 'Ada Kotipathi Lottery',
                'is_active' => true,
            ],
            [
                'name' => 'Supiri Dhana Sampatha',
                'code' => 'DS',
                'description' => 'Supiri Dhana Sampatha Lottery',
                'is_active' => true,
            ],
            [
                'name' => 'Jaya Sampatha',
                'code' => 'JS',
                'description' => 'Jaya Sampatha Lottery',
                'is_active' => true,
            ],
            [
                'name' => 'KAPRUKA',
                'code' => 'KP',
                'description' => 'KAPRUKA Lottery',
                'is_active' => true,
            ],
            [
                'name' => 'LAGNA WASANAWA',
                'code' => 'LW',
                'description' => 'LAGNA WASANAWA Lottery',
                'is_active' => true,
            ],
            [
                'name' => 'Super Ball',
                'code' => 'SB',
                'description' => 'Super Ball Lottery',
                'is_active' => true,
            ],
            [
                'name' => 'Shanida',
                'code' => 'SF',
                'description' => 'Shanida Lottery',
                'is_active' => true,
            ],
            [
                'name' => 'SASIRI',
                'code' => 'SR',
                'description' => 'SASIRI Lottery',
                'is_active' => true,
            ],
        ];

        foreach ($lotteryTypes as $type) {
            LotteryType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }

        $this->command->info('Lottery types seeded successfully!');
    }
}
