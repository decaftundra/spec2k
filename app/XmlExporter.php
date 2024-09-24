<?php

namespace App;

use Carbon\Carbon;
use App\ValidationProfiler;
use Illuminate\Support\Str;
use App\PieceParts\PiecePart;
use App\ShopFindings\ShopFinding;
use Illuminate\Database\Eloquent\Collection;
use Madnest\Madzipper\Madzipper;

class XmlExporter
{
    public $ataId = 'R2009.1';
    public $ataVersion = '1.0';
    public $SFVersion = '2.00';
    public $PPVersion = '1.00';
    
    /**
     * Export the header data array.
     *
     * @param  \App\ShopFinding  $shopfinding
     * @return array  $data
     */
    public function exportHeader(ShopFinding $shopfinding) {
        if ($shopfinding->HDR_Segment) {
            $profile = new ValidationProfiler('HDR_Segment', $shopfinding->HDR_Segment, $shopfinding->id);
            return $profile->export();
        }
        
        return [];
    }
    
    /**
     * Export the shop finding data array.
     *
     * @param  \App\ShopFinding  $shopfinding
     * @return array  $data
     */
    public function exportShopFinding(ShopFinding $shopfinding) {
        
        $data = [];
        
        if ($shopfinding->ShopFindingsDetail) {
        
            if ($shopfinding->ShopFindingsDetail->RCS_Segment) {
                $profile = new ValidationProfiler('RCS_Segment', $shopfinding->ShopFindingsDetail->RCS_Segment, $shopfinding->id);
                $data['RCS_Segment'] = $profile->export();
            }
            
            if ($shopfinding->ShopFindingsDetail->SAS_Segment) {
                $profile = new ValidationProfiler('SAS_Segment', $shopfinding->ShopFindingsDetail->SAS_Segment, $shopfinding->id);
                $data['SAS_Segment'] = $profile->export();
            }
            
            if ($shopfinding->ShopFindingsDetail->SUS_Segment) {
                $profile = new ValidationProfiler('SUS_Segment', $shopfinding->ShopFindingsDetail->SUS_Segment, $shopfinding->id);
                $data['SUS_Segment'] = $profile->export();
            }
            
            if ($shopfinding->ShopFindingsDetail->RLS_Segment) {
                $profile = new ValidationProfiler('RLS_Segment', $shopfinding->ShopFindingsDetail->RLS_Segment, $shopfinding->id);
                $data['RLS_Segment'] = $profile->export();
            }
            
            if ($shopfinding->ShopFindingsDetail->LNK_Segment) {
                $profile = new ValidationProfiler('LNK_Segment', $shopfinding->ShopFindingsDetail->LNK_Segment, $shopfinding->id);
                $data['LNK_Segment'] = $profile->export();
            }
            
            if ($shopfinding->ShopFindingsDetail->AID_Segment) {
                $profile = new ValidationProfiler('AID_Segment', $shopfinding->ShopFindingsDetail->AID_Segment, $shopfinding->id);
                $data['AID_Segment'] = $profile->export();
            }
            
            if ($shopfinding->ShopFindingsDetail->EID_Segment) {
                $profile = new ValidationProfiler('EID_Segment', $shopfinding->ShopFindingsDetail->EID_Segment, $shopfinding->id);
                $data['EID_Segment'] = $profile->export();
            }
            
            if ($shopfinding->ShopFindingsDetail->API_Segment) {
                $profile = new ValidationProfiler('API_Segment', $shopfinding->ShopFindingsDetail->API_Segment, $shopfinding->id);
                $data['API_Segment'] = $profile->export();
            }
            
            if ($shopfinding->ShopFindingsDetail->ATT_Segment) {
                $profile = new ValidationProfiler('ATT_Segment', $shopfinding->ShopFindingsDetail->ATT_Segment, $shopfinding->id);
                $data['ATT_Segment'] = $profile->export();
            }
            
            if ($shopfinding->ShopFindingsDetail->SPT_Segment) {
                $profile = new ValidationProfiler('SPT_Segment', $shopfinding->ShopFindingsDetail->SPT_Segment, $shopfinding->id);
                $data['SPT_Segment'] = $profile->export();
            }
        }
        
        return $data;
    }
    
    /**
     * Export the piece parts data array.
     *
     * @param  \App\PieceParts\PiecePart  $piecePart
     * @param  string  $shopFindingId
     * @return array  $data
     */
    public function exportPiecePart(PiecePart $piecePart, $shopFindingId) {
        
        $data = [];
        
        if ($piecePart->PiecePartDetails) {
            
            foreach($piecePart->PiecePartDetails as $PiecePartDetail) {
                if ($PiecePartDetail->WPS_Segment) {
                    $profile = new ValidationProfiler('WPS_Segment', $PiecePartDetail->WPS_Segment, $shopFindingId);
                    $data[$PiecePartDetail->id]['WPS_Segment'] = $profile->export();
                }
                
                if ($PiecePartDetail->NHS_Segment) {
                    $profile = new ValidationProfiler('NHS_Segment', $PiecePartDetail->NHS_Segment, $shopFindingId);
                    $data[$PiecePartDetail->id]['NHS_Segment'] = $profile->export();
                }
                
                if ($PiecePartDetail->RPS_Segment) {
                    $profile = new ValidationProfiler('RPS_Segment', $PiecePartDetail->RPS_Segment, $shopFindingId);
                    $data[$PiecePartDetail->id]['RPS_Segment'] = $profile->export();
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Create the Shop Findings XML file content.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $allRecords
     * @param  \Carbon\Carbon  $from
     * @param  \Carbon\Carbon  $to
     * @return string
     */
    public function createShopFindingsXmlFile(Collection $allRecords, Carbon $from, Carbon $to)
    {
        $sfxml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><ATA_InformationSet/>');
        $sfxml->addAttribute('id', $this->ataId);
        $sfxml->addAttribute('version', $this->ataVersion);
        $sfxml->addAttribute('xsi:noNamespaceSchemaLocation', 'ATA_InformationSet.xsd', 'http://www.w3.org/2001/XMLSchema-instance');
        
        foreach ($allRecords as $shopfinding) {
            $headerData = $this->exportHeader($shopfinding);
            
            /* ADD REPORTING DATE PERIOD TO HEADER. */
            
            // Shop Findings.
            $ReliabilityData = $sfxml->addChild('ReliabilityData');
            
            $ShopFindings = $ReliabilityData->addChild('ShopFindings');
            $ShopFindings->addAttribute('version', $this->SFVersion);
            
            $HDR_Segment = $ShopFindings->addChild('HDR_Segment');
            
            // Add reporting period dates.
            $headerData = array_slice($headerData, 0, 2, true) +
                ["RDT" => $from->format('Y-m-d'), 'RSD' => $to->format('Y-m-d')] +
                array_slice($headerData, 2, count($headerData) - 2, true);
            
            foreach ($headerData as $k => $v) {
                $HDR_Segment->addChild($k, htmlspecialchars($v, ENT_XML1, 'UTF-8'));
            }
            
            $shopFindingData = $this->exportShopFinding($shopfinding);
            
            $shopFindingsDetails = $ShopFindings->addChild('ShopFindingsDetails');
            
            if (count($shopFindingData)) {
                foreach ($shopFindingData as $segment => $data) {
                    $seg = $shopFindingsDetails->addChild($segment);
                    
                    foreach ($data as $k => $v) {
                        $seg->addChild($k, htmlspecialchars($v, ENT_XML1, 'UTF-8'));
                    }
                }
            }
        }
        
        return $this->formatXml($sfxml);
    }
    
    /**
     * Create the Piece Parts XML file content.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $allRecords
     * @param  \Carbon\Carbon  $from
     * @param  \Carbon\Carbon  $to
     * @return string
     */
    public function createPiecePartsXmlFile(Collection $allRecords, Carbon $from, Carbon $to)
    {
        $ppxml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><ATA_InformationSet/>');
        $ppxml->addAttribute('id', $this->ataId);
        $ppxml->addAttribute('version', $this->ataVersion);
        $ppxml->addAttribute('xsi:noNamespaceSchemaLocation', 'ATA_InformationSet.xsd', 'http://www.w3.org/2001/XMLSchema-instance');
        
        foreach ($allRecords as $shopfinding) {
            
            if ($shopfinding->PiecePart) {
                
                $headerData = $this->exportHeader($shopfinding);
            
                // Piece Parts.
                $ReliabilityData = $ppxml->addChild('ReliabilityData');
                
                $pieceParts = $ReliabilityData->addChild('PieceParts');
                $pieceParts->addAttribute('version', $this->PPVersion);
                
                $HDR_Segment = $pieceParts->addChild('HDR_Segment');
                
                // Add reporting period dates.
                $headerData = array_slice($headerData, 0, 2, true) +
                    ["RDT" => $from->format('Y-m-d'), 'RSD' => $to->format('Y-m-d')] +
                    array_slice($headerData, 2, count($headerData) - 2, true);
                
                foreach ($headerData as $k => $v) {
                    $HDR_Segment->addChild($k, htmlspecialchars($v, ENT_XML1, 'UTF-8'));
                }
                
                $piecePartData = $this->exportPiecePart($shopfinding->PiecePart, $shopfinding->id);
                
                if (count($piecePartData)) {
                    foreach ($piecePartData as $key => $ppd) {
                        $piecePartDetails = $pieceParts->addChild('PiecePartDetails');
                        
                        foreach ($ppd as $segment => $data) {
                            $seg = $piecePartDetails->addChild($segment);
                        
                            foreach ($data as $k => $v) {
                                $seg->addChild($k, htmlspecialchars($v, ENT_XML1, 'UTF-8'));
                            }
                        }
                    }
                }
            }
        }
        
        return $this->formatXml($ppxml);
    }
    
    /**
     * Create info file content to accompany xml files in zip.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $allRecords
     * @param  \Carbon\Carbon  $from
     * @param  \Carbon\Carbon  $to
     * @param  string  $encodedString
     * @return string
     */
    public function createInfoTextFile(Collection $allRecords, Carbon $from, Carbon $to, $encodedString)
    {
        $decoded = $this->decodeQueryString($encodedString);
        
        $piecePartsCount = 0;
        $piecePartIds = [];
        $shopFindingIds = [];
        
        foreach ($allRecords as $shopFinding) {
            
            $shopFindingIds[] = $shopFinding->id;
            
            if ($shopFinding->PiecePartDetails) {
                
                $piecePartsCount =  $piecePartsCount + count($shopFinding->PiecePartDetails);
                
                foreach ($shopFinding->PiecePartDetails as $piecePartDetail) {
                    $piecePartIds[] = $piecePartDetail->id;
                }
            }
        }
        
        $text = 'Date of download: ' . Carbon::now() . "\r\n";
        $text .= 'User: ' . $user = auth()->check() ? auth()->user()->fullname . "\r\n" : 'app' . "\r\n";
        $text .= 'Shop Findings: ' . $allRecords->count() .  "\r\n";
        $text .= 'Piece Parts: ' . $piecePartsCount .  "\r\n";
        $text .= 'URL: ' .  route('reports.export') . '?encoded=' . $encodedString . "\r\n\r\n";
        $text .= 'Export Criteria:' . "\r\n";
        
        foreach ($decoded as $k => $v) {
            if ($k == 'status') {
                $text .= 'Status: ' . ucwords(str_replace('_', ' ', implode(', ', $v))) . "\r\n";
            } else if ($k != 'download-zip') {
                $text .= ucwords(str_replace('_', ' ', $k)) . ': ' . $v . "\r\n";
            }
        }
        
        $text .= "\r\n";
        $text .= 'Shop Finding IDs: ' . "\r\n";
        $text .= implode("\r\n", $shopFindingIds) . "\r\n\r\n";
        $text .= 'Piece Part IDs: ' . "\r\n";
        $text .= implode("\r\n", $piecePartIds) . "\r\n\r\n";
        
        return $text;
    }
    
    /**
     * Create the Zip archive file.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $allRecords
     * @param  \Carbon\Carbon  $from
     * @param  \Carbon\Carbon  $to
     * @param  string  $encodedString
     * @return string
     */
    public function createZipArchive(Collection $allRecords, Carbon $from, Carbon $to, $encodedString)
    {
        // File Contents.
        $info = $this->createInfoTextFile($allRecords, $from, $to, $encodedString);
        $sfxml = $this->createShopFindingsXmlFile($allRecords, $from, $to);
        $ppxml = $this->createPiecePartsXmlFile($allRecords, $from, $to);
        
        // Names.
        $archiveDirName = $this->getArchiveDirName();
        $infoFilename = $this->getInfoFilename();
        $sfXmlFilename = $this->getShopFindingsFilename();
        $ppXmlFilename = $this->getPiecePartsFilename();
        
        $storagePath = $this->correctPath(storage_path('app/downloads/' . $archiveDirName . '.zip'));

        // Compile as Zip and save to disk.
        $zipper = new Madzipper;
        $zipper->make($storagePath);
        $zipper->addString($infoFilename, $info);
        $zipper->addString($sfXmlFilename, $sfxml);
        $zipper->addString($ppXmlFilename, $ppxml);
        $zipper->close();
        
        return $storagePath;
    }
    
    /**
     * Get the shop findings xml filename.
     *
     * @return string
     */
    public function getShopFindingsFilename()
    {
        return 'SHOP2K-SH' . Carbon::now()->format('ymd') . '.xml';
    }
    
    /**
     * Get the piece parts xml filename.
     *
     * @return string
     */
    public function getPiecePartsFilename()
    {
        return 'PP2K-PP' . Carbon::now()->format('ymd') . '.xml';
    }
    
    /**
     * Get the info text filename.
     *
     * @return string
     */
    public function getInfoFilename()
    {
        $user = auth()->check() ? auth()->user()->fullname : 'app';
        
        return 'info-file-' . Str::slug($user) . '-' . Carbon::now()->timestamp . '.txt';
    }
    
    /**
     * Get the zip archive filename.
     *
     * @return string
     */
    public function getArchiveDirName()
    {
        $name = auth()->check() ? auth()->user()->fullname : 'app';
        $id = auth()->check() ? auth()->id() : 0;
        
        return 'xml-export-' . Carbon::now()->timestamp . '-' . $id . '-' . Str::slug($name);
    }
    
    /**
     * Format the xml string.
     *
     * @param  \SimpleXMLElement $simpleXMLElement
     * @return string
     */
    public function formatXml(\SimpleXMLElement $simpleXMLElement)
    {
        $xmlDocument = new \DOMDocument('1.0');
        $xmlDocument->preserveWhiteSpace = false;
        $xmlDocument->formatOutput = true;
        $xmlDocument->loadXML($this->utf8_for_xml($simpleXMLElement->asXML()));
    
        return $xmlDocument->saveXML();
    }
    
    /**
     * Fix all occurrences of slashes.
     *
     * @param  string  $path
     * @return string
     */
    public function correctPath($path)
    {
        return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    }
    
    /**
     * Convert form input to encoded string.
     *
     * @param  array  $input
     * @return string
     */
    public function encodeQueryString(array $input)
    {
        if (isset($input['_token'])) unset($input['_token']);
        if (isset($input['download-zip'])) unset($input['download-zip']);
        if (isset($input['download-sf'])) unset($input['download-sf']);
        if (isset($input['download-pp'])) unset($input['download-pp']);
        
        $queryString = http_build_query($input);
        
        return urlencode(base64_encode(gzcompress($queryString)));
    }
    
    /**
     * Convert encoded query string to form input array.
     *
     * @param  string  $queryString
     * @return array  $inputArray
     */
    public function decodeQueryString(string $queryString)
    {
        $string = urldecode($queryString);
        
        $string = str_replace(' ', '+', $string); // urldecode() converts '+' to spaces for some reason.
        
        $uncompressedString = gzuncompress(base64_decode($string));
        
        parse_str($uncompressedString, $inputArray);
        
        return $inputArray;
    }
    
    /**
     * Convert multi-delimited string to array.
     *
     * @param  string  $string
     * @param  array  $delimiters
     * @return array
     */
    public function delimitedToArray(string $string, array $delimiters = [PHP_EOL, ' ', ','])
    {
        return array_filter(array_map('trim', explode(',', str_replace($delimiters, ',', $string))));
    }
    
    /**
     * Strips out UTF-8 characters that are incompatible with XML.
     *
     * @params string $string
     * @return string
     */
    public function utf8_for_xml($string)
    {
        return preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);
    }
}
