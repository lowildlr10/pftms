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

        foreach ($paperSizeData as $p) {
            $paper = new PaperSize;
            $paper->paper_type = $p->paper_size;
            $paper->unit = 'mm';
            $paper->width = $p->width;
            $paper->height = $p->height;
            $paper->save();
        }
    }
}
