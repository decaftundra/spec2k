<?php

namespace App\Console\Commands;

use App\Notification;
use App\NotificationPiecePart;
use App\PieceParts\PiecePartDetail;
use App\PowerBiToDoPiecePart;
use App\PowerBiToDoShopFinding;
use App\ShopFindings\ShopFinding;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ExtractToDoPowerBiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spec2kapp:extract_to_do_power_bi_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts all to do spec 2000 data saved in the application and formats it for Power BI.';
    
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
        
        DB::table('power_bi_to_do_piece_parts')->truncate();
        DB::table('power_bi_to_do_shop_findings')->truncate();
        
        // Remove records that are in the in progress list.
        $shopFindingIds = ShopFinding::withTrashed()
            ->pluck('id')
            ->toArray();
        
        Notification::whereNotIn('id', $shopFindingIds)->chunk(self::CHUNKS, function ($shopFindings) use ($powerBiShopFindings) {
            
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
                
                $isValid = false;
                $sf['is_valid'] = $isValid;
                $sf['ready_to_export'] = false; // Default.
                $sf['validation_report'] = 'Not validated.';
                
                if (
                    $isValid
                    && in_array($shopFinding->status, ['complete_shipped', 'complete_scrapped'])
                    && ($shopFinding->shipped_at || $shopFinding->scrapped_at)
                ) {
                    $sf['ready_to_export'] = true;
                }
                
                $sf['hdrCHG'] = $shopFinding->get_HDR_CHG() ?? NULL;
                $sf['hdrROC'] = $shopFinding->get_HDR_ROC() ?? NULL;
                $sf['hdrOPR'] = $shopFinding->get_HDR_OPR() ?? NULL;
                $sf['hdrRON'] = $shopFinding->get_HDR_RON() ?? NULL;
                $sf['hdrWHO'] = $shopFinding->get_HDR_WHO() ?? NULL; 
                $sf['is_hdr_segment_valid'] = false;
                
                $sf['aidMFR'] = $shopFinding->get_AID_MFR() ?? NULL;
                $sf['aidAMC'] = $shopFinding->get_AID_AMC() ?? NULL;
                $sf['aidMFN'] = $shopFinding->get_AID_MFN() ?? NULL;
                $sf['aidASE'] = $shopFinding->get_AID_ASE() ?? NULL;
                $sf['aidAIN'] = $shopFinding->get_AID_AIN() ?? NULL;
                $sf['aidREG'] = $shopFinding->get_AID_REG() ?? NULL;
                $sf['aidOIN'] = $shopFinding->get_AID_OIN() ?? NULL;
                $sf['aidCTH'] = $shopFinding->get_AID_CTH() ?? NULL;
                $sf['aidCTY'] = $shopFinding->get_AID_CTY() ?? NULL;
                $sf['is_aid_segment_valid'] = false;
                
                $sf['eidAET'] = $shopFinding->get_EID_AET() ?? NULL;
                $sf['eidEPC'] = $shopFinding->get_EID_EPC() ?? NULL;
                $sf['eidAEM'] = $shopFinding->get_EID_AEM() ?? NULL;
                $sf['eidEMS'] = $shopFinding->get_EID_EMS() ?? NULL;
                $sf['eidMFR'] = $shopFinding->get_EID_MFR() ?? NULL;
                $sf['eidETH'] = $shopFinding->get_EID_ETH() ?? NULL;
                $sf['eidETC'] = $shopFinding->get_EID_ETC() ?? NULL;
                $sf['is_eid_segment_valid'] = false;
                
                $sf['apiAET'] = $shopFinding->get_API_AET() ?? NULL;
                $sf['apiEMS'] = $shopFinding->get_API_EMS() ?? NULL;
                $sf['apiAEM'] = $shopFinding->get_API_AEM() ?? NULL;
                $sf['apiMFR'] = $shopFinding->get_API_MFR() ?? NULL;
                $sf['apiATH'] = $shopFinding->get_API_ATH() ?? NULL;
                $sf['apiATC'] = $shopFinding->get_API_ATC() ?? NULL;
                $sf['is_api_segment_valid'] = false;
                
                $sf['rcsSFI'] = $shopFinding->get_RCS_SFI() ?? NULL;
                $sf['rcsMRD'] = $shopFinding->get_RCS_MRD() ? $shopFinding->rcsMRD->format('Y-m-d H:i:s') : NULL;
                $sf['rcsMFR'] = $shopFinding->get_RCS_MFR() ?? NULL;
                $sf['rcsMPN'] = $shopFinding->get_RCS_MPN() ?? NULL;
                $sf['rcsSER'] = $shopFinding->get_RCS_SER() ?? NULL;
                $sf['rcsRRC'] = $shopFinding->get_RCS_RRC() ?? NULL;
                $sf['rcsFFC'] = $shopFinding->get_RCS_FFC() ?? NULL;
                $sf['rcsFFI'] = $shopFinding->get_RCS_FFI() ?? NULL;
                $sf['rcsFCR'] = $shopFinding->get_RCS_FCR() ?? NULL;
                $sf['rcsFAC'] = $shopFinding->get_RCS_FAC() ?? NULL;
                $sf['rcsFBC'] = $shopFinding->get_RCS_FBC() ?? NULL;
                $sf['rcsFHS'] = $shopFinding->get_RCS_FHS() ?? NULL;
                $sf['rcsMFN'] = $shopFinding->get_RCS_MFN() ?? NULL;
                $sf['rcsPNR'] = $shopFinding->get_RCS_PNR() ?? NULL;
                $sf['rcsOPN'] = $shopFinding->get_RCS_OPN() ?? NULL;
                $sf['rcsUSN'] = $shopFinding->get_RCS_USN() ?? NULL;
                $sf['rcsRET'] = $shopFinding->get_RCS_RET() ?? NULL;
                $sf['rcsCIC'] = $shopFinding->get_RCS_CIC() ?? NULL;
                $sf['rcsCPO'] = $shopFinding->get_RCS_CPO() ?? NULL;
                $sf['rcsPSN'] = $shopFinding->get_RCS_PSN() ?? NULL;
                $sf['rcsWON'] = $shopFinding->get_RCS_WON() ?? NULL;
                $sf['rcsMRN'] = $shopFinding->get_RCS_MRN() ?? NULL;
                $sf['rcsCTN'] = $shopFinding->get_RCS_CTN() ?? NULL;
                $sf['rcsBOX'] = $shopFinding->get_RCS_BOX() ?? NULL;
                $sf['rcsASN'] = $shopFinding->get_RCS_ASN() ?? NULL;
                $sf['rcsUCN'] = $shopFinding->get_RCS_UCN() ?? NULL;
                $sf['rcsSPL'] = $shopFinding->get_RCS_SPL() ?? NULL;
                $sf['rcsUST'] = $shopFinding->get_RCS_UST() ?? NULL;
                $sf['rcsPDT'] = $shopFinding->get_RCS_PDT() ?? NULL;
                $sf['rcsPML'] = $shopFinding->get_RCS_PML() ?? NULL;
                $sf['rcsSFC'] = $shopFinding->get_RCS_SFC() ?? NULL;
                $sf['rcsRSI'] = $shopFinding->get_RCS_RSI() ?? NULL;
                $sf['rcsRLN'] = $shopFinding->get_RCS_RLN() ?? NULL;
                $sf['rcsINT'] = $shopFinding->get_RCS_INT() ?? NULL;
                $sf['rcsREM'] = $shopFinding->get_RCS_REM() ?? NULL;
                $sf['is_rcs_segment_valid'] = false;
                
                $sf['sasINT'] = $shopFinding->get_SAS_INT() ?? NULL;
                $sf['sasSHL'] = $shopFinding->get_SAS_SHL() ?? NULL;
                $sf['sasRFI'] = $shopFinding->get_SAS_RFI() ?? NULL;
                $sf['sasMAT'] = $shopFinding->get_SAS_MAT() ?? NULL;
                $sf['sasSAC'] = $shopFinding->get_SAS_SAC() ?? NULL;
                $sf['sasSDI'] = $shopFinding->get_SAS_SDI() ?? NULL;
                $sf['sasPSC'] = $shopFinding->get_SAS_PSC() ?? NULL;
                $sf['sasREM'] = $shopFinding->get_SAS_REM() ?? NULL;
                $sf['is_sas_segment_valid'] = $shopFinding->false;
                
                $sf['susSHD'] = $shopFinding->get_SUS_SHD() ? $shopFinding->susSHD->format('Y-m-d H:i:s') : NULL;
                $sf['susMFR'] = $shopFinding->get_SUS_MFR() ?? NULL;
                $sf['susMPN'] = $shopFinding->get_SUS_MPN() ?? NULL;
                $sf['susSER'] = $shopFinding->get_SUS_SER() ?? NULL;
                $sf['susMFN'] = $shopFinding->get_SUS_MFN() ?? NULL;
                $sf['susPDT'] = $shopFinding->get_SUS_PDT() ?? NULL;
                $sf['susPNR'] = $shopFinding->get_SUS_PNR() ?? NULL;
                $sf['susOPN'] = $shopFinding->get_SUS_OPN() ?? NULL;
                $sf['susUSN'] = $shopFinding->get_SUS_USN() ?? NULL;
                $sf['susASN'] = $shopFinding->get_SUS_ASN() ?? NULL;
                $sf['susUCN'] = $shopFinding->get_SUS_UCN() ?? NULL;
                $sf['susSPL'] = $shopFinding->get_SUS_SPL() ?? NULL;
                $sf['susUST'] = $shopFinding->get_SUS_UST() ?? NULL;
                $sf['susPML'] = $shopFinding->get_SUS_PML() ?? NULL;
                $sf['susPSC'] = $shopFinding->get_SUS_PSC() ?? NULL;
                $sf['is_sus_segment_valid'] = false;
                
                $sf['rlsMFR'] = $shopFinding->get_RLS_MFR() ?? NULL;
                $sf['rlsMPN'] = $shopFinding->get_RLS_MPN() ?? NULL;
                $sf['rlsSER'] = $shopFinding->get_RLS_SER() ?? NULL;
                $sf['rlsRED'] = $shopFinding->get_RLS_RED() ? $shopFinding->rlsRED->format('Y-m-d H:i:s') : NULL;
                $sf['rlsTTY'] = $shopFinding->get_RLS_TTY() ?? NULL;
                $sf['rlsRET'] = $shopFinding->get_RLS_RET() ?? NULL;
                $sf['rlsDOI'] = $shopFinding->get_RLS_DOI() ? $shopFinding->rlsDOI->format('Y-m-d H:i:s') : NULL;
                $sf['rlsMFN'] = $shopFinding->get_RLS_MFN() ?? NULL;
                $sf['rlsPNR'] = $shopFinding->get_RLS_PNR() ?? NULL;
                $sf['rlsOPN'] = $shopFinding->get_RLS_OPN() ?? NULL;
                $sf['rlsUSN'] = $shopFinding->get_RLS_USN() ?? NULL;
                $sf['rlsRMT'] = $shopFinding->get_RLS_RMT() ?? NULL;
                $sf['rlsAPT'] = $shopFinding->get_RLS_APT() ?? NULL;
                $sf['rlsCPI'] = $shopFinding->get_RLS_CPI() ?? NULL;
                $sf['rlsCPT'] = $shopFinding->get_RLS_CPT() ?? NULL;
                $sf['rlsPDT'] = $shopFinding->get_RLS_PDT() ?? NULL;
                $sf['rlsPML'] = $shopFinding->get_RLS_PML() ?? NULL;
                $sf['rlsASN'] = $shopFinding->get_RLS_ASN() ?? NULL;
                $sf['rlsUCN'] = $shopFinding->get_RLS_UCN() ?? NULL;
                $sf['rlsSPL'] = $shopFinding->get_RLS_SPL() ?? NULL;
                $sf['rlsUST'] = $shopFinding->get_RLS_UST() ?? NULL;
                $sf['rlsRFR'] = $shopFinding->get_RLS_RFR() ?? NULL;
                $sf['is_rls_segment_valid'] = false;
                    
                $sf['lnkRTI'] = $shopFinding->get_LNK_RTI() ?? NULL;
                $sf['is_lnk_segment_valid'] = false;
                    
                $sf['attTRF'] = $shopFinding->get_ATT_TRF() ?? NULL;
                $sf['attOTT'] = $shopFinding->get_ATT_OTT() ?? NULL;
                $sf['attOPC'] = $shopFinding->get_ATT_OPC() ?? NULL;
                $sf['attODT'] = $shopFinding->get_ATT_ODT() ?? NULL;
                $sf['is_att_segment_valid'] = false;
                    
                $sf['sptMAH'] = $shopFinding->get_SPT_MAH() ?? NULL;
                $sf['sptFLW'] = $shopFinding->get_SPT_FLW() ?? NULL;
                $sf['sptMST'] = $shopFinding->get_SPT_MST() ?? NULL;
                $sf['is_spt_segment_valid'] = false;
                    
                $sf['values'] = NULL;
                $sf['is_misc_segment_valid'] = false;
                
                $powerBiShopFindings[] = $sf;
            }
            
            $this->savePowerBiShopFindingsDataToDatabase($powerBiShopFindings);
        });
        
        /*NotificationPiecePart::chunk(self::CHUNKS, function ($piecePartDetails) use ($powerBiPieceParts) {
            
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
                $pp['notification_id'] = $piecePartDetail->notification_id;
                
                $pp['wpsSFI'] = $piecePartDetail->get_WPS_SFI();
                $pp['wpsPPI'] = $piecePartDetail->get_WPS_PPI();
                $pp['wpsPFC'] = $piecePartDetail->get_WPS_PFC();
                $pp['wpsMFR'] = $piecePartDetail->get_WPS_MFR();
                $pp['wpsMFN'] = $piecePartDetail->get_WPS_MFN();
                $pp['wpsMPN'] = $piecePartDetail->get_WPS_MPN();
                $pp['wpsSER'] = $piecePartDetail->get_WPS_SER();
                $pp['wpsFDE'] = $piecePartDetail->get_WPS_FDE();
                $pp['wpsPNR'] = $piecePartDetail->get_WPS_PNR();
                $pp['wpsOPN'] = $piecePartDetail->get_WPS_OPN();
                $pp['wpsUSN'] = $piecePartDetail->get_WPS_USN();
                $pp['wpsPDT'] = $piecePartDetail->get_WPS_PDT();
                $pp['wpsGEL'] = $piecePartDetail->get_WPS_GEL();
                $pp['wpsMRD'] = $piecePartDetail->get_WPS_MRD() ? $piecePartDetail->wpsMRD->format('Y-m-d H:i:s') : NULL;
                $pp['wpsASN'] = $piecePartDetail->get_WPS_ASN();
                $pp['wpsUCN'] = $piecePartDetail->get_WPS_UCN();
                $pp['wpsSPL'] = $piecePartDetail->get_WPS_SPL();
                $pp['wpsUST'] = $piecePartDetail->get_WPS_UST();
                $pp['is_wps_segment_valid'] = false;
                
                $pp['nhsMFR'] = $piecePartDetail->get_NHS_MFR();
                $pp['nhsMPN'] = $piecePartDetail->get_NHS_MPN();
                $pp['nhsSER'] = $piecePartDetail->get_NHS_SER();
                $pp['nhsMFN'] = $piecePartDetail->get_NHS_MFN();
                $pp['nhsPNR'] = $piecePartDetail->get_NHS_PNR();
                $pp['nhsOPN'] = $piecePartDetail->get_NHS_OPN();
                $pp['nhsUSN'] = $piecePartDetail->get_NHS_USN();
                $pp['nhsPDT'] = $piecePartDetail->get_NHS_PDT();
                $pp['nhsASN'] = $piecePartDetail->get_NHS_ASN();
                $pp['nhsUCN'] = $piecePartDetail->get_NHS_UCN();
                $pp['nhsSPL'] = $piecePartDetail->get_NHS_SPL();
                $pp['nhsUST'] = $piecePartDetail->get_NHS_UST();
                $pp['nhsNPN'] = $piecePartDetail->get_NHS_NPN();
                $pp['is_nhs_segment_valid'] = false;
                
                $pp['rpsMPN'] = $piecePartDetail->get_RPS_MPN();
                $pp['rpsMFR'] = $piecePartDetail->get_RPS_MFR();
                $pp['rpsMFN'] = $piecePartDetail->get_RPS_MFN();
                $pp['rpsSER'] = $piecePartDetail->get_RPS_SER();
                $pp['rpsPNR'] = $piecePartDetail->get_RPS_PNR();
                $pp['rpsOPN'] = $piecePartDetail->get_RPS_OPN();
                $pp['rpsUSN'] = $piecePartDetail->get_RPS_USN();
                $pp['rpsASN'] = $piecePartDetail->get_RPS_ASN();
                $pp['rpsUCN'] = $piecePartDetail->get_RPS_UCN();
                $pp['rpsSPL'] = $piecePartDetail->get_RPS_SPL();
                $pp['rpsUST'] = $piecePartDetail->get_RPS_UST();
                $pp['rpsPDT'] = $piecePartDetail->get_RPS_PDT();
                $pp['is_rps_segment_valid'] = false;
                
                $powerBiPieceParts[] = $pp;
            }
            
            $this->savePowerBiPiecePartsDataToDatabase($powerBiPieceParts);
        });
        
        */
        
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
                //DB::table('power_bi_to_do_shop_findings')->insert($shopFinding);
                try {
                    PowerBiToDoShopFinding::updateOrCreate(['id' => $shopFinding['id']], $shopFinding);
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
                //DB::table('power_bi_to_do_piece_parts')->insert($piecePart);
                try {
                    PowerBiToDoPiecePart::updateOrCreate(['id' => $piecePart['id']], $piecePart);
                } catch (\Exception $e) {
                    report($e);
                }
            }
        }
    }
}
