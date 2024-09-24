<?php

namespace App\ShopFindings;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class ShopFindingsDetail extends Model
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
     * Get the shop finding that owns the shop findings detail.
     */
    public function ShopFinding()
    {
        return $this->belongsTo('App\ShopFindings\ShopFinding');
    }
    
    /**
     * Get the accumulated time text record associated with the spec 2000 report.
     */
    public function ATT_segment()
    {
        return $this->hasOne('App\ShopFindings\ATT_Segment');
    }
    
    /**
     * Get the airframe information record associated with the spec 2000 report.
     */
    public function AID_Segment()
    {
        return $this->hasOne('App\ShopFindings\AID_Segment');
    }
    
    /**
     * Get the apu information record associated with the spec 2000 report.
     */
    public function API_Segment()
    {
        return $this->hasOne('App\ShopFindings\API_Segment');
    }
    
    /**
     * Get the engine information record associated with the spec 2000 report.
     */
    public function EID_Segment()
    {
        return $this->hasOne('App\ShopFindings\EID_Segment');
    }
    
    /**
     * Get the linking field record associated with the spec 2000 report.
     */
    public function LNK_Segment()
    {
        return $this->hasOne('App\ShopFindings\LNK_Segment');
    }
    
    /**
     * Get the misc segment record associated with the spec 2000 report.
     */
    public function Misc_Segment()
    {
        return $this->hasOne('App\ShopFindings\Misc_Segment');
    }
    
    /**
     * Get the next higher assembly record associated with the spec 2000 report.
     */
    public function NHS_Segments()
    {
        return $this->hasManyThrough('App\PieceParts\NHS_Segment', 'App\PieceParts\PiecePart');
    }
    
    /**
     * Get the next piece part records associated with the spec 2000 report.
     */
    public function PiecePart()
    {
        return $this->hasOne('App\PieceParts\PiecePart');
    }
    
    /**
     * Get the received lru record associated with the spec 2000 report.
     */
    public function RCS_Segment()
    {
        return $this->hasOne('App\ShopFindings\RCS_Segment');
    }
    
    /**
     * Get the removed lru record associated with the spec 2000 report.
     */
    public function RLS_Segment()
    {
        return $this->hasOne('App\ShopFindings\RLS_Segment');
    }
    
    /**
     * Get the replaced piece part record associated with the spec 2000 report.
     */
    public function RPS_Segments()
    {
        return $this->hasManyThrough('App\PieceParts\RPS_Segment', 'App\PieceParts\PiecePart');
    }
    
    /**
     * Get the shipped lru record associated with the spec 2000 report.
     */
    public function SUS_Segment()
    {
        return $this->hasOne('App\ShopFindings\SUS_Segment');
    }
    
    /**
     * Get the shop action detail record associated with the spec 2000 report.
     */
    public function SAS_Segment()
    {
        return $this->hasOne('App\ShopFindings\SAS_Segment');
    }
    
    /**
     * Get the shop processing time record associated with the spec 2000 report.
     */
    public function SPT_Segment()
    {
        return $this->hasOne('App\ShopFindings\SPT_Segment');
    }
    
    /**
     * Get the worked piece part record associated with the spec 2000 report.
     */
    public function WPS_Segments()
    {
        return $this->hasManyThrough('App\PieceParts\WPS_Segment', 'App\PieceParts\PiecePart');
    }
}
