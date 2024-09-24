<?php

namespace Tests;

use Cache;
use App\User;
use App\Location;
use App\UtasCode;
use Carbon\Carbon;
use App\HDR_Segment;
use App\Codes\RcsFailureCode;
use App\PieceParts\PiecePart;
use App\PieceParts\WPS_Segment;
use App\PieceParts\NHS_Segment;
use App\PieceParts\RPS_Segment;
use App\ShopFindings\AID_Segment;
use App\ShopFindings\EID_Segment;
use App\ShopFindings\API_Segment;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\SAS_Segment;
use App\ShopFindings\SUS_Segment;
use App\ShopFindings\RLS_Segment;
use App\ShopFindings\LNK_Segment;
use App\ShopFindings\ATT_Segment;
use App\ShopFindings\SPT_Segment;
use App\ShopFindings\MISC_Segment;
use App\ShopFindings\ShopFinding;
use Illuminate\Support\Facades\DB;
use App\PieceParts\PiecePartDetail;
use Illuminate\Support\Facades\Schema;
use App\ShopFindings\ShopFindingsDetail;
use App\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;
    
    protected $dataAdminUser;
    protected $siteAdminUser;
    protected $adminUser;
    protected $user;
    protected $inactiveUser;
    
    protected static $setUpRun = false;
    
    public function setUp() :void
    {
        parent::setUp();
        
        /*
        |-----------------------------------------------------------------------------------------------
        | If you get a 'Base table or view not found' error:
        |-----------------------------------------------------------------------------------------------
        | 1. Import a local dump of the database.
        | 2. Run composer dump-autoload.
        | 3. Uncomment the two lines below and run a single test.
        |
        */
        
        //\Artisan::call('migrate:fresh');
        //\Artisan::call('migrate:refresh', ['--seed' => true]);
        
        if (!static::$setUpRun) {
            \Artisan::call('migrate:fresh');
            \Artisan::call('migrate:refresh', ['--seed' => true]);
            static::$setUpRun = true;
        }
        
        $this->dataAdminUser = User::dataAdmins()->firstOrFail();
        $this->siteAdminUser = User::siteAdmins()->firstOrFail();
        $this->adminUser = User::admins()->firstOrFail();
        $this->user = User::users()->firstOrFail();
        $this->inactiveUser = User::inactives()->firstOrFail();
    }

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome(), 5000, 10000
        );
    }
    
    /**
     * Get a notification that the specified user can edit.
     *
     * @param \App\User $user
     * @return \App\Notification
     */
    protected function getEditableNotification($user = NULL)
    {
        if (!$user) {
            $user = $this->user;
        }
        
        return Notification::where('plant_code', $user->location->plant_code)
            ->inRandomOrder()
            ->first();
    }
}
