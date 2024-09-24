<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| LOGIN/LOGOUT ROUTES
|--------------------------------------------------------------------------
*/

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login')->name('postLogin');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

/*
|--------------------------------------------------------------------------
| PASSWORD RESET ROUTES
|--------------------------------------------------------------------------
*/

Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.postReset');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function(){

    Route::get('/', 'HomeController@index'); // Redirects to notifications.index if authenticated.
    
    /*
    |--------------------------------------------------------------------------
    | User Profile
    |--------------------------------------------------------------------------
    */
    
    //Route::get('/user-profile/edit', 'UserProfileController@edit')->name('user-profile.edit');
    //Route::put('/user-profile/edit', 'UserProfileController@update')->name('user-profile.update');
    Route::get('/user-profile/edit-password', 'UserProfileController@editPassword')->name('user-profile.edit-password');
    Route::put('/user-profile/edit-password', 'UserProfileController@updatePassword')->name('user-profile.update-password');
    Route::get('/user-profile/your-activities', 'ActivitiesController@showMyActivity')->name('activity.show-my-activity');
    Route::get('/user-profile/messages/edit', 'MessageController@edit')->name('message.edit');
    Route::put('/user-profile/messages/edit', 'MessageController@update')->name('message.update');

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    
    Route::get('notifications', 'NotificationsController@index')->name('notifications.index')->middleware('get_cached_search');
    
    /*
    |--------------------------------------------------------------------------
    | Datasets (in progress)
    |--------------------------------------------------------------------------
    */
    
    Route::get('datasets', 'DatasetController@index')->name('datasets.index')->middleware('get_cached_search');
    
    /*
    |--------------------------------------------------------------------------
    | Datasets (on standby)
    |--------------------------------------------------------------------------
    */
    
    Route::get('status/on-standby', 'StandbyOrDeletedController@standbyIndex')->name('standby.index')->middleware('get_cached_search');
    
    /*
    |--------------------------------------------------------------------------
    | Datasets & Notifications (deleted)
    |--------------------------------------------------------------------------
    */
    
    Route::get('status/deleted', 'StandbyOrDeletedController@deletedIndex')->name('deleted.index')->middleware('get_cached_search');
    
    /*
    |--------------------------------------------------------------------------
    | HDR_Segment - Header
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/header', 'HeaderController@edit')->name('header.edit')
        ->middleware('fetch-notification'); //->middleware('get_cached_seg:HDR_Segment');
    
    Route::post('dataset/edit/{notification}/header', 'HeaderController@update')->name('header.update');
    Route::post('dataset/delete/header/{id}', 'HeaderController@destroy')->name('header.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | AID_Segment - Airframe Information
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/airframe-information', 'AirframeInformationController@edit')
        ->name('airframe-information.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:AID_Segment');
        
    Route::post('dataset/edit/{notification}/airframe-information', 'AirframeInformationController@update')->name('airframe-information.update');
    Route::post('dataset/delete/airframe-information/{id}', 'AirframeInformationController@destroy')->name('airframe-information.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | EID_Segment - Engine Information
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/engine-information', 'EngineInformationController@edit')
        ->name('engine-information.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:EID_Segment');
        
    Route::post('dataset/edit/{notification}/engine-information', 'EngineInformationController@update')->name('engine-information.update');
    Route::post('dataset/delete/engine-information/{id}', 'EngineInformationController@destroy')->name('engine-information.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | API_Segment - APU Information
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/apu-information', 'ApuInformationController@edit')
        ->name('apu-information.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:API_Segment');
        
    Route::post('dataset/edit/{notification}/apu-information', 'ApuInformationController@update')->name('apu-information.update');
    Route::post('dataset/delete/apu-information/{id}', 'ApuInformationController@destroy')->name('apu-information.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | RCS_Segment - Received LRU
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/received-lru', 'ReceivedLruController@edit')
        ->name('received-lru.edit')
        ->middleware('get_cached_seg:RCS_Segment')
        ->middleware('fetch-notification');
        
    Route::post('dataset/edit/{notification}/received-lru', 'ReceivedLruController@update')->name('received-lru.update');
    Route::post('dataset/delete/received-lru/{id}', 'ReceivedLruController@destroy')->name('received-lru.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | SAS_Segment - Shop Action Details
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/shop-action-details', 'ShopActionDetailController@edit')
        ->name('shop-action-details.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:SAS_Segment');
        
    Route::post('dataset/edit/{notification}/shop-action-details', 'ShopActionDetailController@update')->name('shop-action-details.update');
    Route::post('dataset/delete/shop-action-details/{id}', 'ShopActionDetailController@destroy')->name('shop-action-details.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | SUS_Segment - Shipped LRU
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/shipped-lru', 'ShippedLruController@edit')
        ->name('shipped-lru.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:SUS_Segment');
        
    Route::post('dataset/edit/{notification}/shipped-lru', 'ShippedLruController@update')->name('shipped-lru.update');
    Route::post('dataset/delete/shipped-lru/{id}', 'ShippedLruController@destroy')->name('shipped-lru.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | RLS_Segment - Removed LRU
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/removed-lru', 'RemovedLruController@edit')
        ->name('removed-lru.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:RLS_Segment');
        
    Route::post('dataset/edit/{notification}/removed-lru', 'RemovedLruController@update')->name('removed-lru.update');
    Route::post('dataset/delete/removed-lru/{id}', 'RemovedLruController@destroy')->name('removed-lru.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | LNK_Segment - Linking Fields
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/linking-field', 'LinkingFieldController@edit')
        ->name('linking-field.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:LNK_Segment');
        
    Route::post('dataset/edit/{notification}/linking-field', 'LinkingFieldController@update')->name('linking-field.update');
    Route::post('dataset/delete/linking-field/{id}', 'LinkingFieldController@destroy')->name('linking-field.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | ATT_Segment - Accumulated Time Text
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/accumulated-time-text', 'AccumulatedTimeTextController@edit')
        ->name('accumulated-time-text.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:ATT_Segment');
        
    Route::post('dataset/edit/{notification}/accumulated-time-text', 'AccumulatedTimeTextController@update')->name('accumulated-time-text.update');
    
    Route::post('dataset/delete/accumulated-time-text/{id}', 'AccumulatedTimeTextController@destroy')->name('accumulated-time-text.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | SPT_Segment - Shop Processing Time
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/shop-processing-time', 'ShopProcessingTimeController@edit')
        ->name('shop-processing-time.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:SPT_Segment');
        
    Route::post('dataset/edit/{notification}/shop-processing-time', 'ShopProcessingTimeController@update')->name('shop-processing-time.update');
    Route::post('dataset/delete/shop-processing-time/{id}', 'ShopProcessingTimeController@destroy')->name('shop-processing-time.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | Misc_Segment
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/misc-segment', 'Misc_SegmentController@edit')
        ->name('misc-segment.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:Misc_Segment');
        
    Route::post('dataset/edit/{notification}/misc-segment', 'Misc_SegmentController@update')->name('misc-segment.update');
    Route::post('dataset/delete/misc-segment/{id}', 'Misc_SegmentController@destroy')->name('misc-segment.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | Piece Parts
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/edit/{notification}/piece-parts', 'PiecePartController@index')
        ->name('piece-parts.index')
        ->middleware('fetch-notification');
        
    Route::post('dataset/edit/{notification}/piece-parts', 'PiecePartController@update')->name('piece-parts.update');
    
    // Delete all here???
    
    /*
    |--------------------------------------------------------------------------
    | WPS_Segment - Worked Piece Part
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/{notification}/piece-parts/edit/worked-piece-part/{piece_part_detail_id}', 'WPS_SegmentController@edit')
        ->name('worked-piece-part.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:WPS_Segment');
    
    Route::post('dataset/{notification}/piece-parts/edit/worked-piece-part/{piece_part_detail_id}', 'WPS_SegmentController@update')
        ->name('worked-piece-part.update');
        
    Route::post('dataset/piece-parts/delete/worked-piece-part/{id}', 'WPS_SegmentController@destroy')->name('worked-piece-part.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | NHS_Segment - Next Higher Assembly
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/{notification}/piece-parts/edit/next-higher-assembly/{piece_part_detail}', 'NHS_SegmentController@edit')
        ->name('next-higher-assembly.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:NHS_Segment');
    
    Route::post('dataset/{notification}/piece-parts/edit/next-higher-assembly/{piece_part_detail}', 'NHS_SegmentController@update')
        ->name('next-higher-assembly.update');
        
    Route::post('dataset/piece-parts/delete/next-higher-assembly/{id}', 'NHS_SegmentController@destroy')->name('next-higher-assembly.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | RPS_Segment - Replaced Piece Part
    |--------------------------------------------------------------------------
    */
    
    Route::get('dataset/{notification}/piece-parts/edit/replaced-piece-part/{piece_part_detail}', 'RPS_SegmentController@edit')
        ->name('replaced-piece-part.edit')
        ->middleware('fetch-notification');
        //->middleware('get_cached_seg:RPS_Segment');
    
    Route::post('dataset/{notification}/piece-parts/edit/replaced-piece-part/{piece_part_detail}', 'RPS_SegmentController@update')
        ->name('replaced-piece-part.update');
        
    Route::post('dataset/piece-parts/delete/replaced-piece-part/{id}', 'RPS_SegmentController@destroy')->name('replaced-piece-part.destroy');
        
    /*
    |--------------------------------------------------------------------------
    | Export Reports
    |--------------------------------------------------------------------------
    */
    
    Route::match(['get', 'post'], 'export-reports', 'ReportExportController@index')->name('reports.export');
    Route::get('download-shopfindings-xml', 'ReportExportController@downloadShopFindingsXml')->name('reports.download-sf');
    Route::get('download-pieceparts-xml', 'ReportExportController@downloadPiecePartsXml')->name('reports.download-pp');
    Route::get('download-zip-xml', 'ReportExportController@downloadZip')->name('reports.download-zip');
    
    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    */
    
    Route::post('show_all_fields/{setting}', 'HomeController@showFields')->name('show_all_fields')->middleware('cors');
    Route::post('show_all_segments/{setting}', 'HomeController@showSegments')->name('show_all_segments')->middleware('cors');
    Route::post('get-rcs-failure-codes', 'ReceivedLruController@getRcsFailureCodes')->name('recieved-lru.get-rcs-failure-codes')->middleware('cors');
    Route::post('get-action-codes', 'ShopActionDetailController@getActionCodes')->name('shop-action-details.get-action-codes')->middleware('cors');
    Route::post('get-utas-codes/{notification}', 'Misc_SegmentController@getUtasCodes')->name('misc-segment.getUtasCodes')->middleware('cors');
    Route::post('get-utas-reason-codes/{notification}', 'Misc_SegmentController@getUtasReasonCodes')->name('misc-segment.getUtasReasonCodes')->middleware('cors');
    Route::post('get-utas-type-code/{notification}', 'Misc_SegmentController@getUtasTypeCode')->name('misc-segment.getUtasTypeCode')->middleware('cors');
    Route::post('get-utas-part-no/{notification}', 'Misc_SegmentController@getUtasPartNo')->name('misc-segment.getUtasPartNo')->middleware('cors');
    
    Route::post('get-reporting-organisations', 'HeaderController@getAutocomplete')
        ->name('header.get-autocomplete')
        ->middleware('cors');
    
    Route::post('get-reporting-organisation', 'HeaderController@getReportingOrganisation')
        ->name('header.get-reporting-organisation')
        ->middleware('cors');
        
    Route::post('get-customers', 'HeaderController@getCustomersAutocomplete')
        ->name('header.get-customers-autocomplete')
        ->middleware('cors');
        
    Route::post('get-customer', 'HeaderController@getCustomer')
        ->name('header.get-customer')
        ->middleware('cors');
    
    Route::post('get-airframe-manufacturers', 'AirframeInformationController@getAutocomplete')
        ->name('airframe-information.get-autocomplete')
        ->middleware('cors');
        
    Route::post('get-aircraft-details', 'AirframeInformationController@getAircraftDetail')
        ->name('airframe-information.get-aircraft-detail')
        ->middleware('cors');
        
    Route::post('status/put-on-standby', 'StandbyOrDeletedController@putOnStandby')->name('status.put-on-standby')->middleware('cors');
    Route::post('status/remove-on-standby', 'StandbyOrDeletedController@removeOnStandby')->name('status.remove-on-standby')->middleware('cors');
    Route::post('status/delete', 'StandbyOrDeletedController@delete')->name('status.delete')->middleware('cors');
    Route::post('status/restore', 'StandbyOrDeletedController@restore')->name('status.restore')->middleware('cors');
    
    /*
    |--------------------------------------------------------------------------
    | Issue Tracker
    |--------------------------------------------------------------------------
    */
    
    Route::get('issues', 'IssueTrackerController@index')->name('issue-tracker.index')->middleware('get_cached_search');
    Route::get('issues/show/{id}', 'IssueTrackerController@show')->name('issue-tracker.show');
    Route::get('issues/create', 'IssueTrackerController@create')->name('issue-tracker.create');
    Route::post('issues/create', 'IssueTrackerController@store')->name('issue-tracker.store');
    
    /*
    |--------------------------------------------------------------------------
    | Information Pages
    |--------------------------------------------------------------------------
    */
    
    Route::get('information/customers', 'InformationController@customerIndex')->name('info.customers')->middleware('get_cached_search');
    Route::get('information/locations', 'InformationController@locationIndex')->name('info.locations')->middleware('get_cached_search');
    Route::get('information/cage-codes', 'InformationController@cageCodeIndex')->name('info.cage-codes')->middleware('get_cached_search');
    Route::get('information/aircraft', 'InformationController@aircraftIndex')->name('info.aircraft')->middleware('get_cached_search');
    Route::get('information/location-parts', 'InformationController@locationPartsIndex')->name('info.location-parts')->middleware('get_cached_search');
    Route::get('information/rcs-failure-codes', 'InformationController@rcsFailureCodesIndex')->name('info.rcs-failure-codes')->middleware('get_cached_search');
    Route::get('information/shop-action-codes', 'InformationController@shopActionCodesIndex')->name('info.shop-action-codes')->middleware('get_cached_search');
    Route::get('information/user-roles', 'InformationController@userRolesIndex')->name('info.user-roles');
    Route::get('information/engine-details', 'InformationController@engineDetailsIndex')->name('info.engine-details')->middleware('get_cached_search');
    
}); // End of authenticated routes.

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['admin'])->group(function(){
    
    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */
    
    Route::get('/users', 'UserController@index')->name('user.index')->middleware('get_cached_search');
    Route::get('/users/create', 'UserController@create')->name('user.create');
    Route::post('/users/create', 'UserController@store')->name('user.store');
    Route::get('/users/{user}/edit', 'UserController@edit')->name('user.edit');
    Route::put('/users/{user}/edit', 'UserController@update')->name('user.update');
    //Route::get('/users/{user}/delete', 'UserController@delete')->name('user.delete');
    //Route::delete('/users/{user}/delete', 'UserController@destroy')->name('user.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | Activity (Audit trail)
    |--------------------------------------------------------------------------
    */
    
    Route::get('activity', 'ActivitiesController@index')->name('activity.index')->middleware('get_cached_search');
    Route::get('activity/{user}/show', 'ActivitiesController@show')->name('activity.show')->middleware('get_cached_search');
    
    // Authenticated user's activities are with the user-profile routes.
    
    /*
    |--------------------------------------------------------------------------
    | Issue Tracker (Admin Only)
    |--------------------------------------------------------------------------
    */
    
    Route::get('issues/edit/{id}', 'IssueTrackerController@edit')->name('issue-tracker.edit');
    Route::put('issues/edit/{id}', 'IssueTrackerController@update')->name('issue-tracker.update');
    
    /*
    |--------------------------------------------------------------------------
    | Customer Data (Data Admin Only)
    |--------------------------------------------------------------------------
    */
    
    Route::get('customers', 'CustomerController@index')->name('customer.index');
    Route::get('customers/create', 'CustomerController@create')->name('customer.create');
    Route::post('customers/create', 'CustomerController@store')->name('customer.store');
    Route::get('customers/edit/{customer}', 'CustomerController@edit')->name('customer.edit');
    Route::put('customers/update/{customer}', 'CustomerController@update')->name('customer.update');
    Route::get('customers/delete/{customer}', 'CustomerController@delete')->name('customer.delete');
    Route::delete('customers/delete/{customer}', 'CustomerController@destroy')->name('customer.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | Boeing Data (Admin Only) - NO LONGER USED.
    |--------------------------------------------------------------------------
    */
    
    //Route::get('update-boeing-data', 'BoeingDataController@edit')->name('boeing.edit');
    //Route::put('update-boeing-data', 'BoeingDataController@update')->name('boeing.update');
    
    /*
    |--------------------------------------------------------------------------
    | Location Pages
    |--------------------------------------------------------------------------
    */
    
    Route::get('locations', 'LocationController@index')->name('location.index')->middleware('get_cached_search');
    Route::get('locations/create', 'LocationController@create')->name('location.create');
    Route::post('locations/create', 'LocationController@store')->name('location.store');
    Route::get('locations/edit/{location}', 'LocationController@edit')->name('location.edit');
    Route::put('locations/edit/{location}', 'LocationController@update')->name('location.update');
    Route::get('locations/delete/{location}', 'LocationController@delete')->name('location.delete');
    Route::delete('locations/delete/{location}', 'LocationController@destroy')->name('location.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | Cage Code Pages
    |--------------------------------------------------------------------------
    */
    
    Route::get('cage-codes', 'CageCodeController@index')->name('cage-code.index')->middleware('get_cached_search');
    Route::get('cage-codes/create', 'CageCodeController@create')->name('cage-code.create');
    Route::post('cage-codes/create', 'CageCodeController@store')->name('cage-code.store');
    Route::get('cage-codes/edit/{cage_code}', 'CageCodeController@edit')->name('cage-code.edit');
    Route::put('cage-codes/edit/{cage_code}', 'CageCodeController@update')->name('cage-code.update');
    Route::get('cage-codes/delete/{cage_code}', 'CageCodeController@delete')->name('cage-code.delete');
    Route::delete('cage-codes/delete/{cage_code}', 'CageCodeController@destroy')->name('cage-code.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | Part List Pages
    |--------------------------------------------------------------------------
    */
    
    Route::get('part-lists', 'PartListController@index')->name('part-list.index')->middleware('get_cached_search');
    Route::get('part-lists/create/{location}', 'PartListController@create')->name('part-list.create');
    Route::post('part-lists/create/{location}', 'PartListController@store')->name('part-list.store');
    Route::get('part-lists/edit/{part_list}', 'PartListController@edit')->name('part-list.edit');
    Route::put('part-lists/edit/{part_list}', 'PartListController@update')->name('part-list.update');
    Route::get('part-lists/delete/{part_list}', 'PartListController@delete')->name('part-list.delete');
    Route::delete('part-lists/delete/{part_list}', 'PartListController@destroy')->name('part-list.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | Power BI CSV Files
    |--------------------------------------------------------------------------
    */
    
    Route::get('power-bi-csv-files', 'PowerBIController@index')->name('power-bi.index');
    Route::get('power-bi-csv-files/download/{id}', 'PowerBIController@download')->name('power-bi.download');
    
    /*
    |--------------------------------------------------------------------------
    | Legacy CSV Importer
    |--------------------------------------------------------------------------
    */
    
    Route::get('csv-importer/create', 'CsvImporterController@create')->name('csv-importer.create');
    Route::post('csv-importer/create', 'CsvImporterController@store')->name('csv-importer.store');
    
    /*
    |--------------------------------------------------------------------------
    | Maintenance Notice Pages
    |--------------------------------------------------------------------------
    */
    
    Route::get('maintenance-notices', 'MaintenanceNoticeController@index')->name('maintenance-notice.index');
    Route::get('maintenance-notices/create', 'MaintenanceNoticeController@create')->name('maintenance-notice.create');
    Route::post('maintenance-notices/create', 'MaintenanceNoticeController@store')->name('maintenance-notice.store');
    Route::get('maintenance-notices/edit/{id}', 'MaintenanceNoticeController@edit')->name('maintenance-notice.edit');
    Route::put('maintenance-notices/edit/{id}', 'MaintenanceNoticeController@update')->name('maintenance-notice.update');
    Route::get('maintenance-notices/delete/{id}', 'MaintenanceNoticeController@delete')->name('maintenance-notice.delete');
    Route::delete('maintenance-notices/delete/{id}', 'MaintenanceNoticeController@destroy')->name('maintenance-notice.destroy');
    
    /*
    |--------------------------------------------------------------------------
    | API Client Management
    |--------------------------------------------------------------------------
    */
    
    Route::get('api-client-management', 'ApiController@clientManagement')->name('api.client-management');
    
    /*
    |-----------------------------------------------------------------------------------------------
    | Utas Data Management.
    |-----------------------------------------------------------------------------------------------
    */
    
    Route::get('utas-codes/import', 'UtasDataController@importUtasCodesForm', )->name('utas-data.utas-codes');
    Route::post('utas-codes/import', 'UtasDataController@importUtasCodes', )->name('utas-data.import-utas-codes');
    
    Route::get('utas-part-numbers/import', 'UtasDataController@importUtasPartNumbersForm', )->name('utas-data.utas-part-numbers');
    Route::post('utas-part-numbers/import', 'UtasDataController@importUtasPartNumbers', )->name('utas-data.import-utas-part-numbers');
    
    Route::get('utas-reason-codes/import', 'UtasDataController@importUtasReasonCodesForm', )->name('utas-data.utas-reason-codes');
    Route::post('utas-reason-codes/import', 'UtasDataController@importUtasReasonCodes', )->name('utas-data.import-utas-reason-codes');
    
    Route::get('utas-codes/export/', [\App\Http\Controllers\UtasDataController::class, 'exportUtasCodes'])->name('utas-data.export-utas-codes');
    Route::get('utas-part-numbers/export/', [\App\Http\Controllers\UtasDataController::class, 'exportUtasPartNumbers'])->name('utas-data.export-utas-part-numbers');
    Route::get('utas-reason-codes/export/', [\App\Http\Controllers\UtasDataController::class, 'exportUtasReasonCodes'])->name('utas-data.export-utas-reason-codes');
    
    /*
    |-----------------------------------------------------------------------------------------------
    | Temporary function to find duplicates in shop finding texts. To be removed after SAP bug fix.
    |-----------------------------------------------------------------------------------------------
    */
    
    Route::get('duplicate-text', function(){
        $text = App\ShopFindings\SAS_Segment::pluck('INT', 'id')->toArray();
        
        foreach ($text as $key => $val) {
            $string = $val;
            $string = str_replace("\n", ' ', $string);
            $string = str_replace("\r", '', $string);
            
            if (mb_strlen($string)) {
                $words = explode(' ', $string);
            
                $occurrences = array_count_values($words);
                
                $duplicate = true;
                
                foreach ($occurrences as $word => $appearances) {
                    if ($appearances == 1) {
                        $duplicate = false;
                    }
                }
                
                if ($duplicate) {
                    //mydd($key);
                    
                    $segment = App\ShopFindings\SAS_Segment::find($key);
                    
                    if ($segment) {
                        mydd($segment->getShopFindingId());
                    }
                }
            }
        }
    });
    
}); // End of admin routes.

/*
|--------------------------------------------------------------------------
| TESTS FOR DEV ONLY
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function(){

    if ((env('APP_ENV') != 'production') || (env('APP_ENV') != 'live')) {
    
        Route::get('test-feed-error', function(){
            $message = 'Notifications count mismatch! Expected: ' . 10 . '. but counted: ' . 3;
                
            throw new App\Exceptions\RecordCountException($message);
        });
        
        Route::get('phpinfo', function(){
            phpinfo();
        });
        
        Route::get('validation-report', function(){
            
            $shopFindings = App\ShopFindings\ShopFinding::all();
            
            foreach ($shopFindings as $shopFinding) {
                mydd($shopFinding->getValidationReport());
            }
        });
        
        Route::get('single-validation-report/{notification}', function(\App\Notification $notification){
            $shopFinding = App\ShopFindings\ShopFinding::find($notification->get_RCS_SFI());
            mydd($shopFinding->isValid());
            mydd($shopFinding->getValidationReport());
        });
        
        Route::get('organise-blob-storage', function(){
            $storage = Illuminate\Support\Facades\Storage::disk('azure-blob-storage-archive');
            $files = $storage->listContents('2019/05');
            
            //mydd($files);
            
            if (!empty($files)) {
                foreach ($files as $file) {
                    
                    $filename = substr($file['path'], strrpos($file['path'], '/') + 1);
                    
                    if (stristr($file['path'], '2019-05-22')) {
                        $storage->rename($file['path'],'2019' . DIRECTORY_SEPARATOR . '05' . DIRECTORY_SEPARATOR . '22' . DIRECTORY_SEPARATOR . $filename);
                    }
                    
                    if (stristr($file['path'], '2019-05-23')) {
                        $storage->rename($file['path'],'2019' . DIRECTORY_SEPARATOR . '05' . DIRECTORY_SEPARATOR . '23' . DIRECTORY_SEPARATOR . $filename);
                    }
                    
                    if (stristr($file['path'], '2019-05-24')) {
                        $storage->rename($file['path'],'2019' . DIRECTORY_SEPARATOR . '05' . DIRECTORY_SEPARATOR . '24' . DIRECTORY_SEPARATOR . $filename);
                    }
                }
            }
        });
        
        Route::get('create-acronym', function(){
            
            $user = App\User::find(13);
            
            mydd(App\User::createAcronym($user->first_name, $user->last_name));
            mydd(App\User::updateAcronym($user->id, $user->first_name, $user->last_name));
        });
        
        Route::get('eloquent-test', function(){
            $test = App\User::select(
                'users.*',
                DB::raw('count(activities.id) as count')
            )
            ->leftJoin('activities', 'activities.user_id', '=', 'users.id')
            ->groupBy('users.id')
            ->orderBy('count', 'desc')->paginate(20);
            
            mydd($test);
            
            mydd(App\User::withCount('activities')->orderBy('activities_count', 'desc')->paginate(20));
        });
        
        Route::get('json', function(){
            return App\Notification::with('pieceParts')->inRandomOrder()->first()->toJson();
        });
        
        Route::get('clean-data-test', function(){
            
            $array = [
                'piece_part_detail_id' => '4785089750',
                'SFI' => '344087087',
                'PPI' => '4785089750',
                'PFC' => 'D',
                'MFR' => 'eGySl',
                'MFN' => NULL,
                'MPN' => 0,
                'SER' => 'ZZZZZ',
                'FDE' => NULL,
                'PNR' => 'kbh1ZZL',
                'OPN' => NULL,
                'USN' => 'zJaYrKxY0w1iC',
                'PDT' => NULL,
                'GEL' => 'BFNgYSnv4P',
                'MRD' => NULL,
                'ASN' => 'T',
                'UCN' => 'ykOVh2H',
                'SPL' => NULL,
                'UST' => NULL,
            ];
            
            mydd($array);
            
            
            
            if (count($array)) {
                foreach ($array as $k => $v) {
                    if(!mb_strlen($v)) {
                        unset($array[$k]); 
                    }
                }
            }
            
            mydd($array);
            
            echo htmlspecialchars(0, ENT_XML1, 'UTF-8');
            
        });
        
        // Retrieve notification by full id.
        Route::get('api/v1/notifications/{id?}', function (Illuminate\Http\Request $request, $id = NULL){
            
            /*
            search, plant code, status, date_from, date_to - retrieve multiple record ids.
            
            OR
            
            id - retrieves single record with all related data.
            
            Users: John, Peter
    
            METHOD  URL                      STATUS  RESPONSE
            GET     /users                   200     [John, Peter]
            GET     /users/john              200     John
            GET     /users/kyle              404     Not found
            GET     /users?name=kyle`        200     []
            DELETE  /users/john              204     No Content
            
            */
            
            // Perhaps we only accept full notification id?
            // Or remove preceding zeros and return first result.
            // Or zerofill and search.
            
            if ($id) {
                
                $id = ltrim($id, '0'); // Remove leading zeros and return first like result.
                
                $notification = App\Notification::with('pieceParts')->where('id', 'LIKE', "%$id")->first();
                
                if (!$notification) return response()->json(['error' => true, 'message' => '404 not found.'], 404);
            
                return response()->json($notification->toArray(), 200)->setEncodingOptions(JSON_PRETTY_PRINT);
            }
            
            $orderbyWhitelist = [
                'id' => 'notifications.rcsSFI',
                'material' => 'notifications.rcsMPN',
                'serial' => 'notifications.rcsSER',
                'roc' => 'notifications.hdrROC',
                'ron' => 'notifications.hdrRON',
                'date' => 'notifications.rcsMRD'
            ];
            
            $defaultOrderBy = 'id';
            
            $defaultOrder = 'asc';
            
            $statuses = App\Notification::$statuses;
            
            $reportingOrganisations = App\Location::filter('view-all-notifications');
            $orderby = $orderbyWhitelist[$defaultOrderBy];
            $order = $defaultOrder;
            
            if ($request->has('order_by') && array_key_exists($request->order_by, $orderbyWhitelist)) {
                $orderby = $orderbyWhitelist[$request->order_by];
            }
            
            if ($request->has('order') && in_array($request->order, ['desc', 'asc'])) {
                $order = $request->order;
            }
            
            $search = $request->search ?? NULL;
            $dateStart = $request->date_start ?? NULL;
            $dateEnd = $request->date_end ?? NULL;
            $status = $request->status ?? NULL;
            
            if ($request->plant_code == 'All') {
                $plantCode = NULL;
            } else if ($request->plant_code) {
                $plantCode = $request->plant_code;
            } else {
                // Sometimes there may be no results from the default location.
                $plantCode = array_key_exists(auth()->user()->defaultLocation(), $reportingOrganisations) ? auth()->user()->defaultLocation() : NULL;
            }
            
            // Determine if the user can view all notifications and restrict accordingly.
            if (Gate::denies('view-all-notifications')) {
                $plantCode = auth()->user()->location->plant_code;
            }
            
            // What about pagination ???
            
            // page
            
            $notifications = App\Notification::getToDoList($search, $status, $plantCode, $dateStart, $dateEnd, $orderby, $order);
            
            //mydd($notifications);
            
            // current_page_no, last_page_no, data, from, to, total, per_page, first_page_url, last_page_url, next_page_url, prev_page_url
            
            return response()->json($notifications->items(), 200)->setEncodingOptions(JSON_PRETTY_PRINT);
        });
        
        Route::get('get-token-test', function(){
            $client = new \App\SAPOAuth2Client;
            
            echo $client->getAccessToken();
        });
        
        Route::get('create-test-notification', function(){
            $notification = factory(App\Notification::class)->create([
                "id" => "000300000977",
                "plant_code" => "3200",
                "rcsSFI" => "000300000977",
                "rcsMFR" => ''
            ]);
        });
        
        Route::get('get-location-first-cage-code', function(){
            
            // 3126, 9991
            
            $location = App\Location::whereHas('cage_codes')
                ->with(['cage_codes' => function($query){
                    $query->orderBy('cage_code', 'asc');
                }])->where('plant_code', 3126)->first();
            
            return $location ? $location->cage_codes[0]->cage_code : 'ZZZZZ';
        });
        
    } // end of test routes...
});