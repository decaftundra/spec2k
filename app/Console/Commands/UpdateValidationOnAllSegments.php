<?php

namespace App\Console\Commands;

use App\ShopFindings\ShopFinding;
use Illuminate\Console\Command;

class UpdateValidationOnAllSegments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:update_validation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the validation on all saved segments';

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
        ShopFinding::withTrashed()->with([
            'HDR_Segment',
            'ShopFindingsDetail.AID_Segment',
            'ShopFindingsDetail.API_Segment',
            'ShopFindingsDetail.ATT_Segment',
            'ShopFindingsDetail.EID_Segment',
            'ShopFindingsDetail.LNK_Segment',
            'ShopFindingsDetail.Misc_Segment',
            'ShopFindingsDetail.RCS_Segment',
            'ShopFindingsDetail.RLS_Segment',
            'ShopFindingsDetail.SAS_Segment',
            'ShopFindingsDetail.SPT_Segment',
            'ShopFindingsDetail.SUS_Segment',
            'PiecePart.PiecePartDetails.WPS_Segment',
            'PiecePart.PiecePartDetails.NHS_Segment',
            'PiecePart.PiecePartDetails.RPS_Segment'
        ])->chunk(100, function($shopFindings){

            foreach($shopFindings as $shopFinding) {
                // loop through all the related segments.
                if ($shopFinding->HDR_Segment) {
                    $shopFinding->HDR_Segment->setIsValid();
                }
                
                if ($shopFinding->ShopFindingsDetail) {
                    if ($shopFinding->ShopFindingsDetail->AID_Segment) {
                        $shopFinding->ShopFindingsDetail->AID_Segment->setIsValid();
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->API_Segment) {
                        $shopFinding->ShopFindingsDetail->API_Segment->setIsValid();
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->ATT_Segment) {
                        $shopFinding->ShopFindingsDetail->ATT_Segment->setIsValid();
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->EID_Segment) {
                        $shopFinding->ShopFindingsDetail->EID_Segment->setIsValid();
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->LNK_Segment) {
                        $shopFinding->ShopFindingsDetail->LNK_Segment->setIsValid();
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->Misc_Segment) {
                        $shopFinding->ShopFindingsDetail->Misc_Segment->setIsValid();
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->RCS_Segment) {
                        $shopFinding->ShopFindingsDetail->RCS_Segment->setIsValid();
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->RLS_Segment) {
                        $shopFinding->ShopFindingsDetail->RLS_Segment->setIsValid();
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->SAS_Segment) {
                        $shopFinding->ShopFindingsDetail->SAS_Segment->setIsValid();
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->SPT_Segment) {
                        $shopFinding->ShopFindingsDetail->SPT_Segment->setIsValid();
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->SUS_Segment) {
                        $shopFinding->ShopFindingsDetail->SUS_Segment->setIsValid();
                    }
                }
                
                if ($shopFinding->PiecePart && count($shopFinding->PiecePart->PiecePartDetails)) {
                    foreach ($shopFinding->PiecePart->PiecePartDetails as $PiecePartDetail) {
                        if ($PiecePartDetail->WPS_Segment) {
                            $PiecePartDetail->WPS_Segment->setIsValid();
                        }
                        
                        if ($PiecePartDetail->NHS_Segment) {
                            $PiecePartDetail->NHS_Segment->setIsValid();
                        }
                        
                        if ($PiecePartDetail->RPS_Segment) {
                            $PiecePartDetail->RPS_Segment->setIsValid();
                        }
                    }
                }
                
                $shopFinding->setIsValid();
                
                $this->info('Validation updated on Shopfinding: ' . $shopFinding->id);
            }
        });
        
        $this->info('Validation updates complete.');
    }
}
