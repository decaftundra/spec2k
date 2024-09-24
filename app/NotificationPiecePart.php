<?php

namespace App;

use App\CageCode;
use App\Interfaces\SegmentInterface;
use App\Interfaces\WPS_SegmentInterface;
use App\Interfaces\NHS_SegmentInterface;
use App\Interfaces\RPS_SegmentInterface;
use App\Notification;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Artisan;

class NotificationPiecePart extends Model implements SegmentInterface,
                                                     WPS_SegmentInterface,
                                                     NHS_SegmentInterface,
                                                     RPS_SegmentInterface
{
    use SoftDeletes;
    
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    protected $table = 'notification_piece_parts';
    
    public $incrementing = false;
    
    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';
    
    protected $guarded = [];
    
    protected $dates = [
        'wpsMRD',
        'deleted_at'
    ];
    
    public function Notification()
    {
        return $this->belongsTo('App\Notification');
    }
    
    /**
     * Is the piece part a reversal.
     *
     * @return boolean
     */
    public function isReversal()
    {
        return !is_null($this->reversal_id);
    }
    
    /* PIECE PART SEGMENTS */
    
    /**
     |--------------------------------
     | WORKED PIECE PART FUNCTIONS
     |--------------------------------
     */
     
     /**
     * Get the Shop Finding Record Identifier.
     *
     * @return string
     */
    public function get_WPS_SFI()
    {
        return (string) $this->wpsSFI;
    }
    
    /**
     * Get the Piece Part Record Identifier.
     *
     * @return string
     */
    public function get_WPS_PPI()
    {
        return (string) $this->wpsPPI;
    }
    
    /**
     * Get the Primary Piece Part Failure Indicator.
     *
     * @return string
     */
    public function get_WPS_PFC()
    {
        return (string) $this->wpsPFC;
    }
    
    /**
     * Get the Failed Piece Part Vendor Code.
     *
     * @return string
     */
    public function get_WPS_MFR()
    {
        // Quick fix to correct bad SAP data missing leading zero.
        if ($this->wpsMFR == '5167') {
            return (string) '05167';
        }
        
        if (in_array((string) $this->wpsMFR, CageCode::getPermittedValues())) {
            return (string) $this->wpsMFR;
        }
        
        return NULL;
    }
    
    /**
     * Get the Failed Piece Part Vendor Name.
     *
     * @return string
     */
    public function get_WPS_MFN()
    {
        return (string) $this->wpsMFN;
    }
    
    /**
     * Get the Failed Piece Part Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_WPS_MPN()
    {
        return (string) $this->wpsMPN;
    }
    
    /**
     * Get the Failed Piece Part Serial Number.
     *
     * @return string
     */
    public function get_WPS_SER()
    {
        return (string) $this->wpsSER;
    }
    
    /**
     * Get the Piece Part Failure Description.
     *
     * @return string
     */
    public function get_WPS_FDE()
    {
        return (string) $this->wpsFDE;
    }
    
    /**
     * Get the Vendor Piece Part Number.
     *
     * @return string
     */
    public function get_WPS_PNR()
    {
        return (string) $this->wpsPNR;
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_WPS_OPN()
    {
        return (string) $this->wpsOPN;
    }
    
    /**
     * Get the Piece Part Universal Serial Number.
     *
     * @return string
     */
    public function get_WPS_USN()
    {
        return (string) $this->wpsUSN;
    }
    
    /**
     * Get the Failed Piece Part Description.
     *
     * @return string
     */
    public function get_WPS_PDT()
    {
        return (string) $this->wpsPDT;
    }
    
    /**
     * Get the Piece Part Reference Designator Symbol.
     *
     * @return string
     */
    public function get_WPS_GEL()
    {
        return (string) $this->wpsGEL;
    }
    
    /**
     * Get the Received Date.
     *
     * @return date
     */
    public function get_WPS_MRD()
    {
        return $this->wpsMRD ? $this->wpsMRD->format('d/m/Y') : NULL;
    }
    
    /**
     * Get the Operator Piece Part Number.
     *
     * @return string
     */
    public function get_WPS_ASN()
    {
        return (string) $this->wpsASN;
    }
    
    /**
     * Get the Operator Piece Part Serial Number.
     *
     * @return string
     */
    public function get_WPS_UCN()
    {
        return (string) $this->wpsUCN;
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_WPS_SPL()
    {
        return (string) $this->wpsSPL;
    }
    
    /**
     * Get the Piece Part Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_WPS_UST()
    {
        return (string) $this->wpsUST;
    }
     
    /**
     |--------------------------------
     | NEXT HIGHER ASSEMBLY FUNCTIONS
     |--------------------------------
     */
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Manufacturer Code.
     *
     * @return string
     */
    public function get_NHS_MFR()
    {
        // Quick fix to correct bad SAP data missing leading zero.
        if ($this->nhsMFR == '5167') {
            return (string) '05167';
        }
        
        if (in_array((string) $this->nhsMFR, CageCode::getPermittedValues())) {
            return (string) $this->nhsMFR;
        }
        
        return NULL;
    }
    
    /**
     * Get the Next Higher Assembly Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_NHS_MPN()
    {
        return (string) $this->nhsMPN;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Serial Number.
     *
     * @return string
     */
    public function get_NHS_SER()
    {
        return (string) $this->nhsSER;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Manufacturer Name.
     *
     * @return string
     */
    public function get_NHS_MFN()
    {
        return (string) $this->nhsMFN;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Number.
     *
     * @return string
     */
    public function get_NHS_PNR()
    {
        return (string) $this->nhsPNR;
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_NHS_OPN()
    {
        return (string) $this->nhsOPN;
    }
    
    /**
     * Get the Failed Piece Part Universal Serial Number.
     *
     * @return string
     */
    public function get_NHS_USN()
    {
        return (string) $this->nhsUSN;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Part Name.
     *
     * @return string
     */
    public function get_NHS_PDT()
    {
        return (string) $this->nhsPDT;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Operator Part Number.
     *
     * @return string
     */
    public function get_NHS_ASN()
    {
        return (string) $this->nhsASN;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly Operator Serial Number.
     *
     * @return string
     */
    public function get_NHS_UCN()
    {
        return (string) $this->nhsUCN;
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_NHS_SPL()
    {
        return (string) $this->nhsSPL;
    }
    
    /**
     * Get the Failed Piece Part NHA Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_NHS_UST()
    {
        return (string) $this->nhsUST;
    }
    
    /**
     * Get the Failed Piece Part Next Higher Assembly NHA Part Number.
     *
     * @return string
     */
    public function get_NHS_NPN()
    {
        return (string) $this->nhsNPN;
    }
     
    /**
     |--------------------------------
     | REPLACED PIECE PART FUNCTIONS
     |--------------------------------
     */
    
    /**
     * Get the Replaced Piece Part Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RPS_MPN()
    {
        return (string) $this->rpsMPN;
    }
    
    /**
     * Get the Replaced Piece Part Vendor Code.
     *
     * @return string
     */
    public function get_RPS_MFR()
    {
        // Quick fix to correct bad SAP data missing leading zero.
        if ($this->rpsMFR == '5167') {
            return (string) '05167';
        }
        
        if (in_array((string) $this->rpsMFR, CageCode::getPermittedValues())) {
            return (string) $this->rpsMFR;
        }
        
        return NULL;
    }
    
    /**
     * Get the Replaced Piece Part Vendor Name.
     *
     * @return string
     */
    public function get_RPS_MFN()
    {
        return (string) $this->rpsMFN;
    }
    
    /**
     * Get the Replaced Vendor Piece Part Serial Number.
     *
     * @return string
     */
    public function get_RPS_SER()
    {
        return (string) $this->rpsSER;
    }
    
    /**
     * Get the Replaced Vendor Piece Part Number.
     *
     * @return string
     */
    public function get_RPS_PNR()
    {
        return (string) $this->rpsPNR;
    }
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RPS_OPN()
    {
        return (string) $this->rpsOPN;
    }
    
    /**
     * Get the Replaced Piece Part Universal Serial Number.
     *
     * @return string
     */
    public function get_RPS_USN()
    {
        return (string) $this->rpsUSN;
    }
    
    /**
     * Get the Replaced Operator Piece Part Number.
     *
     * @return string
     */
    public function get_RPS_ASN()
    {
        return (string) $this->rpsASN;
    }
    
    /**
     * Get the Replaced Operator Piece Part Serial Number.
     *
     * @return string
     */
    public function get_RPS_UCN()
    {
        return (string) $this->rpsUCN;
    }
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RPS_SPL()
    {
        return (string) $this->rpsSPL;
    }
    
    /**
     * Get the Replaced Piece Part Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RPS_UST()
    {
        return (string) $this->rpsUST;
    }
    
    /**
     * Get the Replaced Vendor Piece Part Description.
     *
     * @return string
     */
    public function get_RPS_PDT()
    {
        return (string) $this->rpsPDT;
    }
    
    /**
     * Update or create piece parts from middleware api data.
     *
     * @params array $data
     * @return void
     */
    public static function updateOrCreateFromMiddleware(array $data)
    {
        $ppReversalIds = [];
        
        $notification = Notification::withTrashed()->find($data[0]->wpsSFI);
        
        // Only save if notification exists.
        if ($notification) {
        
            foreach ($data as $value) {
                    
                // If reversal, store id in an array.
                if (isset($value->reversal_id) && !empty($value->reversal_id)) {
                    $ppReversalIds[] = $value->wpsPPI;
                }
                
                // Some of the piece parts may already have been reversed (deleted), so we need to include them in the query.
                $piecePart = self::withTrashed()->firstOrCreate([
                    'id' => $value->wpsPPI,
                    'notification_id' => $value->wpsSFI,
                    'wpsSFI' => $value->wpsSFI,
                    'wpsPPI' => $value->wpsPPI
                ]);
                
                // Fetch saved piece part from DB so we can get all attribute keys. This may have been reversed so we need to include soft-deleted records.
                $piecePart = NotificationPiecePart::withTrashed()->find($value->wpsPPI);
                
                $attributeKeys = array_keys($piecePart->getAttributes());
                
                foreach ($value as $key => $val) {
                    
                    if ($key && in_array($key, $attributeKeys) && !empty($val)) {
                        // Format the dates correctly.
                        if (in_array($key, $piecePart->getDates())) {
                            if ((stristr($val, '-') === false) && intval($val)) {
                                $piecePart->{$key} = Carbon::createFromFormat('Ymd', $val);
                            } else if ($val != '0000-00-00') {
                                $piecePart->{$key} = Carbon::createFromFormat('Y-m-d', $val);
                            }
                        } else {
                            $piecePart->{$key} = str_replace(['\r\n', '\n', '\r'], "\n", $val); // Preserve new lines.
                        }
                    }
                }
                
                $piecePart->save();
            }
            
            if (!empty($ppReversalIds)) {
                // Sync piece part reversals...
                Artisan::call('spec2kapp:sync_reversals', ['piecePartIds' => $ppReversalIds]);
            }
        }
    }
}
