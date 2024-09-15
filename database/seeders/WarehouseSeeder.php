<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $whs = [
            ['id' => Uuid::uuid4(), 'code' => 'HoC', 'name' => 'House of Canivore'],
            ['id' => Uuid::uuid4(), 'code' => 'FridgeIT', 'name' => 'Fridge IT'],
            ['id' => Uuid::uuid4(), 'code' => 'Lineage', 'name' => 'Lineage'],
        ];

        collect($whs)->each(function ($wh) {
            $w = new Warehouse($wh);
            $w->save();
        });
    }
}
