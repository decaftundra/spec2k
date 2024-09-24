<?php

namespace Tests\Feature;

use App\Role;
use Carbon\Carbon;
use Tests\TestCase;
use App\Codes\RcsFailureCode;
use App\ShopFindings\RCS_Segment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class RCS_SegmentTest extends TestCase
{
    /**
     * Test the Received LRU form response is 200.
     *
     * @return void
     */
    public function testRCS_SegmentForm()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('received-lru.edit', $notification->get_RCS_SFI()))->assertStatus(200);
    }
    
    /**
     * Test user can't edit or update segment from another location.
     *
     * @return void
     */
    public function testUserCantEditOrUpdateRCS_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->user);
        
        $this->actingAs($this->user)
            ->call('GET', route('received-lru.edit', $notification->get_RCS_SFI()))
            ->assertStatus(403);
            
        $RCS_Segment = factory(RCS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'SFI' => $RCS_Segment->get_RCS_SFI(),
        	'MRD' => $RCS_Segment->get_RCS_MRD(),
        	'MFR' => $RCS_Segment->get_RCS_MFR(),
        	'MPN' => $RCS_Segment->get_RCS_MPN(),
        	'SER' => $RCS_Segment->get_RCS_SER(),
        	'RRC' => $RCS_Segment->get_RCS_RRC(),
        	'FFC' => $RCS_Segment->get_RCS_FFC(),
        	'FFI' => $RCS_Segment->get_RCS_FFI(),
        	'FCR' => $RCS_Segment->get_RCS_FCR(),
        	'FAC' => $RCS_Segment->get_RCS_FAC(),
        	'FBC' => $RCS_Segment->get_RCS_FBC(),
        	'FHS' => $RCS_Segment->get_RCS_FHS(),
        	'MFN' => $RCS_Segment->get_RCS_MFN(),
        	'PNR' => $RCS_Segment->get_RCS_PNR(),
        	'OPN' => $RCS_Segment->get_RCS_OPN(),
        	'USN' => $RCS_Segment->get_RCS_USN(),
        	'RET' => $RCS_Segment->get_RCS_RET(),
        	'CIC' => $RCS_Segment->get_RCS_CIC(),
        	'CPO' => $RCS_Segment->get_RCS_CPO(),
        	'PSN' => $RCS_Segment->get_RCS_PSN(),
        	'WON' => $RCS_Segment->get_RCS_WON(),
        	'MRN' => $RCS_Segment->get_RCS_MRN(),
        	'CTN' => $RCS_Segment->get_RCS_CTN(),
        	'BOX' => $RCS_Segment->get_RCS_BOX(),
        	'ASN' => $RCS_Segment->get_RCS_ASN(),
        	'UCN' => $RCS_Segment->get_RCS_UCN(),
        	'SPL' => $RCS_Segment->get_RCS_SPL(),
        	'UST' => $RCS_Segment->get_RCS_UST(),
        	'PDT' => $RCS_Segment->get_RCS_PDT(),
        	'PML' => $RCS_Segment->get_RCS_PML(),
        	'SFC' => $RCS_Segment->get_RCS_SFC(),
        	'RSI' => $RCS_Segment->get_RCS_RSI(),
        	'RLN' => $RCS_Segment->get_RCS_RLN(),
        	'INT' => $RCS_Segment->get_RCS_INT(),
        	'REM' => $RCS_Segment->get_RCS_REM(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('received-lru.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(403);
    }
    
    /**
     * Test admin can edit or update segment from another location.
     *
     * @return void
     */
    public function testAdminCanEditOrUpdateRCS_SegmentFromOtherLocation()
    {
        $notification = $this->getNotificationNotInUsersLocation($this->adminUser);
        
        $this->actingAs($this->adminUser)
            ->call('GET', route('received-lru.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200);
            
        $RCS_Segment = factory(RCS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'SFI' => $RCS_Segment->get_RCS_SFI(),
        	'MRD' => $RCS_Segment->get_RCS_MRD(),
        	'MFR' => $RCS_Segment->get_RCS_MFR(),
        	'MPN' => $RCS_Segment->get_RCS_MPN(),
        	'SER' => $RCS_Segment->get_RCS_SER(),
        	'RRC' => $RCS_Segment->get_RCS_RRC(),
        	'FFC' => $RCS_Segment->get_RCS_FFC(),
        	'FFI' => $RCS_Segment->get_RCS_FFI(),
        	'FCR' => $RCS_Segment->get_RCS_FCR(),
        	'FAC' => $RCS_Segment->get_RCS_FAC(),
        	'FBC' => $RCS_Segment->get_RCS_FBC(),
        	'FHS' => $RCS_Segment->get_RCS_FHS(),
        	'MFN' => $RCS_Segment->get_RCS_MFN(),
        	'PNR' => $RCS_Segment->get_RCS_PNR(),
        	'OPN' => $RCS_Segment->get_RCS_OPN(),
        	'USN' => $RCS_Segment->get_RCS_USN(),
        	'RET' => $RCS_Segment->get_RCS_RET(),
        	'CIC' => $RCS_Segment->get_RCS_CIC(),
        	'CPO' => $RCS_Segment->get_RCS_CPO(),
        	'PSN' => $RCS_Segment->get_RCS_PSN(),
        	'WON' => $RCS_Segment->get_RCS_WON(),
        	'MRN' => $RCS_Segment->get_RCS_MRN(),
        	'CTN' => $RCS_Segment->get_RCS_CTN(),
        	'BOX' => $RCS_Segment->get_RCS_BOX(),
        	'ASN' => $RCS_Segment->get_RCS_ASN(),
        	'UCN' => $RCS_Segment->get_RCS_UCN(),
        	'SPL' => $RCS_Segment->get_RCS_SPL(),
        	'UST' => $RCS_Segment->get_RCS_UST(),
        	'PDT' => $RCS_Segment->get_RCS_PDT(),
        	'PML' => $RCS_Segment->get_RCS_PML(),
        	'SFC' => $RCS_Segment->get_RCS_SFC(),
        	'RSI' => $RCS_Segment->get_RCS_RSI(),
        	'RLN' => $RCS_Segment->get_RCS_RLN(),
        	'INT' => $RCS_Segment->get_RCS_INT(),
        	'REM' => $RCS_Segment->get_RCS_REM(),
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->call('POST', route('received-lru.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
            
        $this->get($response->headers->get('Location'))
            ->assertSee('Received LRU saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['MRD']);
        unset($attributes['plant_code']);
        
        $this->assertDatabaseHas('RCS_Segments', $attributes);
    }
    
    /**
     * Assert the session has errors if an empty form is submitted.
     *
     * @return void
     */
    public function testInvalidRCSFormSubmit()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('received-lru.update', $notification->get_RCS_SFI()), $attributes);
        
        $response->assertSessionHasErrors(['SFI','MRD','MFR','MPN','SER','RRC','FFC','FFI','FCR','FAC','FBC','FHS']);
    }
    
    /**
     * Test a partial save.
     *
     * @return void
     */
    public function testPartialSave()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $sourceDataArray = [
            "SFI" => $notification->rcsSFI,
            "MRD" => $notification->rcsMRD,
            "MFR" => $notification->rcsMFR,
            "MPN" => $notification->rcsMPN,
            "SER" => $notification->rcsSER,
            "RRC" => $notification->rcsRRC,
            "FFC" => $notification->rcsFFC,
            "FFI" => $notification->rcsFFI,
            "FHS" => $notification->rcsFHS,
            "FCR" => $notification->rcsFCR,
            "FAC" => $notification->rcsFAC,
            "FBC" => $notification->rcsFBC,
            "MFN" => $notification->rcsMFN,
            "PNR" => $notification->rcsPNR,
            "OPN" => $notification->rcsOPN,
            "USN" => $notification->rcsUSN,
            "RET" => $notification->rcsRET,
            "CIC" => $notification->rcsCIC,
            "CPO" => $notification->rcsCPO,
            "PSN" => $notification->rcsPSN,
            "WON" => $notification->rcsWON,
            "MRN" => $notification->rcsMRN,
            "CTN" => $notification->rcsCTN,
            "BOX" => $notification->rcsBOX,
            "ASN" => $notification->rcsASN,
            "UCN" => $notification->rcsUCN,
            "SPL" => $notification->rcsSPL,
            "UST" => $notification->rcsUST,
            "PDT" => $notification->rcsPDT,
            "PML" => $notification->rcsPML,
            "RSI" => $notification->rcsRSI,
            "SFC" => $notification->rcsSFC,
            "RLN" => $notification->rcsRLN,
            "REM" => $notification->rcsREM,
            "INT" => $notification->rcsINT,
        ];
        
        $sourceData = json_encode($sourceDataArray);
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'RRC' => 'U',
            'source_data' => $sourceData
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('received-lru.update', $notification->get_RCS_SFI()), $attributes);
        
        $response->assertSessionHasErrors(['SFI','MRD','MFR','MPN','SER','FFC','FFI','FCR','FAC','FBC','FHS']);
        
        Cache::shouldReceive('get')
            ->once()
            ->with($notification->id . '.' . 'RCS_Segment')
            ->andReturn(['RRC' => 'U']);
            
        Cache::shouldReceive('remember')->andReturn(Role::all());
        
        $this->actingAs($this->user)
            ->get(route('received-lru.edit', $notification->get_RCS_SFI()))
            ->assertSee('THIS SEGMENT HAS BEEN PARTIALLY SAVED.');
    }
    
    /**
     * Test the Received LRU form request validates and segment saves in database.
     *
     * @return void
     */
    public function testEditRCS_Segment()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $RCS_Segment = factory(RCS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'SFI' => $RCS_Segment->get_RCS_SFI(),
        	'MRD' => $RCS_Segment->get_RCS_MRD(),
        	'MFR' => $RCS_Segment->get_RCS_MFR(),
        	'MPN' => $RCS_Segment->get_RCS_MPN(),
        	'SER' => $RCS_Segment->get_RCS_SER(),
        	'RRC' => $RCS_Segment->get_RCS_RRC(),
        	'FFC' => $RCS_Segment->get_RCS_FFC(),
        	'FFI' => $RCS_Segment->get_RCS_FFI(),
        	'FCR' => $RCS_Segment->get_RCS_FCR(),
        	'FAC' => $RCS_Segment->get_RCS_FAC(),
        	'FBC' => $RCS_Segment->get_RCS_FBC(),
        	'FHS' => $RCS_Segment->get_RCS_FHS(),
        	'MFN' => $RCS_Segment->get_RCS_MFN(),
        	'PNR' => $RCS_Segment->get_RCS_PNR(),
        	'OPN' => $RCS_Segment->get_RCS_OPN(),
        	'USN' => $RCS_Segment->get_RCS_USN(),
        	'RET' => $RCS_Segment->get_RCS_RET(),
        	'CIC' => $RCS_Segment->get_RCS_CIC(),
        	'CPO' => $RCS_Segment->get_RCS_CPO(),
        	'PSN' => $RCS_Segment->get_RCS_PSN(),
        	'WON' => $RCS_Segment->get_RCS_WON(),
        	'MRN' => $RCS_Segment->get_RCS_MRN(),
        	'CTN' => $RCS_Segment->get_RCS_CTN(),
        	'BOX' => $RCS_Segment->get_RCS_BOX(),
        	'ASN' => $RCS_Segment->get_RCS_ASN(),
        	'UCN' => $RCS_Segment->get_RCS_UCN(),
        	'SPL' => $RCS_Segment->get_RCS_SPL(),
        	'UST' => $RCS_Segment->get_RCS_UST(),
        	'PDT' => $RCS_Segment->get_RCS_PDT(),
        	'PML' => $RCS_Segment->get_RCS_PML(),
        	'SFC' => $RCS_Segment->get_RCS_SFC(),
        	'RSI' => $RCS_Segment->get_RCS_RSI(),
        	'RLN' => $RCS_Segment->get_RCS_RLN(),
        	'INT' => $RCS_Segment->get_RCS_INT(),
        	'REM' => $RCS_Segment->get_RCS_REM(),
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('received-lru.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
            
        $this->get($response->headers->get('Location'))->assertSee('Received LRU saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['MRD']);
        unset($attributes['plant_code']);
        
        $this->assertDatabaseHas('RCS_Segments', $attributes);
    }
    
    /**
     * Test the Collins Part Received LRU form request validates and segment saves in database.
     *
     * @return void
     */
    public function testEditCollinsRCS_Segment()
    {
        $notification = $this->getEditableCollinsNotification($this->user);
        
        $RCS_Segment = factory(RCS_Segment::class)->make();
        
        $attributes = [
            'rcsSFI' => $notification->get_RCS_SFI(),
            'plant_code' => $notification->plant_code,
            'SFI' => $RCS_Segment->get_RCS_SFI(),
        	'MRD' => $RCS_Segment->get_RCS_MRD(),
        	'MFR' => $RCS_Segment->get_RCS_MFR(),
        	'MPN' => $RCS_Segment->get_RCS_MPN(),
        	'SER' => $RCS_Segment->get_RCS_SER(),
        	'RRC' => $RCS_Segment->get_RCS_RRC(),
        	'FFC' => $RCS_Segment->get_RCS_FFC(),
        	'FFI' => $RCS_Segment->get_RCS_FFI(),
        	'FCR' => $RCS_Segment->get_RCS_FCR(),
        	'FAC' => $RCS_Segment->get_RCS_FAC(),
        	'FBC' => $RCS_Segment->get_RCS_FBC(),
        	'FHS' => $RCS_Segment->get_RCS_FHS(),
        	'MFN' => $RCS_Segment->get_RCS_MFN(),
        	'PNR' => $RCS_Segment->get_RCS_PNR(),
        	'OPN' => $RCS_Segment->get_RCS_OPN(),
        	'USN' => $RCS_Segment->get_RCS_USN(),
        	'RET' => $RCS_Segment->get_RCS_RET(),
        	'CIC' => $RCS_Segment->get_RCS_CIC(),
        	'CPO' => $RCS_Segment->get_RCS_CPO(),
        	'PSN' => $RCS_Segment->get_RCS_PSN(),
        	'WON' => $RCS_Segment->get_RCS_WON(),
        	'MRN' => $RCS_Segment->get_RCS_MRN(),
        	'CTN' => $RCS_Segment->get_RCS_CTN(),
        	'BOX' => $RCS_Segment->get_RCS_BOX(),
        	'ASN' => $RCS_Segment->get_RCS_ASN(),
        	'UCN' => $RCS_Segment->get_RCS_UCN(),
        	'SPL' => $RCS_Segment->get_RCS_SPL(),
        	'UST' => $RCS_Segment->get_RCS_UST(),
        	'PDT' => $RCS_Segment->get_RCS_PDT(),
        	'PML' => $RCS_Segment->get_RCS_PML(),
        	'SFC' => $RCS_Segment->get_RCS_SFC(),
        	'RSI' => $RCS_Segment->get_RCS_RSI(),
        	'RLN' => $RCS_Segment->get_RCS_RLN(),
        	'INT' => $RCS_Segment->get_RCS_INT(),
        	//'REM' => $RCS_Segment->get_RCS_REM(), There is no REM field in the Collins form.
        ];
        
        $response = $this->actingAs($this->user)
            ->call('POST', route('received-lru.update', $notification->get_RCS_SFI()), $attributes)
            ->assertStatus(302);
            
        $errors = session('errors');
        
        if (!empty($errors)) {
            mydd($errors);
            var_dump($attributes);
        }
            
        $this->get($response->headers->get('Location'))->assertSee('Received LRU saved successfully!');
        
        unset($attributes['rcsSFI']); // Not recorded in DB.
        unset($attributes['MRD']);
        unset($attributes['plant_code']);
        
        $this->assertDatabaseHas('RCS_Segments', $attributes);
    }
    
    /**
     * Test that all the fault code combinations in the db pass validation.
     *
     * @return void
     */
    public function testAllFaultCodeCombinationsAreValid()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $faultCodes = RcsFailureCode::get()->toArray();
        
        foreach ($faultCodes as $faultCode) {
            unset($faultCode['id']);
            unset($faultCode['updated_at']);
            unset($faultCode['created_at']);
            
            $RCS_Segment = factory(RCS_Segment::class)->make($faultCode);
            
            $attributes = [
                'rcsSFI' => $notification->get_RCS_SFI(),
                'plant_code' => $notification->plant_code,
                'SFI' => $RCS_Segment->get_RCS_SFI(),
            	'MRD' => $RCS_Segment->get_RCS_MRD(),
            	'MFR' => $RCS_Segment->get_RCS_MFR(),
            	'MPN' => $RCS_Segment->get_RCS_MPN(),
            	'SER' => $RCS_Segment->get_RCS_SER(),
            	'RRC' => $RCS_Segment->get_RCS_RRC(),
            	'FFC' => $RCS_Segment->get_RCS_FFC(),
            	'FFI' => $RCS_Segment->get_RCS_FFI(),
            	'FCR' => $RCS_Segment->get_RCS_FCR(),
            	'FAC' => $RCS_Segment->get_RCS_FAC(),
            	'FBC' => $RCS_Segment->get_RCS_FBC(),
            	'FHS' => $RCS_Segment->get_RCS_FHS(),
            	'MFN' => $RCS_Segment->get_RCS_MFN(),
            	'PNR' => $RCS_Segment->get_RCS_PNR(),
            	'OPN' => $RCS_Segment->get_RCS_OPN(),
            	'USN' => $RCS_Segment->get_RCS_USN(),
            	'RET' => $RCS_Segment->get_RCS_RET(),
            	'CIC' => $RCS_Segment->get_RCS_CIC(),
            	'CPO' => $RCS_Segment->get_RCS_CPO(),
            	'PSN' => $RCS_Segment->get_RCS_PSN(),
            	'WON' => $RCS_Segment->get_RCS_WON(),
            	'MRN' => $RCS_Segment->get_RCS_MRN(),
            	'CTN' => $RCS_Segment->get_RCS_CTN(),
            	'BOX' => $RCS_Segment->get_RCS_BOX(),
            	'ASN' => $RCS_Segment->get_RCS_ASN(),
            	'UCN' => $RCS_Segment->get_RCS_UCN(),
            	'SPL' => $RCS_Segment->get_RCS_SPL(),
            	'UST' => $RCS_Segment->get_RCS_UST(),
            	'PDT' => $RCS_Segment->get_RCS_PDT(),
            	'PML' => $RCS_Segment->get_RCS_PML(),
            	'SFC' => $RCS_Segment->get_RCS_SFC(),
            	'RSI' => $RCS_Segment->get_RCS_RSI(),
            	'RLN' => $RCS_Segment->get_RCS_RLN(),
            	'INT' => $RCS_Segment->get_RCS_INT(),
            	'REM' => $RCS_Segment->get_RCS_REM(),
            ];
            
            $response = $this->actingAs($this->user)
                ->call('POST', route('received-lru.update', $notification->get_RCS_SFI()), $attributes);
                
            $errors = session('errors');
            
            if (!empty($errors)) {
                mydd($errors);
                mydd($attributes);
            }
            
            $this->assertEmpty($errors);
            
            $response->assertSessionMissing('errors');
            
            $test = $this->get($response->headers->get('Location'))->assertSee('Received LRU saved successfully!');
        }
    }
    
    /**
     * Test that the segment can be deleted.
     *
     * @return void
     */
    public function testDeleteRCS_Segment()
    {
        $this->createSingleShopFindingAndPiecePartsWithAllSegments(1, $this->adminUser);
        
        $segment = RCS_Segment::inRandomOrder()->first();
        
        $segmentId = $segment->id;
        
        $this->actingAs($this->adminUser)
            ->ajaxPost(route('received-lru.destroy', $segmentId))
            ->assertStatus(200);
            
        $this->assertDatabaseMissing('RCS_Segments', ['id' => $segmentId]);
    }
}
