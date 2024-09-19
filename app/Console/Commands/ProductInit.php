<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
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

        $hocWhId = Warehouse::query()->where('code', 'HoC')->first('id')->id;
        $prds = [];
        $pws = [];
        collect($products)->each(function ($prd) use (&$prds, &$pws, $hocWhId) {
            $pid = Uuid::uuid4();
            $prds[] = [
                'id' => $pid,
                'code' => $prd->prd_code,
                'name' => $prd->prd_name,
                'cat' => $prd->cat,
                'comment' => $prd->comment,
            ];
            $pws[] = [
                'pId' => $pid,
                'whId' => $hocWhId,
                'qty' => $prd->onhand_qty,
                'crat' => Carbon::now(),
                'upat' => Carbon::now(),
            ];
        });

        if ($this->option('clean')) {
            Product::truncate();
            DB::delete('delete from product_warehouse');
        }

        // Log::debug($prdIds);

        DB::transaction(function () use ($prds, $pws) {
            $sql = 'insert into product_warehouse(product_id,warehouse_id,onhand_qty,free_qty,created_at,updated_at) values (:pId,:whId,:qty,0,:crat,:upat)';
            collect($pws)->each(function ($pw) use ($sql) {
                DB::insert($sql, $pw);
            });

            Product::upsert($prds, ['id'], []);
        });
    }
}
