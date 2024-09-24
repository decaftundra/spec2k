<?php

namespace App\Console\Commands;

use App\Notification;
use App\PieceParts\PiecePartDetail;
use App\PowerBiShopFinding;
use App\PowerBiPiecePart;
use App\ShopFindings\ShopFinding;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ExtractPowerBiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:extract_power_bi_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts all current spec 2000 data saved in the application and formats it for Power BI.';
    
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
        $powerBiShopFindings = [];
        $powerBiPieceParts = [];
        
        Schema::disableForeignKeyConstraints();
        
        DB::table('power_bi_piece_parts')->truncate();
        DB::table('power_bi_shop_findings')->truncate();
        
        ShopFinding::with('HDR_Segment')
        ->with('ShopFindingsDetail.AID_Segment')
        ->with('ShopFindingsDetail.EID_Segment')
        ->with('ShopFindingsDetail.API_Segment')
        ->with('ShopFindingsDetail.RCS_Segment')
        ->with('ShopFindingsDetail.SAS_Segment')
        ->with('ShopFindingsDetail.SUS_Segment')
        ->with('ShopFindingsDetail.RLS_Segment')
        ->with('ShopFindingsDetail.LNK_Segment')
        ->with('ShopFindingsDetail.ATT_Segment')
        ->with('ShopFindingsDetail.SPT_Segment')
        ->with('ShopFindingsDetail.Misc_Segment')
        ->chunk(self::CHUNKS, function($shopFindings) use ($powerBiShopFindings) {
            
            foreach ($shopFindings as $k => $shopFinding) {
                $sf = [
                    'id' => NULL,
                    'plant_code' => NULL,
                    'status' => NULL,
                    'standby_at' => NULL,
                    'subcontracted_at' => NULL,
                    'scrapped_at' => NULL,
                    'shipped_at' => NULL,
                    'is_valid' => NULL,
                    'ready_to_export' => NULL,
                    'validation_report' => NULL,
                    'hdrCHG' => NULL,
                    'hdrROC' => NULL,
                    'hdrOPR' => NULL,
                    'hdrRON' => NULL,
                    'hdrWHO' => NULL,
                    'is_hdr_segment_valid' => NULL,
                    'aidMFR' => NULL,
                    'aidAMC' => NULL,
                    'aidMFN' => NULL,
                    'aidASE' => NULL,
                    'aidAIN' => NULL,
                    'aidREG' => NULL,
                    'aidOIN' => NULL,
                    'aidCTH' => NULL,
                    'aidCTY' => NULL,
                    'is_aid_segment_valid' => NULL,
                    'eidAET' => NULL,
                    'eidEPC' => NULL,
                    'eidAEM' => NULL,
                    'eidEMS' => NULL,
                    'eidMFR' => NULL,
                    'eidETH' => NULL,
                    'eidETC' => NULL,
                    'is_eid_segment_valid' => NULL,
                    'apiAET' => NULL,
                    'apiEMS' => NULL,
                    'apiAEM' => NULL,
                    'apiMFR' => NULL,
                    'apiATH' => NULL,
                    'apiATC' => NULL,
                    'is_api_segment_valid' => NULL,
                    'rcsSFI' => NULL,
                    'rcsMRD' => NULL,
                    'rcsMFR' => NULL,
                    'rcsMPN' => NULL,
                    'rcsSER' => NULL,
                    'rcsRRC' => NULL,
                    'rcsFFC' => NULL,
                    'rcsFFI' => NULL,
                    'rcsFCR' => NULL,
                    'rcsFAC' => NULL,
                    'rcsFBC' => NULL,
                    'rcsFHS' => NULL,
                    'rcsMFN' => NULL,
                    'rcsPNR' => NULL,
                    'rcsOPN' => NULL,
                    'rcsUSN' => NULL,
                    'rcsRET' => NULL,
                    'rcsCIC' => NULL,
                    'rcsCPO' => NULL,
                    'rcsPSN' => NULL,
                    'rcsWON' => NULL,
                    'rcsMRN' => NULL,
                    'rcsCTN' => NULL,
                    'rcsBOX' => NULL,
                    'rcsASN' => NULL,
                    'rcsUCN' => NULL,
                    'rcsSPL' => NULL,
                    'rcsUST' => NULL,
                    'rcsPDT' => NULL,
                    'rcsPML' => NULL,
                    'rcsSFC' => NULL,
                    'rcsRSI' => NULL,
                    'rcsRLN' => NULL,
                    'rcsINT' => NULL,
                    'rcsREM' => NULL,
                    'is_rcs_segment_valid' => NULL,
                    'sasINT' => NULL,
                    'sasSHL' => NULL,
                    'sasRFI' => NULL,
                    'sasMAT' => NULL,
                    'sasSAC' => NULL,
                    'sasSDI' => NULL,
                    'sasPSC' => NULL,
                    'sasREM' => NULL,
                    'is_sas_segment_valid' => NULL,
                    'susSHD' => NULL,
                    'susMFR' => NULL,
                    'susMPN' => NULL,
                    'susSER' => NULL,
                    'susMFN' => NULL,
                    'susPDT' => NULL,
                    'susPNR' => NULL,
                    'susOPN' => NULL,
                    'susUSN' => NULL,
                    'susASN' => NULL,
                    'susUCN' => NULL,
                    'susSPL' => NULL,
                    'susUST' => NULL,
                    'susPML' => NULL,
                    'susPSC' => NULL,
                    'is_sus_segment_valid' => NULL,
                    'rlsMFR' => NULL,
                    'rlsMPN' => NULL,
                    'rlsSER' => NULL,
                    'rlsRED' => NULL,
                    'rlsTTY' => NULL,
                    'rlsRET' => NULL,
                    'rlsDOI' => NULL,
                    'rlsMFN' => NULL,
                    'rlsPNR' => NULL,
                    'rlsOPN' => NULL,
                    'rlsUSN' => NULL,
                    'rlsRMT' => NULL,
                    'rlsAPT' => NULL,
                    'rlsCPI' => NULL,
                    'rlsCPT' => NULL,
                    'rlsPDT' => NULL,
                    'rlsPML' => NULL,
                    'rlsASN' => NULL,
                    'rlsUCN' => NULL,
                    'rlsSPL' => NULL,
                    'rlsUST' => NULL,
                    'rlsRFR' => NULL,
                    'is_rls_segment_valid' => NULL,
                    'lnkRTI' => NULL,
                    'is_lnk_segment_valid' => NULL,
                    'attTRF' => NULL,
                    'attOTT' => NULL,
                    'attOPC' => NULL,
                    'attODT' => NULL,
                    'is_att_segment_valid' => NULL,
                    'sptMAH' => NULL,
                    'sptFLW' => NULL,
                    'sptMST' => NULL,
                    'is_spt_segment_valid' => NULL,
                    'values' => NULL,
                    'is_misc_segment_valid' => NULL
                ];
                
                $sf['id'] = $shopFinding->id;
                $sf['plant_code'] = $shopFinding->plant_code;
                $sf['status'] = $shopFinding->status;
                $sf['standby_at'] = $shopFinding->standby_at ? $shopFinding->standby_at->format('Y-m-d H:i:s') : NULL;
                $sf['subcontracted_at'] = $shopFinding->subcontracted_at ? $shopFinding->subcontracted_at->format('Y-m-d H:i:s') : NULL;
                $sf['scrapped_at'] = $shopFinding->scrapped_at ? $shopFinding->scrapped_at->format('Y-m-d H:i:s') : NULL;
                $sf['shipped_at'] = $shopFinding->shipped_at ? $shopFinding->shipped_at->format('Y-m-d H:i:s') : NULL;
                
                $isValid = $shopFinding->isValid();
                $sf['is_valid'] = $isValid;
                $sf['ready_to_export'] = false; // Default.
                $sf['validation_report'] = $shopFinding->getValidationReport();
                
                if (
                    $isValid
                    && in_array($shopFinding->status, ['complete_shipped', 'complete_scrapped'])
                    && ($shopFinding->shipped_at || $shopFinding->scrapped_at)
                ) {
                    $sf['ready_to_export'] = true;
                }
                
                if ($shopFinding->HDR_Segment) {
                    $sf['hdrCHG'] = $shopFinding->HDR_Segment->get_HDR_CHG() ?? NULL;
                    $sf['hdrROC'] = $shopFinding->HDR_Segment->get_HDR_ROC() ?? NULL;
                    $sf['hdrOPR'] = $shopFinding->HDR_Segment->get_HDR_OPR() ?? NULL;
                    $sf['hdrRON'] = $shopFinding->HDR_Segment->get_HDR_RON() ?? NULL;
                    $sf['hdrWHO'] = $shopFinding->HDR_Segment->get_HDR_WHO() ?? NULL; 
                    $sf['is_hdr_segment_valid'] = $shopFinding->HDR_Segment->is_valid;
                }
                
                if ($shopFinding->ShopFindingsDetail) {
                    if ($shopFinding->ShopFindingsDetail->AID_Segment) {
                        $sf['aidMFR'] = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_MFR() ?? NULL;
                        $sf['aidAMC'] = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_AMC() ?? NULL;
                        $sf['aidMFN'] = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_MFN() ?? NULL;
                        $sf['aidASE'] = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_ASE() ?? NULL;
                        $sf['aidAIN'] = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_AIN() ?? NULL;
                        $sf['aidREG'] = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_REG() ?? NULL;
                        $sf['aidOIN'] = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_OIN() ?? NULL;
                        $sf['aidCTH'] = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_CTH() ?? NULL;
                        $sf['aidCTY'] = $shopFinding->ShopFindingsDetail->AID_Segment->get_AID_CTY() ?? NULL;
                        $sf['is_aid_segment_valid'] = $shopFinding->ShopFindingsDetail->AID_Segment->is_valid;
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->EID_Segment) {
                        $sf['eidAET'] = $shopFinding->ShopFindingsDetail->EID_Segment->get_EID_AET() ?? NULL;
                        $sf['eidEPC'] = $shopFinding->ShopFindingsDetail->EID_Segment->get_EID_EPC() ?? NULL;
                        $sf['eidAEM'] = $shopFinding->ShopFindingsDetail->EID_Segment->get_EID_AEM() ?? NULL;
                        $sf['eidEMS'] = $shopFinding->ShopFindingsDetail->EID_Segment->get_EID_EMS() ?? NULL;
                        $sf['eidMFR'] = $shopFinding->ShopFindingsDetail->EID_Segment->get_EID_MFR() ?? NULL;
                        $sf['eidETH'] = $shopFinding->ShopFindingsDetail->EID_Segment->get_EID_ETH() ?? NULL;
                        $sf['eidETC'] = $shopFinding->ShopFindingsDetail->EID_Segment->get_EID_ETC() ?? NULL;
                        $sf['is_eid_segment_valid'] = $shopFinding->ShopFindingsDetail->EID_Segment->is_valid;
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->API_Segment) {
                        $sf['apiAET'] = $shopFinding->ShopFindingsDetail->API_Segment->get_API_AET() ?? NULL;
                        $sf['apiEMS'] = $shopFinding->ShopFindingsDetail->API_Segment->get_API_EMS() ?? NULL;
                        $sf['apiAEM'] = $shopFinding->ShopFindingsDetail->API_Segment->get_API_AEM() ?? NULL;
                        $sf['apiMFR'] = $shopFinding->ShopFindingsDetail->API_Segment->get_API_MFR() ?? NULL;
                        $sf['apiATH'] = $shopFinding->ShopFindingsDetail->API_Segment->get_API_ATH() ?? NULL;
                        $sf['apiATC'] = $shopFinding->ShopFindingsDetail->API_Segment->get_API_ATC() ?? NULL;
                        $sf['is_api_segment_valid'] = $shopFinding->ShopFindingsDetail->API_Segment->is_valid;
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->RCS_Segment) {
                        $sf['rcsSFI'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_SFI() ?? NULL;
                        $sf['rcsMRD'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_MRD() ? $shopFinding->ShopFindingsDetail->RCS_Segment->MRD->format('Y-m-d H:i:s') : NULL;
                        $sf['rcsMFR'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_MFR() ?? NULL;
                        $sf['rcsMPN'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_MPN() ?? NULL;
                        $sf['rcsSER'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_SER() ?? NULL;
                        $sf['rcsRRC'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_RRC() ?? NULL;
                        $sf['rcsFFC'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_FFC() ?? NULL;
                        $sf['rcsFFI'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_FFI() ?? NULL;
                        $sf['rcsFCR'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_FCR() ?? NULL;
                        $sf['rcsFAC'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_FAC() ?? NULL;
                        $sf['rcsFBC'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_FBC() ?? NULL;
                        $sf['rcsFHS'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_FHS() ?? NULL;
                        $sf['rcsMFN'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_MFN() ?? NULL;
                        $sf['rcsPNR'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_PNR() ?? NULL;
                        $sf['rcsOPN'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_OPN() ?? NULL;
                        $sf['rcsUSN'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_USN() ?? NULL;
                        $sf['rcsRET'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_RET() ?? NULL;
                        $sf['rcsCIC'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_CIC() ?? NULL;
                        $sf['rcsCPO'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_CPO() ?? NULL;
                        $sf['rcsPSN'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_PSN() ?? NULL;
                        $sf['rcsWON'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_WON() ?? NULL;
                        $sf['rcsMRN'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_MRN() ?? NULL;
                        $sf['rcsCTN'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_CTN() ?? NULL;
                        $sf['rcsBOX'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_BOX() ?? NULL;
                        $sf['rcsASN'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_ASN() ?? NULL;
                        $sf['rcsUCN'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_UCN() ?? NULL;
                        $sf['rcsSPL'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_SPL() ?? NULL;
                        $sf['rcsUST'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_UST() ?? NULL;
                        $sf['rcsPDT'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_PDT() ?? NULL;
                        $sf['rcsPML'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_PML() ?? NULL;
                        $sf['rcsSFC'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_SFC() ?? NULL;
                        $sf['rcsRSI'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_RSI() ?? NULL;
                        $sf['rcsRLN'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_RLN() ?? NULL;
                        $sf['rcsINT'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_INT() ?? NULL;
                        $sf['rcsREM'] = $shopFinding->ShopFindingsDetail->RCS_Segment->get_RCS_REM() ?? NULL;
                        $sf['is_rcs_segment_valid'] = $shopFinding->ShopFindingsDetail->RCS_Segment->is_valid;
                    } else {
                        // Record rcsMRD date, rcsMFR, rcsMPN, rcsSER fields from Notification for data purposes.
                        $notification = Notification::find($shopFinding->id);
                        
                        $sf['rcsMRD'] = $notification && $notification->get_RCS_MRD() ? $notification->rcsMRD->format('Y-m-d H:i:s') : NULL;
                        $sf['rcsMFR'] = $notification && $notification->get_RCS_MFR() ? $notification->get_RCS_MFR() : NULL;
                        $sf['rcsMPN'] = $notification && $notification->get_RCS_MPN() ? $notification->get_RCS_MPN() : NULL;
                        $sf['rcsSER'] = $notification && $notification->get_RCS_SER() ? $notification->get_RCS_SER() : NULL;
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->SAS_Segment) {
                        $sf['sasINT'] = $shopFinding->ShopFindingsDetail->SAS_Segment->get_SAS_INT() ?? NULL;
                        $sf['sasSHL'] = $shopFinding->ShopFindingsDetail->SAS_Segment->get_SAS_SHL() ?? NULL;
                        $sf['sasRFI'] = $shopFinding->ShopFindingsDetail->SAS_Segment->get_SAS_RFI() ?? NULL;
                        $sf['sasMAT'] = $shopFinding->ShopFindingsDetail->SAS_Segment->get_SAS_MAT() ?? NULL;
                        $sf['sasSAC'] = $shopFinding->ShopFindingsDetail->SAS_Segment->get_SAS_SAC() ?? NULL;
                        $sf['sasSDI'] = $shopFinding->ShopFindingsDetail->SAS_Segment->get_SAS_SDI() ?? NULL;
                        $sf['sasPSC'] = $shopFinding->ShopFindingsDetail->SAS_Segment->get_SAS_PSC() ?? NULL;
                        $sf['sasREM'] = $shopFinding->ShopFindingsDetail->SAS_Segment->get_SAS_REM() ?? NULL;
                        $sf['is_sas_segment_valid'] = $shopFinding->ShopFindingsDetail->SAS_Segment->is_valid;
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->SUS_Segment) {
                        $sf['susSHD'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_SHD() ? $shopFinding->ShopFindingsDetail->SUS_Segment->SHD->format('Y-m-d H:i:s') : NULL;
                        $sf['susMFR'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_MFR() ?? NULL;
                        $sf['susMPN'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_MPN() ?? NULL;
                        $sf['susSER'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_SER() ?? NULL;
                        $sf['susMFN'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_MFN() ?? NULL;
                        $sf['susPDT'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_PDT() ?? NULL;
                        $sf['susPNR'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_PNR() ?? NULL;
                        $sf['susOPN'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_OPN() ?? NULL;
                        $sf['susUSN'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_USN() ?? NULL;
                        $sf['susASN'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_ASN() ?? NULL;
                        $sf['susUCN'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_UCN() ?? NULL;
                        $sf['susSPL'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_SPL() ?? NULL;
                        $sf['susUST'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_UST() ?? NULL;
                        $sf['susPML'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_PML() ?? NULL;
                        $sf['susPSC'] = $shopFinding->ShopFindingsDetail->SUS_Segment->get_SUS_PSC() ?? NULL;
                        $sf['is_sus_segment_valid'] = $shopFinding->ShopFindingsDetail->SUS_Segment->is_valid;
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->RLS_Segment) {
                        $sf['rlsMFR'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_MFR() ?? NULL;
                        $sf['rlsMPN'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_MPN() ?? NULL;
                        $sf['rlsSER'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_SER() ?? NULL;
                        $sf['rlsRED'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_RED() ? $shopFinding->ShopFindingsDetail->RLS_Segment->RED->format('Y-m-d H:i:s') : NULL;
                        $sf['rlsTTY'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_TTY() ?? NULL;
                        $sf['rlsRET'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_RET() ?? NULL;
                        $sf['rlsDOI'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_DOI() ? $shopFinding->ShopFindingsDetail->RLS_Segment->DOI->format('Y-m-d H:i:s') : NULL;
                        $sf['rlsMFN'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_MFN() ?? NULL;
                        $sf['rlsPNR'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_PNR() ?? NULL;
                        $sf['rlsOPN'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_OPN() ?? NULL;
                        $sf['rlsUSN'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_USN() ?? NULL;
                        $sf['rlsRMT'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_RMT() ?? NULL;
                        $sf['rlsAPT'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_APT() ?? NULL;
                        $sf['rlsCPI'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_CPI() ?? NULL;
                        $sf['rlsCPT'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_CPT() ?? NULL;
                        $sf['rlsPDT'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_PDT() ?? NULL;
                        $sf['rlsPML'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_PML() ?? NULL;
                        $sf['rlsASN'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_ASN() ?? NULL;
                        $sf['rlsUCN'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_UCN() ?? NULL;
                        $sf['rlsSPL'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_SPL() ?? NULL;
                        $sf['rlsUST'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_UST() ?? NULL;
                        $sf['rlsRFR'] = $shopFinding->ShopFindingsDetail->RLS_Segment->get_RLS_RFR() ?? NULL;
                        $sf['is_rls_segment_valid'] = $shopFinding->ShopFindingsDetail->RLS_Segment->is_valid;
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->LNK_Segment) {
                        $sf['lnkRTI'] = $shopFinding->ShopFindingsDetail->LNK_Segment->get_LNK_RTI() ?? NULL;
                        $sf['is_lnk_segment_valid'] = $shopFinding->ShopFindingsDetail->LNK_Segment->is_valid;
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->ATT_Segment) {
                        $sf['attTRF'] = $shopFinding->ShopFindingsDetail->ATT_Segment->get_ATT_TRF() ?? NULL;
                        $sf['attOTT'] = $shopFinding->ShopFindingsDetail->ATT_Segment->get_ATT_OTT() ?? NULL;
                        $sf['attOPC'] = $shopFinding->ShopFindingsDetail->ATT_Segment->get_ATT_OPC() ?? NULL;
                        $sf['attODT'] = $shopFinding->ShopFindingsDetail->ATT_Segment->get_ATT_ODT() ?? NULL;
                        $sf['is_att_segment_valid'] = $shopFinding->ShopFindingsDetail->ATT_Segment->is_valid;
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->SPT_Segment) {
                        $sf['sptMAH'] = $shopFinding->ShopFindingsDetail->SPT_Segment->get_SPT_MAH() ?? NULL;
                        $sf['sptFLW'] = $shopFinding->ShopFindingsDetail->SPT_Segment->get_SPT_FLW() ?? NULL;
                        $sf['sptMST'] = $shopFinding->ShopFindingsDetail->SPT_Segment->get_SPT_MST() ?? NULL;
                        $sf['is_spt_segment_valid'] = $shopFinding->ShopFindingsDetail->SPT_Segment->is_valid;
                    }
                    
                    if ($shopFinding->ShopFindingsDetail->Misc_Segment) {
                        $sf['values'] = $shopFinding->ShopFindingsDetail->Misc_Segment->values ?? NULL;
                        $sf['is_misc_segment_valid'] = $shopFinding->ShopFindingsDetail->Misc_Segment->is_valid;
                    }
                }
                
                $powerBiShopFindings[] = $sf;
                
            }
                
            $this->savePowerBiShopFindingsDataToDatabase($powerBiShopFindings);
        });
        
        PiecePartDetail::with('PiecePart')
        ->with('WPS_Segment')
        ->with('NHS_Segment')
        ->with('RPS_Segment')
        ->chunk(self::CHUNKS, function ($piecePartDetails) use ($powerBiPieceParts) {
                
            foreach ($piecePartDetails as $piecePartDetail) {
                $pp = [
                    'id' => NULL,
                    'notification_id' => NULL,
                    'wpsSFI' => NULL,
                    'wpsPPI' => NULL,
                    'wpsPFC' => NULL,
                    'wpsMFR' => NULL,
                    'wpsMFN' => NULL,
                    'wpsMPN' => NULL,
                    'wpsSER' => NULL,
                    'wpsFDE' => NULL,
                    'wpsPNR' => NULL,
                    'wpsOPN' => NULL,
                    'wpsUSN' => NULL,
                    'wpsPDT' => NULL,
                    'wpsGEL' => NULL,
                    'wpsMRD' => NULL,
                    'wpsASN' => NULL,
                    'wpsUCN' => NULL,
                    'wpsSPL' => NULL,
                    'wpsUST' => NULL,
                    'is_wps_segment_valid' => NULL,
                    'nhsMFR' => NULL,
                    'nhsMPN' => NULL,
                    'nhsSER' => NULL,
                    'nhsMFN' => NULL,
                    'nhsPNR' => NULL,
                    'nhsOPN' => NULL,
                    'nhsUSN' => NULL,
                    'nhsPDT' => NULL,
                    'nhsASN' => NULL,
                    'nhsUCN' => NULL,
                    'nhsSPL' => NULL,
                    'nhsUST' => NULL,
                    'nhsNPN' => NULL,
                    'is_nhs_segment_valid' => NULL,
                    'rpsMPN' => NULL,
                    'rpsMFR' => NULL,
                    'rpsMFN' => NULL,
                    'rpsSER' => NULL,
                    'rpsPNR' => NULL,
                    'rpsOPN' => NULL,
                    'rpsUSN' => NULL,
                    'rpsASN' => NULL,
                    'rpsUCN' => NULL,
                    'rpsSPL' => NULL,
                    'rpsUST' => NULL,
                    'rpsPDT' => NULL,
                    'is_rps_segment_valid' => NULL
                ];
                
                $pp['id'] = $piecePartDetail->id;
                $pp['notification_id'] = $piecePartDetail->PiecePart->shop_finding_id;
                
                if ($piecePartDetail->WPS_Segment) {
                    $pp['wpsSFI'] = $piecePartDetail->WPS_Segment->get_WPS_SFI();
                    $pp['wpsPPI'] = $piecePartDetail->WPS_Segment->get_WPS_PPI();
                    $pp['wpsPFC'] = $piecePartDetail->WPS_Segment->get_WPS_PFC();
                    $pp['wpsMFR'] = $piecePartDetail->WPS_Segment->get_WPS_MFR();
                    $pp['wpsMFN'] = $piecePartDetail->WPS_Segment->get_WPS_MFN();
                    $pp['wpsMPN'] = $piecePartDetail->WPS_Segment->get_WPS_MPN();
                    $pp['wpsSER'] = $piecePartDetail->WPS_Segment->get_WPS_SER();
                    $pp['wpsFDE'] = $piecePartDetail->WPS_Segment->get_WPS_FDE();
                    $pp['wpsPNR'] = $piecePartDetail->WPS_Segment->get_WPS_PNR();
                    $pp['wpsOPN'] = $piecePartDetail->WPS_Segment->get_WPS_OPN();
                    $pp['wpsUSN'] = $piecePartDetail->WPS_Segment->get_WPS_USN();
                    $pp['wpsPDT'] = $piecePartDetail->WPS_Segment->get_WPS_PDT();
                    $pp['wpsGEL'] = $piecePartDetail->WPS_Segment->get_WPS_GEL();
                    $pp['wpsMRD'] = $piecePartDetail->WPS_Segment->get_WPS_MRD() ? $piecePartDetail->WPS_Segment->MRD->format('Y-m-d H:i:s') : NULL;
                    $pp['wpsASN'] = $piecePartDetail->WPS_Segment->get_WPS_ASN();
                    $pp['wpsUCN'] = $piecePartDetail->WPS_Segment->get_WPS_UCN();
                    $pp['wpsSPL'] = $piecePartDetail->WPS_Segment->get_WPS_SPL();
                    $pp['wpsUST'] = $piecePartDetail->WPS_Segment->get_WPS_UST();
                    $pp['is_wps_segment_valid'] = $piecePartDetail->WPS_Segment->is_valid;
                }
                
                if ($piecePartDetail->NHS_Segment) {
                    $pp['nhsMFR'] = $piecePartDetail->NHS_Segment->get_NHS_MFR();
                    $pp['nhsMPN'] = $piecePartDetail->NHS_Segment->get_NHS_MPN();
                    $pp['nhsSER'] = $piecePartDetail->NHS_Segment->get_NHS_SER();
                    $pp['nhsMFN'] = $piecePartDetail->NHS_Segment->get_NHS_MFN();
                    $pp['nhsPNR'] = $piecePartDetail->NHS_Segment->get_NHS_PNR();
                    $pp['nhsOPN'] = $piecePartDetail->NHS_Segment->get_NHS_OPN();
                    $pp['nhsUSN'] = $piecePartDetail->NHS_Segment->get_NHS_USN();
                    $pp['nhsPDT'] = $piecePartDetail->NHS_Segment->get_NHS_PDT();
                    $pp['nhsASN'] = $piecePartDetail->NHS_Segment->get_NHS_ASN();
                    $pp['nhsUCN'] = $piecePartDetail->NHS_Segment->get_NHS_UCN();
                    $pp['nhsSPL'] = $piecePartDetail->NHS_Segment->get_NHS_SPL();
                    $pp['nhsUST'] = $piecePartDetail->NHS_Segment->get_NHS_UST();
                    $pp['nhsNPN'] = $piecePartDetail->NHS_Segment->get_NHS_NPN();
                    $pp['is_nhs_segment_valid'] = $piecePartDetail->NHS_Segment->is_valid;
                }
                
                if ($piecePartDetail->RPS_Segment) {
                    $pp['rpsMPN'] = $piecePartDetail->RPS_Segment->get_RPS_MPN();
                    $pp['rpsMFR'] = $piecePartDetail->RPS_Segment->get_RPS_MFR();
                    $pp['rpsMFN'] = $piecePartDetail->RPS_Segment->get_RPS_MFN();
                    $pp['rpsSER'] = $piecePartDetail->RPS_Segment->get_RPS_SER();
                    $pp['rpsPNR'] = $piecePartDetail->RPS_Segment->get_RPS_PNR();
                    $pp['rpsOPN'] = $piecePartDetail->RPS_Segment->get_RPS_OPN();
                    $pp['rpsUSN'] = $piecePartDetail->RPS_Segment->get_RPS_USN();
                    $pp['rpsASN'] = $piecePartDetail->RPS_Segment->get_RPS_ASN();
                    $pp['rpsUCN'] = $piecePartDetail->RPS_Segment->get_RPS_UCN();
                    $pp['rpsSPL'] = $piecePartDetail->RPS_Segment->get_RPS_SPL();
                    $pp['rpsUST'] = $piecePartDetail->RPS_Segment->get_RPS_UST();
                    $pp['rpsPDT'] = $piecePartDetail->RPS_Segment->get_RPS_PDT();
                    $pp['is_rps_segment_valid'] = $piecePartDetail->RPS_Segment->is_valid;
                }
                
                $powerBiPieceParts[] = $pp;
            }
            
            $this->savePowerBiPiecePartsDataToDatabase($powerBiPieceParts);
        });
        
        Schema::enableForeignKeyConstraints();
    }
    
    /**
     * Write the extracted data to the database.
     *
     * @param (array) $powerBiShopFindings
     * @return void
     */
    private function savePowerBiShopFindingsDataToDatabase(array $powerBiShopFindings)
    {
        if (count($powerBiShopFindings)) {
            foreach ($powerBiShopFindings as $shopFinding) {
                //DB::table('power_bi_shop_findings')->insert($shopFinding);
                try {
                    PowerBiShopFinding::updateOrCreate($shopFinding);
                } catch (\Exception $e) {
                    report($e);
                }
            }
        }
    }
    
    /**
     * Write the extracted data to the database.
     *
     * @param (array) $powerBiPieceParts
     * @return void
     */
    private function savePowerBiPiecePartsDataToDatabase($powerBiPieceParts)
    {
        if (count($powerBiPieceParts)) {
            foreach ($powerBiPieceParts as $piecePart) {
                //DB::table('power_bi_piece_parts')->insert($piecePart);
                try {
                    PowerBiPiecePart::updateOrCreate($piecePart);
                } catch (\Exception $e) {
                    report($e);
                }
            }
        }
    }
}
