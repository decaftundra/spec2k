<?php

namespace App;

use App\Activity;
use Carbon\Carbon;
use App\HDR_Segment;
use App\ShopFindings\ShopFindingsDetail;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\SAS_Segment;
use App\ShopFindings\Misc_Segment;
use App\ShopFindings\AID_Segment;
use App\ShopFindings\EID_Segment;
use App\ShopFindings\API_Segment;
use App\ShopFindings\SUS_Segment;
use App\ShopFindings\RLS_Segment;
use App\ShopFindings\LNK_Segment;
use App\ShopFindings\ATT_Segment;
use App\ShopFindings\SPT_Segment;
use App\PieceParts\PiecePart;
use App\PieceParts\PiecePartDetail;
use App\PieceParts\NHS_Segment;
use App\PieceParts\RPS_Segment;
use App\PieceParts\WPS_Segment;
use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CsvImporter extends Model
{
    use DateTrait, HasFactory;
    
    /**
     * The boeing csv directory.
     *
     * @var string
     */
    protected $csvDirectory = 'csv-import-files';
    
    /**
     * The shop finding expected csv columns.
     *
     * @var array
     */
    public static $shopFindingColumns = [
        'id',
        'plant_code',
        'status',
        'standby_at',
        'subcontracted_at',
        'scrapped_at',
        'shipped_at',
        'is_valid',
        'ready_to_export',
        'validation_report',
        'hdrCHG',
        'hdrROC',
        'hdrOPR',
        'hdrRON',
        'hdrWHO',
        'is_hdr_segment_valid',
        'aidMFR',
        'aidAMC',
        'aidMFN',
        'aidASE',
        'aidAIN',
        'aidREG',
        'aidOIN',
        'aidCTH',
        'aidCTY',
        'is_aid_segment_valid',
        'eidAET',
        'eidEPC',
        'eidAEM',
        'eidEMS',
        'eidMFR',
        'eidETH',
        'eidETC',
        'is_eid_segment_valid',
        'apiAET',
        'apiEMS',
        'apiAEM',
        'apiMFR',
        'apiATH',
        'apiATC',
        'is_api_segment_valid',
        'rcsSFI',
        'rcsMRD',
        'rcsMFR',
        'rcsMPN',
        'rcsSER',
        'rcsRRC',
        'rcsFFC',
        'rcsFFI',
        'rcsFCR',
        'rcsFAC',
        'rcsFBC',
        'rcsFHS',
        'rcsMFN',
        'rcsPNR',
        'rcsOPN',
        'rcsUSN',
        'rcsRET',
        'rcsCIC',
        'rcsCPO',
        'rcsPSN',
        'rcsWON',
        'rcsMRN',
        'rcsCTN',
        'rcsBOX',
        'rcsASN',
        'rcsUCN',
        'rcsSPL',
        'rcsUST',
        'rcsPDT',
        'rcsPML',
        'rcsSFC',
        'rcsRSI',
        'rcsRLN',
        'rcsINT',
        'rcsREM',
        'is_rcs_segment_valid',
        'sasINT',
        'sasSHL',
        'sasRFI',
        'sasMAT',
        'sasSAC',
        'sasSDI',
        'sasPSC',
        'sasREM',
        'is_sas_segment_valid',
        'susSHD',
        'susMFR',
        'susMPN',
        'susSER',
        'susMFN',
        'susPDT',
        'susPNR',
        'susOPN',
        'susUSN',
        'susASN',
        'susUCN',
        'susSPL',
        'susUST',
        'susPML',
        'susPSC',
        'is_sus_segment_valid',
        'rlsMFR',
        'rlsMPN',
        'rlsSER',
        'rlsRED',
        'rlsTTY',
        'rlsRET',
        'rlsDOI',
        'rlsMFN',
        'rlsPNR',
        'rlsOPN',
        'rlsUSN',
        'rlsRMT',
        'rlsAPT',
        'rlsCPI',
        'rlsCPT',
        'rlsPDT',
        'rlsPML',
        'rlsASN',
        'rlsUCN',
        'rlsSPL',
        'rlsUST',
        'rlsRFR',
        'is_rls_segment_valid',
        'lnkRTI',
        'is_lnk_segment_valid',
        'attTRF',
        'attOTT',
        'attOPC',
        'attODT',
        'is_att_segment_valid',
        'sptMAH',
        'sptFLW',
        'sptMST',
        'is_spt_segment_valid',
        'values',
        'is_misc_segment_valid',
    ];
    
    /**
     * The piece part expected csv columns..
     *
     * @var array
     */
    public static $piecePartColumns = [
        'id',
        'notification_id',
        'wpsSFI',
        'wpsPPI',
        'wpsPFC',
        'wpsMFR',
        'wpsMFN',
        'wpsMPN',
        'wpsSER',
        'wpsFDE',
        'wpsPNR',
        'wpsOPN',
        'wpsUSN',
        'wpsPDT',
        'wpsGEL',
        'wpsMRD',
        'wpsASN',
        'wpsUCN',
        'wpsSPL',
        'wpsUST',
        'is_wps_segment_valid',
        'nhsMFR',
        'nhsMPN',
        'nhsSER',
        'nhsMFN',
        'nhsPNR',
        'nhsOPN',
        'nhsUSN',
        'nhsPDT',
        'nhsASN',
        'nhsUCN',
        'nhsSPL',
        'nhsUST',
        'nhsNPN',
        'is_nhs_segment_valid',
        'rpsMPN',
        'rpsMFR',
        'rpsMFN',
        'rpsSER',
        'rpsPNR',
        'rpsOPN',
        'rpsUSN',
        'rpsASN',
        'rpsUCN',
        'rpsSPL',
        'rpsUST',
        'rpsPDT',
        'is_rps_segment_valid'
    ];
    
    /**
     * Return the validation rules array.
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'shopfindings_file' => 'required_without:pieceparts_file|file|mimes:csv,txt',
            'pieceparts_file' => 'required_without:shopfindings_file|file|mimes:csv,txt'
        ];
    }
    
    /**
     * Import new Shop Finding records, ignore existing ones.
     *
     * @params array $data
     * @return void
     */
    protected static function importShopFindingCsv(array $data)
    {
        $collection = collect($data);
        
        Schema::disableForeignKeyConstraints();
        
        foreach (collect($data)->chunk(100) as $chunk) {
            DB::transaction(function () use ($chunk) {
                self::deletePiecePartsAndSegments($chunk->pluck('id')->toArray());
                self::deleteShopFindingAndSegments($chunk->pluck('id')->toArray());
                DB::table('notifications')->upsert($chunk->toArray(), ['rcsSFI']);
            });
        }
        
        Schema::enableForeignKeyConstraints();
    }
    
    /**
     * Import new Piece Part records, ignore existing ones.
     *
     * @params array $data
     * @return void
     */
    protected static function importPiecePartCsv(array $data)
    {
        $collection = collect($data);
        
        Schema::disableForeignKeyConstraints();
        
        foreach ($collection->chunk(100) as $chunk) {
            DB::transaction(function () use ($chunk) {
                self::deletePiecePartsAndSegments($chunk->pluck('id')->toArray());
                DB::table('notification_piece_parts')->upsert($chunk->toArray(), ['id', 'notification_id']);
            });
        }
        
        Schema::enableForeignKeyConstraints();
    }
    
    /**
     * Check that the csv columns are correct for the shop findings.
     *
     * @params array $data
     * @return bool
     */
    protected static function checkShopFindingCsvColumns(array $data)
    {
        return $data == self::$shopFindingColumns;
    }
    
    /**
     * Check that the csv columns are correct for the piece parts.
     *
     * @params array $data
     * @return bool
     */
    protected static function checkPiecePartCsvColumns(array $data)
    {
        return $data == self::$piecePartColumns;
    }
    
    /**
     * Map Shop Finding csv data to the correct data parameter.
     *
     * @params array $row
     * @return array
     */
    protected static function mapShopFindingRowData(array $row)
    {
        return [
            'id' => $row[0],
            'plant_code' => $row[1],
            'hdrCHG' => !empty($row[10]) ? $row[10] : NULL,
            'hdrROC' => !empty($row[11]) ? $row[11] : NULL,
            'hdrRDT' => NULL,
            'hdrRSD' => NULL,
            'hdrOPR' => !empty($row[12]) ? $row[12] : NULL,
            'hdrRON' => !empty($row[13]) ? $row[13] : NULL,
            'hdrWHO' => !empty($row[14]) ? $row[14] : NULL,
            
            'aidMFR' => !empty($row[16]) ? $row[16] : NULL,
            'aidAMC' => !empty($row[17]) ? $row[17] : NULL,
            'aidMFN' => !empty($row[18]) ? $row[18] : NULL,
            'aidASE' => !empty($row[19]) ? $row[19] : NULL,
            'aidAIN' => !empty($row[20]) ? $row[20] : NULL,
            'aidREG' => !empty($row[21]) ? $row[21] : NULL,
            'aidOIN' => !empty($row[22]) ? $row[22] : NULL,
            'aidCTH' => !empty($row[23]) ? $row[23] : NULL,
            'aidCTY' => !empty($row[24]) ? $row[24] : NULL,
            
            'eidAET' => !empty($row[26]) ? $row[26] : NULL,
            'eidEPC' => !empty($row[27]) ? $row[27] : NULL,
            'eidAEM' => !empty($row[28]) ? $row[28] : NULL,
            'eidEMS' => !empty($row[29]) ? $row[29] : NULL,
            'eidMFR' => !empty($row[30]) ? $row[30] : NULL,
            'eidETH' => !empty($row[31]) ? $row[31] : NULL,
            'eidETC' => !empty($row[32]) ? $row[32] : NULL,
            
            'apiAET' => !empty($row[34]) ? $row[34] : NULL,
            'apiEMS' => !empty($row[35]) ? $row[35] : NULL,
            'apiAEM' => !empty($row[36]) ? $row[36] : NULL,
            'apiMFR' => !empty($row[37]) ? $row[37] : NULL,
            'apiATH' => !empty($row[38]) ? $row[38] : NULL,
            'apiATC' => !empty($row[39]) ? $row[39] : NULL,
            
            'rcsSFI' => !empty($row[0]) ? $row[0] : NULL,
            'rcsMRD' => array_key_exists(42, $row) && self::validateDate($row[42]) ? Carbon::createFromFormat('d/m/Y', $row[42]) : NULL,
            'rcsMFR' => !empty($row[43]) ? $row[43] : NULL,
            'rcsMPN' => !empty($row[44]) ? $row[44] : NULL,
            'rcsSER' => !empty($row[45]) ? $row[45] : NULL,
            'rcsRRC' => !empty($row[46]) ? $row[46] : NULL,
            'rcsFFC' => !empty($row[47]) ? $row[47] : NULL,
            'rcsFFI' => !empty($row[48]) ? $row[48] : NULL,
            'rcsFCR' => !empty($row[49]) ? $row[49] : NULL,
            'rcsFAC' => !empty($row[50]) ? $row[50] : NULL,
            'rcsFBC' => !empty($row[51]) ? $row[51] : NULL,
            'rcsFHS' => !empty($row[52]) ? $row[52] : NULL,
            'rcsMFN' => !empty($row[53]) ? $row[53] : NULL,
            'rcsPNR' => !empty($row[54]) ? $row[54] : NULL,
            'rcsOPN' => !empty($row[55]) ? $row[55] : NULL,
            'rcsUSN' => !empty($row[56]) ? $row[56] : NULL,
            'rcsRET' => !empty($row[57]) ? $row[57] : NULL,
            'rcsCIC' => !empty($row[58]) ? $row[58] : NULL,
            'rcsCPO' => !empty($row[59]) ? $row[59] : NULL,
            'rcsPSN' => !empty($row[60]) ? $row[60] : NULL,
            'rcsWON' => !empty($row[61]) ? $row[61] : NULL,
            'rcsMRN' => !empty($row[62]) ? $row[62] : NULL,
            'rcsCTN' => !empty($row[63]) ? $row[63] : NULL,
            'rcsBOX' => !empty($row[64]) ? $row[64] : NULL,
            'rcsASN' => !empty($row[65]) ? $row[65] : NULL,
            'rcsUCN' => !empty($row[66]) ? $row[66] : NULL,
            'rcsSPL' => !empty($row[67]) ? $row[67] : NULL,
            'rcsUST' => !empty($row[68]) ? $row[68] : NULL,
            'rcsPDT' => !empty($row[69]) ? $row[69] : NULL,
            'rcsPML' => !empty($row[70]) ? $row[70] : NULL,
            'rcsSFC' => !empty($row[71]) ? $row[71] : NULL,
            'rcsRSI' => !empty($row[72]) ? $row[72] : NULL,
            'rcsRLN' => !empty($row[73]) ? $row[73] : NULL,
            'rcsINT' => !empty($row[74]) ? utf8_encode($row[74]) : NULL,
            'rcsREM' => !empty($row[75]) ? utf8_encode($row[75]) : NULL,
            
            'sasINT' => !empty($row[77]) ? utf8_encode($row[77]) : NULL,
            'sasSHL' => !empty($row[78]) ? $row[78] : NULL,
            'sasRFI' => array_key_exists(79, $row) && !is_null($row[79]) ? $row[79] : NULL, // Can be either null, 0, or 1.
            'sasMAT' => !empty($row[80]) ? $row[80] : NULL,
            'sasSAC' => !empty($row[81]) ? $row[81] : NULL,
            'sasSDI' => !empty($row[82]) ? $row[82] : NULL,
            'sasPSC' => !empty($row[83]) ? $row[83] : NULL,
            'sasREM' => !empty($row[84]) ? utf8_encode($row[84]) : NULL,
            
            'susSHD' => array_key_exists(86, $row) && self::validateDate($row[86]) ? Carbon::createFromFormat('d/m/Y', $row[86]) : NULL,
            'susMFR' => !empty($row[87]) ? $row[87] : NULL,
            'susMPN' => !empty($row[88]) ? $row[88] : NULL,
            'susSER' => !empty($row[89]) ? $row[89] : NULL,
            'susMFN' => !empty($row[90]) ? $row[90] : NULL,
            'susPDT' => !empty($row[91]) ? $row[91] : NULL,
            'susPNR' => !empty($row[92]) ? $row[92] : NULL,
            'susOPN' => !empty($row[93]) ? $row[93] : NULL,
            'susUSN' => !empty($row[94]) ? $row[94] : NULL,
            'susASN' => !empty($row[95]) ? $row[95] : NULL,
            'susUCN' => !empty($row[96]) ? $row[96] : NULL,
            'susSPL' => !empty($row[97]) ? $row[97] : NULL,
            'susUST' => !empty($row[98]) ? $row[98] : NULL,
            'susPML' => !empty($row[99]) ? $row[99] : NULL,
            'susPSC' => !empty($row[100]) ? $row[100] : NULL,
            
            'rlsMFR' => !empty($row[102]) ? $row[102] : NULL,
            'rlsMPN' => !empty($row[103]) ? $row[103] : NULL,
            'rlsSER' => !empty($row[104]) ? $row[104] : NULL,
            'rlsRED' => array_key_exists(105, $row) && self::validateDate($row[105]) ? Carbon::createFromFormat('d/m/Y', $row[105]) : NULL,
            'rlsTTY' => !empty($row[106]) ? $row[106] : NULL,
            'rlsRET' => !empty($row[107]) ? $row[107] : NULL,
            'rlsDOI' => array_key_exists(108, $row) && self::validateDate($row[108]) ? Carbon::createFromFormat('d/m/Y', $row[108]) : NULL,
            'rlsMFN' => !empty($row[109]) ? $row[109] : NULL,
            'rlsPNR' => !empty($row[110]) ? $row[110] : NULL,
            'rlsOPN' => !empty($row[111]) ? $row[111] : NULL,
            'rlsUSN' => !empty($row[112]) ? $row[112] : NULL,
            'rlsRMT' => !empty($row[113]) ? utf8_encode($row[113]) : NULL,
            'rlsAPT' => !empty($row[114]) ? $row[114] : NULL,
            'rlsCPI' => !empty($row[115]) ? $row[115] : NULL,
            'rlsCPT' => !empty($row[116]) ? $row[116] : NULL,
            'rlsPDT' => !empty($row[117]) ? $row[117] : NULL,
            'rlsPML' => !empty($row[118]) ? $row[118] : NULL,
            'rlsASN' => !empty($row[119]) ? $row[119] : NULL,
            'rlsUCN' => !empty($row[120]) ? $row[120] : NULL,
            'rlsSPL' => !empty($row[121]) ? $row[121] : NULL,
            'rlsUST' => !empty($row[122]) ? $row[122] : NULL,
            'rlsRFR' => !empty($row[123]) ? $row[123] : NULL,
            
            'lnkRTI' => !empty($row[125]) ? $row[125] : NULL,
            
            'attTRF' => !empty($row[127]) ? $row[127] : NULL,
            'attOTT' => !empty($row[128]) ? $row[128] : NULL,
            'attOPC' => !empty($row[129]) ? $row[129] : NULL,
            'attODT' => !empty($row[130]) ? $row[130] : NULL,
            
            'sptMAH' => !empty($row[132]) ? $row[132] : NULL,
            'sptFLW' => !empty($row[133]) ? $row[133] : NULL,
            'sptMST' => !empty($row[134]) ? $row[134] : NULL,
            
            'is_csv_import' => 1,
            'csv_import_autosaved_at' => NULL,
            'values' => json_decode($row[136]) ? $row[136] : NULL, /*$row[136]*/ // Misc Segment.
            
            'created_at' => Carbon::now(),
            
            'status' => $row[2],
            'standby_at' => array_key_exists(3, $row) && self::validateDate($row[3]) ? Carbon::createFromFormat('d/m/Y', $row[3]) : NULL,
            'subcontracted_at' => array_key_exists(4, $row) && self::validateDate($row[4]) ? Carbon::createFromFormat('d/m/Y', $row[4]) : NULL,
            'scrapped_at' => array_key_exists(5, $row) && self::validateDate($row[5]) ? Carbon::createFromFormat('d/m/Y', $row[5]) : NULL,
            'shipped_at' => array_key_exists(6, $row) && self::validateDate($row[6]) ? Carbon::createFromFormat('d/m/Y', $row[6]) : NULL,
        ];
    }
    
    /**
     * Map Piece Part csv data to the correct data parameter.
     *
     * @params array $row
     * @return array
     */
    protected static function mapPiecePartRowData(array $row)
    {
        return [
            'id' => $row[0],
            'notification_id' => $row[1],
            'wpsSFI' => !empty($row[1]) ? $row[1] : NULL,
            'wpsPPI' => !empty($row[0]) ? $row[0] : NULL,
            'wpsPFC' => !empty($row[4]) ? $row[4] : NULL,
            'wpsMFR' => !empty($row[5]) ? $row[5] : NULL,
            'wpsMFN' => !empty($row[6]) ? $row[6] : NULL,
            'wpsMPN' => !empty($row[7]) ? $row[7] : NULL,
            'wpsSER' => !empty($row[8]) ? $row[8] : NULL,
            'wpsFDE' => !empty($row[9]) ? utf8_encode($row[9]) : NULL, // Text field
            'wpsPNR' => !empty($row[10]) ? $row[10] : NULL,
            'wpsOPN' => !empty($row[11]) ? $row[11] : NULL,
            'wpsUSN' => !empty($row[12]) ? $row[12] : NULL,
            'wpsPDT' => !empty($row[13]) ? utf8_encode($row[13]) : NULL, // Text field
            'wpsGEL' => !empty($row[14]) ? $row[14] : NULL,
            'wpsMRD' => array_key_exists(15, $row) && self::validateDate($row[15]) ? Carbon::createFromFormat('d/m/Y', $row[15]) : NULL, // Date
            'wpsASN' => !empty($row[16]) ? $row[16] : NULL,
            'wpsUCN' => !empty($row[17]) ? $row[17] : NULL,
            'wpsSPL' => !empty($row[18]) ? $row[18] : NULL,
            'wpsUST' => !empty($row[19]) ? $row[19] : NULL,
            'nhsMFR' => !empty($row[21]) ? $row[21] : NULL,
            'nhsMPN' => !empty($row[22]) ? $row[22] : NULL,
            'nhsSER' => !empty($row[23]) ? $row[23] : NULL,
            'nhsMFN' => !empty($row[24]) ? $row[24] : NULL,
            'nhsPNR' => !empty($row[25]) ? $row[25] : NULL,
            'nhsOPN' => !empty($row[26]) ? $row[26] : NULL,
            'nhsUSN' => !empty($row[27]) ? $row[27] : NULL,
            'nhsPDT' => !empty($row[28]) ? $row[28] : NULL,
            'nhsASN' => !empty($row[29]) ? $row[29] : NULL,
            'nhsUCN' => !empty($row[30]) ? $row[30] : NULL,
            'nhsSPL' => !empty($row[31]) ? $row[31] : NULL,
            'nhsUST' => !empty($row[32]) ? $row[32] : NULL,
            'nhsNPN' => !empty($row[33]) ? $row[33] : NULL,
            'rpsMPN' => !empty($row[35]) ? $row[35] : NULL,
            'rpsMFR' => !empty($row[36]) ? $row[36] : NULL,
            'rpsMFN' => !empty($row[37]) ? $row[37] : NULL,
            'rpsSER' => !empty($row[38]) ? $row[38] : NULL,
            'rpsPNR' => !empty($row[39]) ? $row[39] : NULL,
            'rpsOPN' => !empty($row[40]) ? $row[40] : NULL,
            'rpsUSN' => !empty($row[41]) ? $row[41] : NULL,
            'rpsASN' => !empty($row[42]) ? $row[42] : NULL,
            'rpsUCN' => !empty($row[43]) ? $row[43] : NULL,
            'rpsSPL' => !empty($row[44]) ? $row[44] : NULL,
            'rpsUST' => !empty($row[45]) ? $row[45] : NULL,
            'rpsPDT' => !empty($row[46]) ? $row[46] : NULL,
            'reversal_id' => NULL,
            'created_at' => Carbon::now(), // Date
            'updated_at' => Carbon::now(), // Date
            'deleted_at' => NULL, // Date
        ];
    }
    
    /**
     * Delete the shop finding records and all related segments.
     *
     * @params array $shopFindingIds
     * @return void
     */
    protected static function deleteShopFindingAndSegments(array $shopFindingIds)
    {
        // Disconnect related activities.
        Activity::whereIn('shop_finding_id', $shopFindingIds)->update(['shop_finding_id' => NULL]);
        
        $shopFindingDetailIds = ShopFindingsDetail::whereIn('shop_finding_id', $shopFindingIds)->pluck('id')->toArray();
            
        HDR_Segment::whereIn('shop_finding_id', $shopFindingIds)->delete();
        RCS_Segment::whereIn('shop_findings_detail_id', $shopFindingDetailIds)->delete();
        SAS_Segment::whereIn('shop_findings_detail_id', $shopFindingDetailIds)->delete();
        Misc_Segment::whereIn('shop_findings_detail_id', $shopFindingDetailIds)->delete();
        AID_Segment::whereIn('shop_findings_detail_id', $shopFindingDetailIds)->delete();
        EID_Segment::whereIn('shop_findings_detail_id', $shopFindingDetailIds)->delete();
        API_Segment::whereIn('shop_findings_detail_id', $shopFindingDetailIds)->delete();
        SUS_Segment::whereIn('shop_findings_detail_id', $shopFindingDetailIds)->delete();
        RLS_Segment::whereIn('shop_findings_detail_id', $shopFindingDetailIds)->delete();
        LNK_Segment::whereIn('shop_findings_detail_id', $shopFindingDetailIds)->delete();
        ATT_Segment::whereIn('shop_findings_detail_id', $shopFindingDetailIds)->delete();
        SPT_Segment::whereIn('shop_findings_detail_id', $shopFindingDetailIds)->delete();
        
        ShopFindingsDetail::whereIn('shop_finding_id', $shopFindingIds)->delete();
        ShopFinding::whereIn('id', $shopFindingIds)->forceDelete();
    }
    
    /**
     * Delete the piece parts and all related segments.
     *
     * @params array $shopFindingIds
     * @return void
     */
    protected static function deletePiecePartsAndSegments(array $shopFindingIds)
    {
        // Disconnect related activities.
        Activity::whereIn('shop_finding_id', $shopFindingIds)->update(['shop_finding_id' => NULL]);
        
        $piecePartIds = PiecePart::whereIn('shop_finding_id', $shopFindingIds)->pluck('id')->toArray();
        $piecePartDetailIds = PiecePartDetail::withTrashed()->whereIn('piece_part_id', $piecePartIds)->pluck('id')->toArray();
        
        NHS_Segment::withTrashed()->whereIn('piece_part_detail_id', $piecePartDetailIds)->forceDelete();
        RPS_Segment::withTrashed()->whereIn('piece_part_detail_id', $piecePartDetailIds)->forceDelete();
        WPS_Segment::withTrashed()->whereIn('piece_part_detail_id', $piecePartDetailIds)->forceDelete();
        
        PiecePartDetail::whereIn('piece_part_id', $piecePartIds)->forceDelete();
        PiecePart::whereIn('shop_finding_id', $shopFindingIds)->delete();
    }
}
