<?php

namespace Database\Seeders;

use App\Models\ReactType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['type' => 'Excellent', 'icon_code' => '😍', 'sinhala_type' => 'විශිෂ්ටයි',    'tamil_type' => 'மிகச்சிறந்த',      'sort_order' => 1, 'is_active' => true],
            ['type' => 'Good',      'icon_code' => '🙂', 'sinhala_type' => 'හොඳයි',        'tamil_type' => 'சிறந்த',           'sort_order' => 2, 'is_active' => true],
            ['type' => 'Average',   'icon_code' => '😐', 'sinhala_type' => 'සාමාන්‍යයි',   'tamil_type' => 'சராசரி',           'sort_order' => 3, 'is_active' => true],
            ['type' => 'Poor',      'icon_code' => '🙁', 'sinhala_type' => 'දුර්වලයි',     'tamil_type' => 'மோசமான',           'sort_order' => 4, 'is_active' => true],
            ['type' => 'Very Poor', 'icon_code' => '☹️', 'sinhala_type' => 'ඉතා දුර්වලයි', 'tamil_type' => 'மிகவும் மோசமான',  'sort_order' => 5, 'is_active' => true],
        ];

        foreach ($types as $type) {
            ReactType::updateOrCreate(['type' => $type['type']], $type);
        }

        $this->command->info('✅ React types seeded!');
    }
}