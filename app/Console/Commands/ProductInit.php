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
    protected $signature = 'product:init {--clean}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'init product from prd env';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = DB::connection('mysql2')->select('select * from hoc_products');

        $prds = [];
        collect($products)->each(function ($prd) use (&$prds) {
            $prds[] = [
                'id' => Uuid::uuid4(),
                'code' => $prd->prd_code,
                'name' => $prd->prd_name,
                'cat' => $prd->cat,
                'comment' => $prd->comment,
            ];
        });

        if($this->option('clean')){
            Product::truncate();
        }

        Product::upsert($prds, ['id'], []);
    }
}
