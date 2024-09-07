<?php

namespace Database\Seeders;

use App\Models\Run;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class RunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $runs = [
            [
                "id" => "1316a5496bef4fcc8cdd55cc28ec8a8d",
                "code" => "EA",
                "name" => "East Afternoon",
            ],
            [
                "id" => "177d340eaa7b4f57991c681910b44aa6",
                "code" => "LE",
                "name" => "Late East",
            ],
            [
                "id" => "36f336bc49a64e2ba8946f6ea8f693cb",
                "code" => "EE",
                "name" => "Early East",
            ],
            [
                "id" => "3b32d552d99b472d8943e4576ba064a2",
                "code" => "RM2",
                "name" => "Rundle Mall 02",
            ],
            [
                "id" => "3d5eec9248144295a75c1e14dddf58f8",
                "code" => "W",
                "name" => "West",
            ],
            [
                "id" => "488afc25058645beb9925eebb7bbe2ef",
                "code" => "RM1",
                "name" => "Rundle Mall 01",
            ],
            [
                "id" => "6c99c449773845d984e51416c0f174c6",
                "code" => "ED",
                "name" => "Early Delivery",
            ],
            [
                "id" => "6de261d9ab7b41bbaf91aada8be3040e",
                "code" => "CA",
                "name" => "Chinatown Afternoon",
            ],
            [
                "id" => "7247dc930c144c9d91d839e55f9a51da",
                "code" => "N",
                "name" => "North",
            ],
            [
                "id" => "868df02725744e888c427a20df17fd75",
                "code" => "S",
                "name" => "South & Hill",
            ],
            [
                "id" => "923e95f656cd4f02a46ecb1520125c04",
                "code" => "CT",
                "name" => "Chinatown",
            ],
            [
                "id" => "bf067e17a77d4581a2fbaae30b528585",
                "code" => "PU",
                "name" => "Pickup",
            ],
            [
                "id" => "cc23150e1bab4906aa6a11e00c367ff1",
                "code" => "~NR",
                "name" => "No Run Assigned",
            ],
            [
                "id" => "d0a1ad7e46154b8680504f7e7f472f0e",
                "code" => "TTP",
                "name" => "Tea Tree Plaza",
            ]
        ];

        collect($runs)->each(function ($run) {
            $run['id'] = Uuid::fromString($run['id']);
            $r = new Run($run);
            $r->save();
        });
    }
}
