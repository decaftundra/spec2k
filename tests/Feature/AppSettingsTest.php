<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppSettingsTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShowAllFormFieldsSetting()
    {
        $this->actingAs($this->user)->ajaxPost(route('show_all_fields', ['setting' => 1]));
        
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('airframe-information.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200)
            ->assertSessionHas('show_all_fields');
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testHideFormFieldsSetting()
    {
        $this->actingAs($this->user)->ajaxPost(route('show_all_fields', [0]));
        
        $notification = $this->getEditableNotification($this->user);
        
        $this->actingAs($this->user)->call('GET', route('airframe-information.edit', $notification->get_RCS_SFI()))
            ->assertStatus(200)
            ->assertSessionMissing('show_all_fields');
    }
}