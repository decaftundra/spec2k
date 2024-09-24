<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedMissingShopFindings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        
        DB::table('notifications')->insert([
            [
                'id' => "000350417136",
                'plant_code' => 3101,
                'hdrROC' => 79318,
                'hdrRON' => "MCS North Hollywood",
                'rcsSFI' => "000350417136",
                'rcsMRD' => "2020-01-06 00:00:00",
                'rcsMFR' => 79318,
                'rcsMPN' => "424315-2",
                'rcsSER' => "0350",
                'rcsREM' => "REASON FOR REMOVAL: BLEED 2 LEAKS CAS MESSAGE DISPLAYED.\nWORKSCOPE: AT REPAIR UPGRADE TO P/N 424315-4.\nVISUAL: NORMAL EXTERNAL WEAR.",
                'sasINT' => "Fan Air Valve\nVisual/Teardown Findings: Normal external wear. Burst disk ruptured.\nRepair and Upgrade to -4 in accordance with CMM 36-11-04 Rev. No. 3\nDated Jan 01/19, Comply with SL424315-36-01 Original Issue Dated Sep.\n30/16 and SB 424315-36-01 Original Issue Dated Oct. 22/18\nERNESTO PANFILO 1-13-2020",
                'susSHD' => "2020-04-15 23:15:09",
                'susMFR' => NULL,
                'susMPN' => "424315-4",
                'susSER' => "0350"
            ],
            [
                'id' => "000350417786",
                'plant_code' => 3101,
                'hdrROC' => 79318,
                'hdrRON' => "MCS North Hollywood",
                'rcsSFI' => "000350417786",
                'rcsMRD' => "2020-01-09 00:00:00",
                'rcsMFR' => 79318,
                'rcsMPN' => "424165-3",
                'rcsSER' => 1219,
                'rcsREM' => "REMOVAL REASON: RH COWL VALVE FAIL OPEN. AMBER CAS MESSAGE.\nWORKSCOPE: ATP AND TEAR DOWN REPORT WITH A DETERMINATION OF ROOT CAUSE TO ANY ANOMALY.\nVISUAL: NORMAL EXTERNAL WEAR.",
                'sasINT' => "Valve Thermal/Cowl Anti-Ice\nIncoming test: Valve failed for regulation timing test.\nFindings: Body has contamination build up at sealing area of body bore.\nRegulator seat and poppet worn out. Servo poppet and seat look clean no\ncontamination.\nRecommendation: Overhaul per CMM 30-21-01 Rev 1, 7/15/2019\nFindings: Body has contamination build up at sealing area of body bore.\nRegulator seat and poppet worn out.\nRecommendation: Overhaul per CMM 30-21-01 Rev 1, 7/15/2019",
                'susSHD' => "2020-04-20 23:19:01",
                'susMFR' => 79318,
                'susMPN' => "424165-3",
                'susSER' => 1219
            ],
            [
                'id' => "000350423461",
                'plant_code' => 3101,
                'hdrROC' => 79318,
                'hdrRON' => "MCS North Hollywood",
                'rcsSFI' => "000350423461",
                'rcsMRD' => "2020-02-21 00:00:00",
                'rcsMFR' => 79318,
                'rcsMPN' => "424315-3",
                'rcsSER' => "0238",
                'rcsREM' => "REASON FOR REMOVAL: BLEED 1 FAIL CAS.\nWORKSCOPE: INSPECT, TEST, REPAIR OR OVERHAUL AS REQUIRED. IF APPLICABLE UPGRADE THE PART TO THE LATEST VERSION.\nVISUAL: MINOR PITTING INSIDE VALVE BODY.",
            "Fan Air Valve
                'sasINT' => Visual/Teardown Findings: Normal external wear. Minor nicks on disk.\nBlue and pink substance on body flange.\nRepair and Upgrade to -4 in accordance with CMM 36-11-04 Rev. No. 3\nDated Jan 01/19, Comply with SB 424315-36-01 Original Issue Dated Oct.\n22/18\nERNESTO PANFILO 3-4-2020",
                'susSHD' => "2020-04-15 23:15:09",
                'susMFR' => NULL,
                'susMPN' => "424315-4",
                'susSER' => "0238"
            ],
            [
                'id' => "000350424551",
                'plant_code' => 3101,
                'hdrROC' => 79318,
                'hdrRON' => "MCS North Hollywood",
                'rcsSFI' => "000350424551",
                'rcsMRD' => "2020-02-29 00:00:00",
                'rcsMFR' => 79318,
                'rcsMPN' => "341595-2",
                'rcsSER' => "1012A",
                'rcsREM' => "REASON FOR REMOVAL: A/S MSG\nWORKSCOPE: INSPECT, TEST AND ADVISE. COMPLY WITH UPS EO-C-2900-20348A.\nVISUAL: EXTERNAL CONTAMINATION.",
                'sasINT' => "VALVE\nVisually inspected per CMM 29-12-95 Rev 3 dated Apr 1/98\nFailed incoming test\nValve is contaminated\nOverhaul is recommended\nDADO 3.16.2020\nActuator failed electrical test. Overhaul is recommended per CMM mentioned above. Evaluated by Franco G. on 3/25/2020",
                'susSHD' => "2020-04-16 23:15:24",
                'susMFR' => 79318,
                'susMPN' => "341595-2",
                'susSER' => "1012A"
            ]
        ]);
        
        Schema::enableForeignKeyConstraints();
    }
}