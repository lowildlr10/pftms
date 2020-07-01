<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PaperSize;

class PaperSizesMigrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paperSizeData = DB::connection('mysql-old-pftms')
                           ->table('tblpaper_size')
                           ->get();
        $dataCount = $paperSizeData->count();

        foreach ($paperSizeData as $ctr => $p) {
            $percentage = number_format((($ctr + 1) / $dataCount) * 100, 2);
            echo "Paper Sizes: [ $percentage% ] migrated.\n";

            $paper = new PaperSize;
            $paper->paper_type = $p->paper_size;
            $paper->unit = 'mm';
            $paper->width = $p->width;
            $paper->height = $p->height;
            $paper->save();
        }
    }
}
