<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    /**
     * Test for a 200 response from the notifications index.
     *
     * @return void
     */
    public function testNotificationsIndex()
    {
        $this->actingAs($this->user)->call('GET', route('notifications.index'))->assertStatus(200);
    }
    
    // test piece part count.
    
    // test if it is a utas part.
    
    /**
     * Test the notifications search filter with a full notification id.
     *
     * @return void
     */
    public function testSearchFilter()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $url = route('notifications.index') . '?roc=All&search=' . (string) $notification->get_RCS_SFI();
        
        $this->actingAs($this->user)
            ->call('GET', $url)
            ->assertStatus(200)
            ->assertSee('Displaying 1 to 1 of 1 notifications.')
            ->assertSee($notification->get_HDR_CHG())
            ->assertSee($notification->get_HDR_ROC());
    }
    
    /**
     * Test the notifications search filter with a partial notification id.
     *
     * @return void
     */
    public function testPartialIdSearch()
    {
        $notification = $this->getEditableNotification($this->user);
        
        $partialId = substr($notification->id, -5);
        
        $total = Notification::whereNested(function($q) use ($partialId) {
            $q->where('rcsSFI', 'LIKE', (string) "%$partialId%")
                ->orWhere('rcsMPN', 'LIKE', (string) "%$partialId%")
                ->orWhere('rcsSER', 'LIKE', (string) "%$partialId%");
            })
            ->where('plant_code', $this->user->location->plant_code)
            ->orderBy('rcsSFI', 'asc')->count();
        
        $perPage = 20;
        
        if ($total < $perPage) {
            $perPage = $total;
        }
        
        $url = route('notifications.index') . '?roc=All&search=' . $partialId;
        
        $this->actingAs($this->user)
            ->call('GET', $url)
            ->assertStatus(200)
            ->assertSee("Displaying 1 to $perPage of $total notifications.")
            ->assertSee($partialId)
            ->assertSee($notification->get_HDR_CHG())
            ->assertSee($notification->get_HDR_ROC());
    }
    
    /**
     * Test the notifications search filter with a partial part number.
     *
     * @return void
     */
    public function testPartialPartNumberSearch()
    {
        $notifications = Notification::where('plant_code', $this->user->location->plant_code)->get();
        
        $notification = collect($notifications)->filter(function($value, $key){
            return $key == 'rcsMPN' && (strlen($value) > 7);
        })->random(); // Get a random Notification from collection.
        
        $partialPartNumber = substr($notification->rcsMPN, -5);
        
        $total = Notification::whereNested(function($q) use ($partialPartNumber) {
            $q->where('rcsSFI', 'LIKE', (string) "%$partialPartNumber%")
                ->orWhere('rcsMPN', 'LIKE', (string) "%$partialPartNumber%")
                ->orWhere('rcsSER', 'LIKE', (string) "%$partialPartNumber%");
            })
            ->where('plant_code', $this->user->location->plant_code)
            ->orderBy('rcsSFI', 'asc')->count();
        
        $perPage = 20;
        
        if ($total < $perPage) {
            $perPage = $total;
        }
        
        $url = route('notifications.index') . '?roc=All&search=' . $partialPartNumber;
        
        $this->actingAs($this->user)
            ->call('GET', $url)
            ->assertStatus(200)
            ->assertSee("Displaying 1 to $perPage of $total notifications.")
            ->assertSee($partialPartNumber);
    }
    
    /**
     * Test the notifications search filter with a partial part number.
     *
     * @return void
     */
    public function testPartialSerialNumberSearch()
    {
        $notifications = Notification::where('plant_code', $this->user->location->plant_code)->get();
        
        $notification = collect($notifications)->filter(function($value, $key){
            return $key == 'rcsSFI' && (strlen($value) > 7);
        })->random(); // Get a random Notification from collection.
        
        $partialSerialNo = substr($notification->rcsSFI, -5);
        
        $total = Notification::whereNested(function($q) use ($partialSerialNo) {
            $q->where('rcsSFI', 'LIKE', (string) "%$partialSerialNo%")
                ->orWhere('rcsMPN', 'LIKE', (string) "%$partialSerialNo%")
                ->orWhere('rcsSER', 'LIKE', (string) "%$partialSerialNo%");
            })
            ->where('plant_code', $this->user->location->plant_code)
            ->orderBy('rcsSFI', 'asc')->count();
        
        $perPage = 20;
        
        if ($total < $perPage) {
            $perPage = $total;
        }
        
        $url = route('notifications.index') . '?roc=All&search=' . $partialSerialNo;
        
        $this->actingAs($this->user)
            ->call('GET', $url)
            ->assertStatus(200)
            ->assertSee("Displaying 1 to $perPage of $total notifications.")
            ->assertSee($partialSerialNo);
    }
    
    // test location search
    
    // test date range search
}
