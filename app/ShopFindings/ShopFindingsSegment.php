<?php

namespace App\ShopFindings;

use App\Segment;

abstract class ShopFindingsSegment extends Segment
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['shop_findings_detail_id'];
    
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['shop_findings_detail'];
    
    /**
     * Get the spec 2000 report record associated with the segment.
     */
    public function shop_findings_detail()
    {
        return $this->belongsTo('App\ShopFindings\ShopFindingsDetail');
    }
    
    public function getIdentifier()
    {
        return $this->shop_findings_detail->shop_finding_id;
    }
    
    public function getShopFindingId()
    {
        return $this->shop_findings_detail->shop_finding_id;
    }
    
    /**
     * Create or update the segment.
     *
     * @param (array) $data
     * @param (string) $shopFindingsDetailId
     * @return void
     */
    public static abstract function createOrUpdateSegment(array $data, string $shopFindingsDetailId, $autosave = null);
}