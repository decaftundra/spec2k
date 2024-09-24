<?php

namespace App\Console\Commands;

use App\ShopFindings\ShopFinding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixReturnToStockItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:fix_return_to_stock_items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes the shipped_at dates on items that were returned to stock.';

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
        $returnToStock = DB::table('shop_findings')
            ->select('shop_findings.id', 'shop_findings.status', 'shop_findings.shipped_at', 'SUS_Segments.SHD')
            ->join('shop_findings_details', 'shop_findings_details.shop_finding_id', '=', 'shop_findings.id')
            ->join('SUS_Segments', 'shop_findings_details.id', '=', 'SUS_Segments.shop_findings_detail_id')
            ->where('shop_findings.status', '=', 'complete_shipped')
            ->whereRaw('date(shop_findings.shipped_at) > date(SUS_Segments.SHD)')
            ->get();
            
        if (count($returnToStock)) {
            foreach ($returnToStock as $rts) {
                $shopFinding = ShopFinding::find($rts->id);
                $shopFinding->shipped_at = $rts->SHD;
                $shopFinding->save();
            }
        }
    }
}
