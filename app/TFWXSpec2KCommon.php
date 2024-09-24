<?php


namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class TFWXSpec2KCommon
{



    /**
     * LJMMay24 MGTSUP-749
     * are we dealing with a Collins Notification
     * however it looks like it can match agains both
     * notifications->rcsMPN
     * and
     * rcs_segments->MPN
     */
    public static function IsCollins($NotificationID)
    {

        //        Log::info('MGTSUP-749 : IsCollins '. $NotificationID);
        $iNumberOfCollinsMatches = 0; // this says if we have any collins records at all?

        $Notification_MATNR = "DONTMATCHTHIS";
        $RCS_Segment_MATNR = "DONTMATCHTHIS";


        // NotificationValues
//        $LJMSqlQueryNotifications = " SELECT * FROM notifications WHERE id = " . $NotificationID . " AND rcsMPN IS NOT NULL;";
        $LJMSqlQueryNotifications = " SELECT * FROM notifications WHERE id = " . $NotificationID . " ;";
        //        Log::info('MGTSUP-749 : $LJMSqlQueryNotifications '. $LJMSqlQueryNotifications);
        $recNotifications = DB::select($LJMSqlQueryNotifications);
        foreach ($recNotifications as $recNotification) {
            //            Log::info('MGTSUP-749 : $recNotification->rcsMPN '. $recNotification->rcsMPN);
            $Notification_MATNR = $recNotification->rcsMPN; // we have an MPN
        }

        // is there a shop_finding_details record?
        $shop_finding_details_id = 0;
        $LJMSqlQueryShop_Finding_Details = " SELECT * FROM shop_findings_details WHERE shop_finding_id = " . $NotificationID . ";";
        $recShop_Finding_Details = DB::select($LJMSqlQueryShop_Finding_Details);
        foreach ($recShop_Finding_Details as $recShop_Finding_Detail) {
            $shop_finding_details_id = $recShop_Finding_Detail->id;
            // is there an rcs_segments record
            $LJMSqlQueryRCS_Segments = " SELECT * FROM rcs_segments WHERE shop_findings_detail_id = " . $shop_finding_details_id . " AND MPN IS NOT NULL;";
            $recRCS_Segments = DB::select($LJMSqlQueryRCS_Segments);
            foreach ($recRCS_Segments as $recRCS_Segment) {
                $RCS_Segment_MATNR = $recRCS_Segment->MPN;

                Log::info('MGTSUP-749 : $RCS_Segment_MATNR ' . $RCS_Segment_MATNR);

            }
        }

        // Notifications Table
        $LJMSqlQueryNotifications = " SELECT count(MATNR) AS NumberOfCollinsMatches FROM utas_codes WHERE MATNR = '" . $Notification_MATNR . "' OR MATNR = '" . $RCS_Segment_MATNR . "' ;";
        //        Log::info('MGTSUP-749 : $LJMSqlQueryNotifications '. $LJMSqlQueryNotifications);
        $NumberOfCollinsMatches = DB::select($LJMSqlQueryNotifications);
        foreach ($NumberOfCollinsMatches as $NumberOfCollinsMatch) {
            $iNumberOfCollinsMatches = $iNumberOfCollinsMatches + $NumberOfCollinsMatch->NumberOfCollinsMatches;
        }


        if ($iNumberOfCollinsMatches > 0) {
            Log::info('MGTSUP-974 : Setting Collins Notification ' . $NotificationID . ' Query was: ' . $LJMSqlQueryNotifications);


            return true;
        } else {
            return false;
        }
    }



}


?>