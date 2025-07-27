<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class ProductInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:mirror';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync product up from prd env';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = DB::connection('mysql2')->select('select * from hoc_products order by prd_name');

        $prds = [];
        collect($products)->each(function ($prd) use (&$prds) {
            $this->comment("name-->$prd->prd_name unit:$prd->base_unit");
            $pid = Uuid::uuid4();
            $prds[] = [
                'id' => $pid,
                'code' => $prd->prd_code,
                'name' => $prd->prd_name,
                'cat' => $prd->cat,
                'comment' => $prd->comment,
                'base_unit' => $prd->base_unit,
//                'unit_map' => $prd->unit_map,
                'sync_time' => $prd->updated_at,
            ];
        });

        // Log::debug($prdIds);

        DB::transaction(function () use ($prds) {
            Product::upsert($prds, ['code'], ['name', 'cat', 'comment', 'base_unit', 'sync_time']);
        });
    }
}
