<?php

namespace App\PieceParts;

use App\Notification;
use App\ShopFindings\ShopFinding;
use App\ShopFindings\RCS_Segment;
use App\PieceParts\WPS_Segment;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class PiecePart extends Model
{
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
    
    protected $connection = 'mysql';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['shop_finding_id'];
    
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['ShopFinding'];
    
    /**
     * The piece part warning code when at least one piece part should (but not always) have been recorded as failed.
     *
     * @constant string
     */
    const FAILED = 'Failed';
    
    /**
     * The piece part warning code when no piece parts should (but not always) have been recorded as failed.
     *
     * @constant string
     */
    const NOTFAILED = 'NotFailed';
    
    /**
     * Piece Part warning messages.
     *
     * @var array
     */
    public static $warnings = [
        self::FAILED => "Normally at least one piece part should have a Fail ID of 'Y - Yes'.",
        self::NOTFAILED => "The piece part was either 'Scheduled', 'Modified', or 'Other' so shouldn't have any piece parts with a fail ID of 'Y - Yes'."
    ];
    
    /**
     * Get the spec 2000 report record associated with the piece part.
     */
    public function ShopFinding()
    {
        return $this->belongsTo('App\ShopFindings\ShopFinding');
    }
    
    /**
     * Get the header information record associated with the piece part.
     */
    public function HDR_Segment()
    {
        return $this->hasOne('App\HDR_Segment');
    }
    
    /**
     * Get the piece part detail records associated with the piece part.
     */
    public function PiecePartDetails()
    {
        return $this->hasMany('App\PieceParts\PiecePartDetail');
    }
    
    /**
     * Count how many piece part records there are.
     *
     * @return integer
     */
    public static function countPieceParts($id)
    {
        return count(static::getPiecePartDetails($id));
    }
    
    /**
     * Get partial piece part details array, includes unsaved piece parts.
     *
     * @param (string) $id
     * @return array
     */
    public static function getPiecePartDetails($id)
    {
        /*
        We have to consider that even though the WPS_Segment is always mandatory,
        other piece parts may have been saved without the WPS_Segment being saved yet.
        */
        
        // Get saved piece parts if they exist
        $piecePart = static::WhereHas('PiecePartDetails.WPS_Segment', function($q){
            $q->whereNotNull('piece_part_id');
        })->with('PiecePartDetails.WPS_Segment')->where('shop_finding_id', $id)->first();
        
        $notification = Notification::with('pieceParts')->find($id);
        
        $notificationPieceParts = $notification && $notification->PieceParts ? $notification->PieceParts : [];
        $savedPieceParts = $piecePart && $piecePart->PiecePartDetails ? $piecePart->PiecePartDetails : [];
        $piecePartDetails = [];
        
        if (count($notificationPieceParts)) {
            foreach ($notificationPieceParts as $npp) {
                $piecePartDetails[$npp->id]['id'] = $npp->id;
                $piecePartDetails[$npp->id]['WPS_SFI'] = $npp->get_WPS_SFI();
                $piecePartDetails[$npp->id]['WPS_PPI'] = $npp->get_WPS_PPI();
                $piecePartDetails[$npp->id]['WPS_MPN'] = $npp->get_WPS_MPN();
                $piecePartDetails[$npp->id]['WPS_PDT'] = $npp->get_WPS_PDT();
                $piecePartDetails[$npp->id]['WPS_PFC'] = $npp->get_WPS_PFC();
                $piecePartDetails[$npp->id]['WPS_MRD'] = $npp->get_WPS_MRD();
            }
        }
        
        if (count($savedPieceParts)) {
            foreach ($savedPieceParts as $spp) {
                $piecePartDetails[$spp->id]['id'] = $spp->id;
                $piecePartDetails[$spp->id]['WPS_SFI'] = $piecePart->shop_finding_id;
                $piecePartDetails[$spp->id]['WPS_PPI'] = $spp->id;
                $piecePartDetails[$spp->id]['WPS_MPN'] = $spp->WPS_Segment ? $spp->WPS_Segment->get_WPS_MPN() : '-';
                $piecePartDetails[$spp->id]['WPS_MPN'] = $spp->WPS_Segment ? $spp->WPS_Segment->get_WPS_MPN() : '-';
                $piecePartDetails[$spp->id]['WPS_PDT'] = $spp->WPS_Segment ? $spp->WPS_Segment->get_WPS_PDT() : '-';
                $piecePartDetails[$spp->id]['WPS_PFC'] = $spp->WPS_Segment ? $spp->WPS_Segment->get_WPS_PFC() : '-';
                $piecePartDetails[$spp->id]['WPS_MRD'] = $spp->WPS_Segment ? $spp->WPS_Segment->get_WPS_MRD() : '-';
            }
        }
        
        return $piecePartDetails;
    }
    
    /**
     * Should the fail IDs of the piece parts trigger a warning message.
     *
     * @param (type) $id
     * @return boolean
     */
    public static function showPiecePartFailIdWarning($id)
    {   
        $warningId = self::getPiecePartWarningId($id);
        
        return $warningId ? true : false;
    }
    
    /**
     * Get the warning code for the piece part.
     *
     * @param (type) $id
     * @return boolean
     */
    public static function getPiecePartWarningId($id)
    {
        $RCS_Segment = RCS_Segment::where('SFI', $id)->first();
        
        if (!$RCS_Segment) return false;
        
        if ($RCS_Segment->piecePartHasFailed()) {
            $WPS_Segments = WPS_Segment::where('SFI', $id)->where('PFC', 'Y')->get();
            
            return count($WPS_Segments) ? false : self::FAILED;
        }
        
        if ($RCS_Segment->piecePartHasNotFailed()) {
            $WPS_Segments = WPS_Segment::where('SFI', $id)->where('PFC', 'Y')->get();
            
            return count($WPS_Segments) ? self::NOTFAILED : false;
        }
        
        return false;
    }
    
    /**
     * Get the piece part warning message.
     *
     * @param (type) $id
     * @return string
     */
    public static function getPiecePartWarningMessage($id)
    {
        $warningId = self::getPiecePartWarningId($id);
        
        return $warningId && isset(self::$warnings[$warningId]) ? self::$warnings[$warningId] : '';
    }
}
