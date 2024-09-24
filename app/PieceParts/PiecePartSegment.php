<?php

namespace App\PieceParts;

use App\Segment;

abstract class PiecePartSegment extends Segment
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['piece_part_detail_id'];
    
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['PiecePartDetail'];
    
    /**
     * Get the piece part record associated with the replaced piece part.
     */
    public function PiecePartDetail()
    {
        return $this->belongsTo('App\PieceParts\PiecePartDetail');
    }
    
    public function getIdentifier()
    {
        return $this->piece_part_detail_id;
    }
    
    public function getPiecePartDetailId()
    {
        return $this->piece_part_detail_id;
    }
    
    public function getShopFindingId()
    {
        return $this->PiecePartDetail->PiecePart->shop_finding_id;
    }
    
    /**
     * Create or update the segment.
     *
     * @param (array) $data
     * @param (string) $piecePartDetailId
     * @return void
     */
    public static abstract function createOrUpdateSegment(array $data, string $piecePartDetailId, $autosave = null);
}