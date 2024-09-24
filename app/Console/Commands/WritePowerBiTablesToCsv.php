<?php

namespace App\Console\Commands;

use App\PowerBiPiecePart;
use App\PowerBiShopFinding;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class WritePowerBiTablesToCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:write_power_bi_tables_to_csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Writes the two power bi tables to csv files.';

    /**
     * The chunk size.
     *
     * @const integer
     */
    const CHUNKS = 50;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $directory = PowerBiShopFinding::POWER_BI_DIRECTORY;

        // Delete old files if they exist.
        if (Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->deleteDirectory($directory);
        }

        $this->savePowerBiShopFindingsDataToCsv();

        $this->savePowerBiPiecePartsDataToCsv();
    }

    /**
     * Get the Power BI data from the database and write it to separate csv files.
     *
     * @return void
     */
    private function savePowerBiShopFindingsDataToCsv()
    {
        $directory = PowerBiShopFinding::POWER_BI_DIRECTORY;
        $shopFindingFilename = 'power_bi_shop_findings_' . Carbon::now()->format('d-m-Y__H-i') . '.csv';

        // Delete temporary file if exists.
        if (file_exists($shopFindingFilename)) {
            unlink($shopFindingFilename);
        }

        $shopFindingsFile = fopen($shopFindingFilename, 'a+');
        $dates = (new PowerBiShopFinding)->getDates();

        // Add column names to file.
        $columns = PowerBiShopFinding::getTableColumns();
        fputcsv($shopFindingsFile, (array) $columns);

        PowerBiShopFinding::chunk(self::CHUNKS, function ($powerBiShopFindings) use ($dates, $shopFindingsFile) {

            foreach ($powerBiShopFindings as $powerBiShopFinding) {

                $powerBiShopFinding = $powerBiShopFinding->toArray();

                foreach ($powerBiShopFinding as $k => $v) {
                    if (in_array($k, $dates) && $v) {
                        $powerBiShopFinding[$k] = Carbon::createFromFormat('Y-m-d H:i:s', $v)->toDateString();
                    }
                }

                fputcsv($shopFindingsFile, (array) array_values($powerBiShopFinding));
            }

        });

        // Store new file.
        Storage::disk('local')->put($directory . DIRECTORY_SEPARATOR . $shopFindingFilename, $shopFindingsFile);

        fclose($shopFindingsFile);




        // LJMMay23 MGTSUP-449
        Log::info('LJM Saved: ' . $directory . DIRECTORY_SEPARATOR . $shopFindingFilename);





        // Remove temporary file.
        unlink($shopFindingFilename);
    }

    /**
     * Get the Power BI data from the database and write it to separate csv files.
     *
     * @return void
     */
    private function savePowerBiPiecePartsDataToCsv()
    {
        $directory = PowerBiShopFinding::POWER_BI_DIRECTORY;
        $piecePartsFilename = 'power_bi_piece_parts_' . Carbon::now()->format('d-m-Y__H-i') . '.csv';

        // Delete temporary file if exists.
        if (file_exists($piecePartsFilename)) {
            unlink($piecePartsFilename);
        }

        $piecePartsFile = fopen($piecePartsFilename, 'a+');
        $dates = (new PowerBiPiecePart)->getDates();

        // Add column names to file.
        $columns = PowerBiPiecePart::getTableColumns();
        fputcsv($piecePartsFile, (array) $columns);

        PowerBiPiecePart::chunk(self::CHUNKS, function ($powerBiPieceParts) use ($dates, $piecePartsFile) {

            foreach ($powerBiPieceParts as $powerBiPiecePart) {

                $powerBiPiecePart = $powerBiPiecePart->toArray();

                foreach ($powerBiPiecePart as $k => $v) {
                    if (in_array($k, $dates) && $v) {
                        $powerBiPiecePart[$k] = Carbon::createFromFormat('Y-m-d H:i:s', $v)->toDateString();
                    }
                }

                fputcsv($piecePartsFile, (array) array_values($powerBiPiecePart));
            }

        });

        // Store new file.
        Storage::disk('local')->put($directory . DIRECTORY_SEPARATOR . $piecePartsFilename, $piecePartsFile);

        fclose($piecePartsFile);




        // LJMMay23 MGTSUP-449
        Log::info('LJM Saved: ' . $directory . DIRECTORY_SEPARATOR . $piecePartsFilename);






        // Remove temporary file.
        unlink($piecePartsFilename);
    }
}
