<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BasicPagesTest extends DuskTestCase
{
    /**
     * Test the login page shows OK.
     *
     * @return void
     */
    public function testLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('login'))
                    ->assertSeeIn('.panel-heading', 'Login');
        });
    }
    
    /**
     * Test the Notifications page shows OK.
     *
     * @return void
     */
    public function testNotificationsIndex()
    {
        $this->browse(function ($first) {
            $first->loginAs($this->user)
                  ->visit(route('notifications.index'))
                  ->assertSeeIn('h1', 'To Do');
        });
    }
    
    /**
     * Test the In Progress page shows OK.
     *
     * @return void
     */
    public function testInProgressIndex()
    {
        $this->browse(function ($first) {
            $first->loginAs($this->user)
                  ->visit(route('datasets.index'))
                  ->assertSeeIn('h1', 'In Progress');
        });
    }
    
    /**
     * Test the Standby page shows OK.
     *
     * @return void
     */
    public function testStandbyIndex()
    {
        $this->browse(function ($first) {
            $first->loginAs($this->user)
                  ->visit(route('standby.index'))
                  ->assertSeeIn('h1', 'On Standby');
        });
    }
    
    /**
     * Test the Deleted page shows OK.
     *
     * @return void
     */
    public function testDeletedIndex()
    {
        $this->browse(function ($first) {
            $first->loginAs($this->user)
                  ->visit(route('deleted.index'))
                  ->assertSeeIn('h1', 'Deleted');
        });
    }
    
    /**
     * Test the Export page shows OK.
     *
     * @return void
     */
    public function testExportIndex()
    {
        $this->browse(function ($first) {
            $first->loginAs($this->dataAdminUser)
                  ->visit(route('reports.export'))
                  ->assertSeeIn('h1', 'Reports to export');
        });
    }
    
    /*
    
    activity.show-my-activity
    user-profile.edit-password
    message.edit
    
    info.customers
    info.locations
    info.cage-codes
    info.aircraft
    info.location-parts
    info.rcs-failure-codes
    info.shop-action-codes
    info.user-roles
    issue-tracker.index
    
    user.index
    activity.index
    boeing.edit
    customer.index
    location.index
    cage-code.index
    part-list.index
    power-bi.index
    
    */
}
