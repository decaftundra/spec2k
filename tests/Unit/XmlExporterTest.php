<?php

namespace Tests\Unit;

use App\Codes\UtasReasonCode;
use App\UtasCode;
use App\UtasPartNumber;
use App\XmlExporter;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class XmlExporterTest extends TestCase
{
    public $shopFindings;
    public $collinsShopFindings;
    public $xmlExporter;
    
    /**
     * Test the exporter can create an array for the header.
     *
     * @return void
     */
    public function testExportHeader()
    {
        $this->shopFindings = $this->createMultipleShopFindingsAndPiecePartsWithAllSegments(5, mt_rand(1, 5), $this->adminUser);
        $this->xmlExporter = new XmlExporter;
        
        $header = $this->xmlExporter->exportHeader($this->shopFindings->random(1)->first());
        
        $keysInOrder = ['CHG', 'ROC', 'OPR', 'RON', 'WHO'];
        
        $this->assertAllArrayValuesAreFilled($header);
        $this->assertTrue(is_array($header));
        $this->assertTrue($keysInOrder === array_keys($header));
    }
    
    /**
     * Test the exporter can create an array for the shopfinding.
     *
     * @return void
     */
    public function testExportShopFinding()
    {
        $this->shopFindings = $this->createMultipleShopFindingsAndPiecePartsWithAllSegments(5, mt_rand(1, 5), $this->adminUser);
        $this->xmlExporter = new XmlExporter;
        
        $shopFinding = $this->xmlExporter->exportShopFinding($this->shopFindings->random(1)->first());
        
        $keysInOrder = [
            'RCS_Segment',
            'SAS_Segment',
            'SUS_Segment',
            'RLS_Segment',
            'LNK_Segment',
            'AID_Segment',
            'EID_Segment',
            'API_Segment',
            'ATT_Segment',
            'SPT_Segment'
        ];
        
        $this->assertAllArrayValuesAreFilled($shopFinding);
        $this->assertTrue(is_array($shopFinding));
        $this->assertTrue($keysInOrder === array_keys($shopFinding));
    }
    
    /**
     * Test the exporter can create an array for the piece parts.
     *
     * @return void
     */
    public function testExportPiecePart()
    {
        $this->shopFindings = $this->createMultipleShopFindingsAndPiecePartsWithAllSegments(5, mt_rand(1, 5), $this->adminUser);
        $this->xmlExporter = new XmlExporter;
        
        $shopFinding = $this->shopFindings->random(1)->first();
        
        $piecePartArray = $this->xmlExporter->exportPiecePart($shopFinding->PiecePart, $shopFinding->id);
        
        $this->assertTrue(is_array($piecePartArray));
        
        $piecePartCollection = collect($piecePartArray);
        
        $piecePartDetail = $piecePartCollection->random(1)->first();
        
        $keysInOrder = ['WPS_Segment', 'NHS_Segment', 'RPS_Segment'];
        
        $this->assertAllArrayValuesAreFilled($piecePartArray);
        $this->assertTrue($keysInOrder === array_keys($piecePartDetail));
    }
    
    /**
     * Test the shop finding xml output structure.
     *
     * @return void
     */
    public function testShopFindingsXmlExport()
    {
        $this->shopFindings = $this->createMultipleShopFindingsAndPiecePartsWithAllSegments(5, mt_rand(1, 5), $this->adminUser);
        $this->xmlExporter = new XmlExporter;
        
        $rawData = [];
        
        $from = Carbon::now()->subMonths(6);
        $to = Carbon::now()->addMonths(6);
        
        $xml = $this->xmlExporter->createShopFindingsXmlFile($this->shopFindings, $from, $to);
        
        $expectedXmlStructure = '<ATA_InformationSet xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" id="R2009.1" version="1.0" xsi:noNamespaceSchemaLocation="ATA_InformationSet.xsd">';
        
        foreach ($this->shopFindings as $shopFinding) {
            $expectedXmlStructure .= '<ReliabilityData>';
            $expectedXmlStructure .= '<ShopFindings version="2.00">';
            
            if ($shopFinding->HDR_Segment) {
                
                $rawData[$shopFinding->id]['HDR_Segment'] = $shopFinding->HDR_Segment->toArray();
                
                $expectedXmlStructure .= '<HDR_Segment>';
                
                $headerData = [];
                
                foreach ($shopFinding->HDR_Segment->getAttributes() as $key => $val) {
                    
                    $methodName = $shopFinding->HDR_Segment->getPrefix().$key;
                    
                    if (method_exists($shopFinding->HDR_Segment, $methodName) && mb_strlen(trim($val))) {
                        $headerData[$key] = $val;
                    }
                }
                
                $headerData = array_slice($headerData, 0, 2, true) +
                ["RDT" => 'dummydata', 'RSD' => 'dummydata'] +
                array_slice($headerData, 2, count($headerData) - 2, true);
                
                foreach ($headerData as $key => $value) {
                    $expectedXmlStructure .= "<$key/>";
                }
                
                $expectedXmlStructure .= '</HDR_Segment>';
            }
            
            if ($shopFinding->shopFindingsDetail) {
                $expectedXmlStructure .= '<ShopFindingsDetails>';
                
                if ($shopFinding->ShopFindingsDetail->RCS_Segment) {
                    
                    $rawData[$shopFinding->id]['RCS_Segment'] = $shopFinding->ShopFindingsDetail->RCS_Segment->toArray();
                    
                    $expectedXmlStructure .= '<RCS_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->RCS_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->RCS_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->RCS_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</RCS_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->SAS_Segment) {
                    
                    $rawData[$shopFinding->id]['SAS_Segment'] = $shopFinding->ShopFindingsDetail->SAS_Segment->toArray();
                    
                    $expectedXmlStructure .= '<SAS_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->SAS_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->SAS_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->SAS_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</SAS_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->SUS_Segment) {
                    
                    $rawData[$shopFinding->id]['SUS_Segment'] = $shopFinding->ShopFindingsDetail->SUS_Segment->toArray();
                    
                    $expectedXmlStructure .= '<SUS_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->SUS_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->SUS_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->SUS_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</SUS_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->RLS_Segment) {
                    
                    $rawData[$shopFinding->id]['RLS_Segment'] = $shopFinding->ShopFindingsDetail->RLS_Segment->toArray();
                    
                    $expectedXmlStructure .= '<RLS_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->RLS_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->RLS_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->RLS_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</RLS_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->LNK_Segment) {
                    
                    $rawData[$shopFinding->id]['LNK_Segment'] = $shopFinding->ShopFindingsDetail->LNK_Segment->toArray();
                    
                    $expectedXmlStructure .= '<LNK_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->LNK_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->LNK_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->LNK_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</LNK_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->AID_Segment) {
                    
                    $rawData[$shopFinding->id]['AID_Segment'] = $shopFinding->ShopFindingsDetail->AID_Segment->toArray();
                    
                    $expectedXmlStructure .= '<AID_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->AID_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->AID_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->AID_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</AID_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->EID_Segment) {
                    
                    $rawData[$shopFinding->id]['EID_Segment'] = $shopFinding->ShopFindingsDetail->EID_Segment->toArray();
                    
                    $expectedXmlStructure .= '<EID_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->EID_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->EID_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->EID_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</EID_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->API_Segment) {
                    
                    $rawData[$shopFinding->id]['API_Segment'] = $shopFinding->ShopFindingsDetail->API_Segment->toArray();
                    
                    $expectedXmlStructure .= '<API_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->API_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->API_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->API_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</API_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->ATT_Segment) {
                    
                    $rawData[$shopFinding->id]['ATT_Segment'] = $shopFinding->ShopFindingsDetail->ATT_Segment->toArray();
                    
                    $expectedXmlStructure .= '<ATT_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->ATT_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->ATT_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->ATT_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</ATT_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->SPT_Segment) {
                    
                    $rawData[$shopFinding->id]['SPT_Segment'] = $shopFinding->ShopFindingsDetail->SPT_Segment->toArray();
                    
                    $expectedXmlStructure .= '<SPT_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->SPT_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->SPT_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->SPT_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</SPT_Segment>';
                }
                
                $expectedXmlStructure .= '</ShopFindingsDetails>';
            }
            
            $expectedXmlStructure .= '</ShopFindings>';
            $expectedXmlStructure .= '</ReliabilityData>';
        }
        
        $expectedXmlStructure .= '</ATA_InformationSet>';
        
        $expected = new \DOMDocument;
        $expected->loadXML($expectedXmlStructure);
        
        $actual = new \DOMDocument;
        $actual->loadXML($xml);
        
        $dump = '<pre>' . print_r(['raw' => $rawData, 'expected' => $expectedXmlStructure, 'actual' => $xml]) . '</pre>';

        $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, true, $dump);
    }
    
    /**
     * Test the piece part output xml structure.
     *
     * @return void
     */
    public function testPiecePartXmlExport()
    {
        $this->shopFindings = $this->createMultipleShopFindingsAndPiecePartsWithAllSegments(5, mt_rand(1, 5), $this->adminUser);
        $this->xmlExporter = new XmlExporter;
        
        $rawData = [];
        
        $from = Carbon::now()->subMonths(6);
        $to = Carbon::now()->addMonths(6);
        
        $xml = $this->xmlExporter->createPiecePartsXmlFile($this->shopFindings, $from, $to);
        
        $expectedXmlStructure = '<ATA_InformationSet xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" id="R2009.1" version="1.0" xsi:noNamespaceSchemaLocation="ATA_InformationSet.xsd">';
        
        foreach ($this->shopFindings as $shopFinding) {
            $expectedXmlStructure .= '<ReliabilityData>';
            $expectedXmlStructure .= '<PieceParts version="1.00">';
            
            if ($shopFinding->HDR_Segment) {
                
                $rawData[$shopFinding->id]['HDR_Segment'] = $shopFinding->HDR_Segment->toArray();
                
                $expectedXmlStructure .= '<HDR_Segment>';
                
                $headerData = [];
                
                foreach ($shopFinding->HDR_Segment->getAttributes() as $key => $val) {
                    
                    $methodName = $shopFinding->HDR_Segment->getPrefix().$key;
                    
                    if (method_exists($shopFinding->HDR_Segment, $methodName) && mb_strlen(trim($val))) {
                        $headerData[$key] = $val;
                    }
                }
                
                $headerData = array_slice($headerData, 0, 2, true) +
                ["RDT" => 'dummydata', 'RSD' => 'dummydata'] +
                array_slice($headerData, 2, count($headerData) - 2, true);
                
                foreach ($headerData as $key => $value) {
                    $expectedXmlStructure .= "<$key/>";
                }
                
                $expectedXmlStructure .= '</HDR_Segment>';
            }
            
            if ($shopFinding->PiecePart->PiecePartDetails && count($shopFinding->PiecePart->PiecePartDetails)) {
                
                foreach($shopFinding->PiecePart->PiecePartDetails as $key => $ppd) {
                    $expectedXmlStructure .= '<PiecePartDetails>';
                    
                    if ($ppd->WPS_Segment) {
                        
                        $rawData[$shopFinding->id][$ppd->id]['WPS_Segment'] = $ppd->WPS_Segment->toArray();
                        
                        $expectedXmlStructure .= '<WPS_Segment>';
                        
                        foreach ($ppd->WPS_Segment->getAttributes() as $key => $val) {
                            
                            $methodName = $ppd->WPS_Segment->getPrefix().$key;
                            
                            if (method_exists($ppd->WPS_Segment, $methodName) && mb_strlen(trim($val))) {
                                $expectedXmlStructure .= "<$key/>";
                            }
                        }
                        
                        $expectedXmlStructure .= '</WPS_Segment>';
                    }
                    
                    if ($ppd->NHS_Segment) {
                        
                        $rawData[$shopFinding->id][$ppd->id]['NHS_Segment'] = $ppd->NHS_Segment->toArray();
                        
                        $expectedXmlStructure .= '<NHS_Segment>';
                        
                        foreach ($ppd->NHS_Segment->getAttributes() as $key => $val) {
                            
                            $methodName = $ppd->NHS_Segment->getPrefix().$key;
                            
                            if (method_exists($ppd->NHS_Segment, $methodName) && mb_strlen(trim($val))) {
                                $expectedXmlStructure .= "<$key/>";
                            }
                        }
                        
                        $expectedXmlStructure .= '</NHS_Segment>';
                    }
                    
                    if ($ppd->RPS_Segment) {
                        
                        $rawData[$shopFinding->id][$ppd->id]['RPS_Segment'] = $ppd->RPS_Segment->toArray();
                        
                        $expectedXmlStructure .= '<RPS_Segment>';
                        
                        foreach ($ppd->RPS_Segment->getAttributes() as $key => $val) {
                            
                            $methodName = $ppd->RPS_Segment->getPrefix().$key;
                            
                            if (method_exists($ppd->RPS_Segment, $methodName) && mb_strlen(trim($val))) {
                                $expectedXmlStructure .= "<$key/>";
                            }
                        }
                        
                        $expectedXmlStructure .= '</RPS_Segment>';
                    }
                
                    $expectedXmlStructure .= '</PiecePartDetails>';
                }
                
            }
            
            $expectedXmlStructure .= '</PieceParts>';
            $expectedXmlStructure .= '</ReliabilityData>';
        }
        
        $expectedXmlStructure .= '</ATA_InformationSet>';
        
        $expected = new \DOMDocument;
        $expected->loadXML($expectedXmlStructure);
        
        $actual = new \DOMDocument;
        $actual->loadXML($xml);
        
        $dump = '<pre>' . print_r(['raw' => $rawData, 'expected' => $expectedXmlStructure, 'actual' => $xml]) . '</pre>';

        $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, true, $dump);
    }
    
    /**
     * Check Collins XML have the correct substitutions included.
     *
     * @return void
     */
    public function testCollinsShopFindingXmlExportSubstitutions()
    {
        $this->xmlExporter = new XmlExporter;
        
        $this->collinsShopFindings = $this->createMultipleCollinsShopFindingsAndPieceParts(5, mt_rand(1, 5), $this->adminUser);
        
        $from = Carbon::now()->subMonths(6);
        $to = Carbon::now()->addMonths(6);
        
        $xml = $this->xmlExporter->createShopFindingsXmlFile($this->collinsShopFindings, $from, $to);
        
        //mydd($xml);
        
        $domDocument = new \DOMDocument;
        $domDocument->loadXML($xml);
        
        // Check xml for collins part number substitutions in RCS, RLS & SUS segments.
        $collinsPartNos = UtasPartNumber::pluck('utas_part_no')->toArray();
        $collinsReasons = UtasReasonCode::pluck('REASON')->toArray();
        
        $rcsNodes = $domDocument->getElementsByTagName('RCS_Segment');
        $nodeListLength = $rcsNodes->length;
        
        if ($nodeListLength) {
            for ($i = 0; $i < $nodeListLength; $i ++) {
                $node = $rcsNodes->item(0);
                
                foreach ($node->childNodes as $childNode) {
                    $this->assertEquals($node->getElementsByTagName('MPN')->length, 1);
                    $this->assertEquals($node->getElementsByTagName('PNR')->length, 1);
                    $this->assertEquals($node->getElementsByTagName('MFR')->length, 1);
                    $this->assertEquals($node->getElementsByTagName('REM')->length, 1);
                    
                    if ($childNode->nodeName == 'MPN') {
                        $this->assertTrue(in_array($childNode->nodeValue, $collinsPartNos));
                    }
                    
                    if ($childNode->nodeName == 'PNR') {
                        $this->assertTrue(in_array($childNode->nodeValue, $collinsPartNos));
                    }
                    
                    if ($childNode->nodeName == 'MFR') {
                        $this->assertEquals($childNode->nodeValue, UtasCode::CAGE_CODE);
                    }
                    
                    if ($childNode->nodeName == 'REM') {
                        $this->assertTrue(in_array($childNode->nodeValue, $collinsReasons));
                    }
                }
            }
        } else {
            mydd('RCS Segments missing from Collins XML');
            mydd($xml,1);
        }
        
        $rlsNodes = $domDocument->getElementsByTagName('RLS_Segment');
        $nodeListLength = $rlsNodes->length;
        
        if ($nodeListLength) {
            for ($i = 0; $i < $nodeListLength; $i ++) {
                $node = $rlsNodes->item(0);
                
                foreach ($node->childNodes as $childNode) {
                    $this->assertEquals($node->getElementsByTagName('MPN')->length, 1);
                    $this->assertEquals($node->getElementsByTagName('PNR')->length, 1);
                    $this->assertEquals($node->getElementsByTagName('MFR')->length, 1);
                    
                    if ($childNode->nodeName == 'MPN') {
                        $this->assertTrue(in_array($childNode->nodeValue, $collinsPartNos));
                    }
                    
                    if ($childNode->nodeName == 'PNR') {
                        $this->assertTrue(in_array($childNode->nodeValue, $collinsPartNos));
                    }
                    
                    if ($childNode->nodeName == 'MFR') {
                        $this->assertEquals($childNode->nodeValue, UtasCode::CAGE_CODE);
                    }
                }
            }
        } else {
            mydd('RLS Segments missing from Collins XML');
            mydd($xml,1);
        }
        
        $susNodes = $domDocument->getElementsByTagName('SUS_Segment');
        $nodeListLength = $susNodes->length;
        
        if ($nodeListLength) {
            for ($i = 0; $i < $nodeListLength; $i ++) {
                $node = $susNodes->item(0);
                
                foreach ($node->childNodes as $childNode) {
                    $this->assertEquals($node->getElementsByTagName('MPN')->length, 1);
                    $this->assertEquals($node->getElementsByTagName('PNR')->length, 1);
                    $this->assertEquals($node->getElementsByTagName('MFR')->length, 1);
                    
                    if ($childNode->nodeName == 'MPN') {
                        $this->assertTrue(in_array($childNode->nodeValue, $collinsPartNos));
                    }
                    
                    if ($childNode->nodeName == 'PNR') {
                        $this->assertTrue(in_array($childNode->nodeValue, $collinsPartNos));
                    }
                    
                    if ($childNode->nodeName == 'MFR') {
                        $this->assertEquals($childNode->nodeValue, UtasCode::CAGE_CODE);
                    }
                }
            }
        } else {
            mydd('SUS Segments missing from Collins XML');
            mydd($xml,1);
        }
        
        $sasNodes = $domDocument->getElementsByTagName('SAS_Segment');
        $nodeListLength = $sasNodes->length;
        
        if ($nodeListLength) {
            for ($i = 0; $i < $nodeListLength; $i ++) {
                $node = $sasNodes->item(0);
                
                foreach ($node->childNodes as $childNode) {
                    $this->assertEquals($node->getElementsByTagName('INT')->length, 1);
                    
                    if ($childNode->nodeName == 'INT') {
                        $this->assertTrue((bool) stristr($childNode->nodeValue, '[SubassemblyName] ='));
                        $this->assertTrue((bool) stristr($childNode->nodeValue, ' [Component] ='));
                        $this->assertTrue((bool) stristr($childNode->nodeValue, ' [FeatureName] ='));
                        $this->assertTrue((bool) stristr($childNode->nodeValue, ' [FailureDescription] ='));
                        $this->assertTrue((bool) stristr($childNode->nodeValue, ' [Modifier] ='));
                        $this->assertTrue((bool) stristr($childNode->nodeValue, ' [Comments] ='));
                    }
                }
            }
        } else {
            mydd('SAS Segments missing from Collins XML');
            mydd($xml,1);
        }
    }
    
    /**
     * Check Collins XML have the correct substitutions included in piece parts.
     *
     * @return void
     */
    public function testCollinsPiecePartXmlExportSubstitutions()
    {
        $this->xmlExporter = new XmlExporter;
        
        $this->collinsShopFindings = $this->createMultipleCollinsShopFindingsAndPieceParts(5, mt_rand(1, 5), $this->adminUser);
        
        $from = Carbon::now()->subMonths(6);
        $to = Carbon::now()->addMonths(6);
        
        $xml = $this->xmlExporter->createPiecePartsXmlFile($this->collinsShopFindings, $from, $to);
        
        mydd($xml);
        
        $domDocument = new \DOMDocument;
        $domDocument->loadXML($xml);
        
        // Check xml for collins part number substitutions in RCS, RLS & SUS segments.
        $collinsPartNos = UtasPartNumber::pluck('utas_part_no')->toArray();
        $collinsReasons = UtasReasonCode::pluck('REASON')->toArray();
        
        $nhsNodes = $domDocument->getElementsByTagName('NHS_Segment');
        $nodeListLength = $nhsNodes->length;
        
        if ($nodeListLength) {
            for ($i = 0; $i < $nodeListLength; $i ++) {
                $node = $nhsNodes->item(0);
                
                foreach ($node->childNodes as $childNode) {
                    $this->assertEquals($node->getElementsByTagName('MPN')->length, 1);
                    $this->assertEquals($node->getElementsByTagName('PNR')->length, 1);
                    $this->assertEquals($node->getElementsByTagName('MFR')->length, 1);
                    
                    if ($childNode->nodeName == 'MPN') {
                        $this->assertTrue(in_array($childNode->nodeValue, $collinsPartNos));
                    }
                    
                    if ($childNode->nodeName == 'PNR') {
                        $this->assertTrue(in_array($childNode->nodeValue, $collinsPartNos));
                    }
                    
                    if ($childNode->nodeName == 'MFR') {
                        $this->assertEquals($childNode->nodeValue, UtasCode::CAGE_CODE);
                    }
                }
            }
        } else {
            mydd('NHS Segments missing from Collins XML');
            mydd($xml,1);
        }
    }
    
    /**
     * Test the Collins shop finding xml structure.
     *
     * @return void
     */
    public function testCollinsShopFindingXmlExport()
    {
        $this->xmlExporter = new XmlExporter;
        
        $rawData = [];
        
        $this->collinsShopFindings = $this->createMultipleCollinsShopFindingsAndPieceParts(5, mt_rand(1, 5), $this->adminUser);
        
        $from = Carbon::now()->subMonths(6);
        $to = Carbon::now()->addMonths(6);
        
        $xml = $this->xmlExporter->createShopFindingsXmlFile($this->collinsShopFindings, $from, $to);
        
        $expectedXmlStructure = '<ATA_InformationSet xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" id="R2009.1" version="1.0" xsi:noNamespaceSchemaLocation="ATA_InformationSet.xsd">';
        
        foreach ($this->collinsShopFindings as $shopFinding) {
            $expectedXmlStructure .= '<ReliabilityData>';
            $expectedXmlStructure .= '<ShopFindings version="2.00">';
            
            if ($shopFinding->HDR_Segment) {
                
                $rawData[$shopFinding->id]['HDR_Segment'] = $shopFinding->HDR_Segment->toArray();
                
                $expectedXmlStructure .= '<HDR_Segment>';
                
                $headerData = [];
                
                foreach ($shopFinding->HDR_Segment->getAttributes() as $key => $val) {
                    
                    $methodName = $shopFinding->HDR_Segment->getPrefix().$key;
                    
                    if (method_exists($shopFinding->HDR_Segment, $methodName) && mb_strlen(trim($val))) {
                        $headerData[$key] = $val;
                    }
                }
                
                $headerData = array_slice($headerData, 0, 2, true) +
                ["RDT" => 'dummydata', 'RSD' => 'dummydata'] +
                array_slice($headerData, 2, count($headerData) - 2, true);
                
                foreach ($headerData as $key => $value) {
                    $expectedXmlStructure .= "<$key/>";
                }
                
                $expectedXmlStructure .= '</HDR_Segment>';
            }
            
            if ($shopFinding->shopFindingsDetail) {
                $expectedXmlStructure .= '<ShopFindingsDetails>';
                
                if ($shopFinding->ShopFindingsDetail->RCS_Segment) {
                    
                    $rawData[$shopFinding->id]['RCS_Segment'] = $shopFinding->ShopFindingsDetail->RCS_Segment->toArray();
                    
                    $expectedXmlStructure .= '<RCS_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->RCS_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->RCS_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->RCS_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    // Collins Parts will always have the REM attribute in the RCS_Segment.
                    if (!in_array('REM', $shopFinding->ShopFindingsDetail->RCS_Segment->getAttributes())) {
                        $expectedXmlStructure .= "<REM/>";
                    }
                    
                    $expectedXmlStructure .= '</RCS_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->SAS_Segment) {
                    
                    $rawData[$shopFinding->id]['SAS_Segment'] = $shopFinding->ShopFindingsDetail->SAS_Segment->toArray();
                    
                    $expectedXmlStructure .= '<SAS_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->SAS_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->SAS_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->SAS_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</SAS_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->SUS_Segment) {
                    
                    $rawData[$shopFinding->id]['SUS_Segment'] = $shopFinding->ShopFindingsDetail->SUS_Segment->toArray();
                    
                    $expectedXmlStructure .= '<SUS_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->SUS_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->SUS_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->SUS_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</SUS_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->RLS_Segment) {
                    
                    $rawData[$shopFinding->id]['RLS_Segment'] = $shopFinding->ShopFindingsDetail->RLS_Segment->toArray();
                    
                    $expectedXmlStructure .= '<RLS_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->RLS_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->RLS_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->RLS_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</RLS_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->LNK_Segment) {
                    
                    $rawData[$shopFinding->id]['LNK_Segment'] = $shopFinding->ShopFindingsDetail->LNK_Segment->toArray();
                    
                    $expectedXmlStructure .= '<LNK_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->LNK_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->LNK_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->LNK_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</LNK_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->AID_Segment) {
                    
                    $rawData[$shopFinding->id]['AID_Segment'] = $shopFinding->ShopFindingsDetail->AID_Segment->toArray();
                    
                    $expectedXmlStructure .= '<AID_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->AID_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->AID_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->AID_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</AID_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->EID_Segment) {
                    
                    $rawData[$shopFinding->id]['EID_Segment'] = $shopFinding->ShopFindingsDetail->EID_Segment->toArray();
                    
                    $expectedXmlStructure .= '<EID_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->EID_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->EID_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->EID_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</EID_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->API_Segment) {
                    
                    $rawData[$shopFinding->id]['API_Segment'] = $shopFinding->ShopFindingsDetail->API_Segment->toArray();
                    
                    $expectedXmlStructure .= '<API_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->API_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->API_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->API_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</API_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->ATT_Segment) {
                    
                    $rawData[$shopFinding->id]['ATT_Segment'] = $shopFinding->ShopFindingsDetail->ATT_Segment->toArray();
                    
                    $expectedXmlStructure .= '<ATT_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->ATT_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->ATT_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->ATT_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</ATT_Segment>';
                }
                
                if ($shopFinding->ShopFindingsDetail->SPT_Segment) {
                    
                    $rawData[$shopFinding->id]['SPT_Segment'] = $shopFinding->ShopFindingsDetail->SPT_Segment->toArray();
                    
                    $expectedXmlStructure .= '<SPT_Segment>';
                    
                    foreach ($shopFinding->ShopFindingsDetail->SPT_Segment->getAttributes() as $key => $val) {
                        
                        $methodName = $shopFinding->ShopFindingsDetail->SPT_Segment->getPrefix().$key;
                        
                        if (method_exists($shopFinding->ShopFindingsDetail->SPT_Segment, $methodName) && mb_strlen(trim($val))) {
                            $expectedXmlStructure .= "<$key/>";
                        }
                    }
                    
                    $expectedXmlStructure .= '</SPT_Segment>';
                }
                
                $expectedXmlStructure .= '</ShopFindingsDetails>';
            }
            
            $expectedXmlStructure .= '</ShopFindings>';
            $expectedXmlStructure .= '</ReliabilityData>';
        }
        
        $expectedXmlStructure .= '</ATA_InformationSet>';
        
        $expected = new \DOMDocument;
        $expected->loadXML($expectedXmlStructure);
        
        $actual = new \DOMDocument;
        $actual->loadXML($xml);
        
        $dump = '<pre>' . print_r(['raw' => $rawData, 'expected' => $expectedXmlStructure, 'actual' => $xml]) . '</pre>';

        $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, true, $dump);
    }
    
    /**
     * Recursively check that an array doesn't have any empty or null values.
     *
     * @return void
     */
    private function assertAllArrayValuesAreFilled(array $array)
    {
        foreach ($array as $key => $value) {
            
            if (is_array($value)) {
                $this->assertAllArrayValuesAreFilled($value);
            } else {
                $this->assertNotNull($value, "$key: $value - ".json_encode($array));
                
                // 0 is allowed but seen as empty so we check for string length instead.
                $this->assertNotEmpty(mb_strlen(trim($value)), "$key: $value - ".json_encode($array));
            }
        }
    }
}