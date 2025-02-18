<?php

namespace App\Console\Commands;

use App\Models\DailyStock;
use App\Support\StockImporter;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class StocktakeDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocktake-daily {date? : The date for stocktake, default today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'stocktake for everyday from excel file from stock directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $d = $this->argument("date");
        $format = 'Ymd';
        if (!empty($d) && str_contains($d, '-')) {
            $format = 'Y-m-d';
        }

        $stockDate = $d ? Carbon::createFromFormat($format, $d) : Carbon::now();
//        $this->info("stocktake for date:" . $stockDate->toDateString());

        $path = env('DAILY_STOCK_PATH');
        $file = $path . '/' . $stockDate->format('Ymd') . ' Stock check.xlsx';
        if (!File::isFile($file)) {
            $file = $path . '/' . $stockDate->format('Ymd') . '+Stock+check.xlsx';
            if (!File::isFile($file)) {
                Log::warning('stock file does not exists!' . $file);
                $this->warn('stock file does not exists!' . $file);
                return;
            }
        }

        $fileMd5Hash = File::hash($file);

        $existStock = DailyStock::query()
            ->where('stock_date', $stockDate->toDateString())
            ->firstOrNew([
                'stock_date' => $stockDate->toDateString(),
                'filename' => File::basename($file),
                'hash' => $fileMd5Hash,
            ]);

        if (!empty($existStock->id) && strcmp($fileMd5Hash, $existStock->hash) === 0) {
            return;
        }

//        Log::info('parsing stock file:' . $file);

        $stockArrays = Excel::toArray(new StockImporter(), $file);
        $stock = [];
        $prefix = '';
        foreach ($stockArrays[0] as $array) {
//            $this->info(json_encode($array));
            if (in_array($array[0], ['Beef', 'Pork', 'Chicken', 'Lamb', 'Seafood'])) {
                $prefix = $array[0];
                continue;
            }

            $stock[$prefix . '-' . $array[0]] = $array[1];
        }

        $existStock->stock = $stock;
        $existStock->save();
    }
}
