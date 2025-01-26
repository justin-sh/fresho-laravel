<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        if (empty($hocWhId)) {
            $this->error("Please seeder warehouse database first");
            return;
        }

        $prds = [];
        $pws = [];
        collect($products)->each(function ($prd) use (&$prds, &$pws, $hocWhId) {
//            Log::info('name=>'. $prd->prd_name . ' qty=>'.$prd->onhand_qty);
            $pid = Uuid::uuid4();
            $prds[] = [
                'id' => $pid,
                'code' => $prd->prd_code,
                'name' => $prd->prd_name,
                'cat' => $prd->cat,
                'comment' => $prd->comment,
            ];
//            $pws[] = [
//                'pId' => $pid,
//                'whId' => $hocWhId,
//                'qty' => $prd->onhand_qty,
//                'crat' => Carbon::now(),
//                'upat' => Carbon::now(),
//            ];
            $pws[] = [
                $prd->prd_code,
                'HoC',
                $prd->onhand_qty,
                Carbon::now(),
                Carbon::now(),
                $prd->onhand_qty,
                Carbon::now(),
            ];
        });

        if ($this->option('clean')) {
            Product::truncate();
            DB::delete('delete from product_warehouse');
        }

        // Log::debug($prdIds);

        DB::transaction(function () use ($prds, $pws) {
            $sql = 'insert into product_warehouse(prd_code,wh_code,onhand_qty,free_qty,created_at,updated_at)'
                    . ' values (?,?,?,0,?,?)'
                    . ' ON DUPLICATE KEY UPDATE onhand_qty=?, updated_at=?';
            collect($pws)->each(function ($pw) use ($sql) {
                DB::insert($sql, $pw);
            });

            Product::upsert($prds, ['id'], []);
        });
    }
}
