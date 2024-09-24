<?php

namespace App\Interfaces;

use App\Interfaces\HeaderInterface;

interface Spec2000ReportInterface extends HeaderInterface
{
    /**
     * Get the Shop Findings Record Identifier.
     *
     * @return integer
     */
    public function get_RCS_ShopFindingsRecordIdentifier();
    
    /**
     * Get the author id.
     *
     * @return string
     */
    public function get_ATA_AuthorId();
    
    /**
     * Get the author version.
     *
     * @return integer
     */
    public function get_ATA_AuthorVersion();
    
    /**
     * Get the shop findings version.
     *
     * @return integer
     */
    public function get_SF_Version();
    
    /**
     * Get the shop received date.
     *
     * @return date
     */
    public function get_RCS_ShopReceivedDate();
    
    /**
     * Get the Received Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RCS_ReceivedManufacturerFullLengthPartNumber();
    
    /**
     * Get the Received Part Manufacturer Code.
     *
     * @return string
     */
    public function get_RCS_ReceivedPartManufacturerCode();
    
    /**
     * Get the Received Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_RCS_ReceivedManufacturerSerialNumber();
    
    /**
     * Get the Comment Text.
     *
     * @return string
     */
    public function get_RCS_CommentText();
    
    /**
     * Get the Supplier Removal Type Code.
     *
     * @return string
     */
    public function get_RCS_SupplierRemovalTypeCode();
    
    /**
     * Get the Failure/Fault Found.
     *
     * @return string
     */
    public function get_RCS_FailureFaultFound();
    
    /**
     * Get the Failure/Fault Induced.
     *
     * @return string
     */
    public function get_RCS_FailureFaultInduced();
    
    /**
     * Get the Failure/Fault Confirms Reason For Removal.
     *
     * @return string
     */
    public function get_RCS_FailureFaultConfirmsReasonForRemoval();
    
    /**
     * Get the Failure/Fault Confirms Aircraft Message.
     *
     * @return string
     */
    public function get_RCS_FailureFaultConfirmsAircraftMessage();
    
    /**
     * Get the Failure/Fault Confirms Aircraft Part Bite Message.
     *
     * @return string
     */
    public function get_RCS_FailureFaultConfirmsAircraftPartBiteMessage();
    
    /**
     * Get the Hardware/Software Failure.
     *
     * @return string
     */
    public function get_RCS_HardwareSoftwareFailure();
    
    /**
     * Get the Shop Action Text Incoming.
     *
     * @return string
     */
    public function get_SAS_ShopActionTextIncoming();
    
    /**
     * Get the Shop Repair Location Code.
     *
     * @return string
     */
    public function get_SAS_ShopRepairLocationCode();
    
    /**
     * Get the Shop Final Action Indicator.
     *
     * @return boolean
     */
    public function get_SAS_ShopFinalActionIndicator();
    
    /**
     * Get the Shop Action Code.
     *
     * @return string
     */
    public function get_SAS_ShopActionCode();
    
    /**
     * Get the Airframe Manufacturer Code.
     *
     * @return string
     */
    public function get_AID_AirframeManufacturerCode();
    
    /**
     * Get the Airframe Manufacturer Name.
     *
     * @return string
     */
    public function get_AID_AirframeManufacturerName();
    
    /**
     * Get the Aircraft Model.
     *
     * @return string
     */
    public function get_AID_AircraftModel();
    
    /**
     * Get the Aircraft Series.
     *
     * @return string
     */
    public function get_AID_AircraftSeries();
    
    /**
     * Get the Aircraft Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_AID_AircraftManufacturerSerialNumber();
    
    /**
     * Get the Aircraft Registration Number.
     *
     * @return string
     */
    public function get_AID_AircraftRegistrationNumber();
    
    /**
     * Get the Operator Aircraft Internal Identifier.
     *
     * @return string
     */
    public function get_AID_OperatorAircraftInternalIdentifier();
    
    /**
     * Get the Shipped Date.
     *
     * @return date
     */
    public function get_SUS_ShippedDate();
    
    /**
     * Get the Shipped Part Manufacturer Code.
     *
     * @return string
     */
    public function get_SUS_ShippedPartManufacturerCode();
    
    /**
     * Get the Shipped Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_SUS_ShippedManufacturerFullLengthPartNumber();
    
    /**
     * Get the Shipped Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_SUS_ShippedManufacturerSerialNumber();
    
    /**
     * Get the Shipped Part Manufacturer Name.
     *
     * @return string
     */
    public function get_SUS_ShippedPartManufacturerName();
    
    /**
     * Get the Shipped Manufacturer Part Description.
     *
     * @return string
     */
    public function get_SUS_ShippedManufacturerPartDescription();
    
    /**
     * Get the Shipped Manufacturer Part Number.
     *
     * @return string
     */
    public function get_SUS_ShippedManufacturerPartNumber();
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_SUS_OverlengthPartNumber();
    
    /**
     * Get the Shipped Universal Serial Number.
     *
     * @return string
     */
    public function get_SUS_ShippedUniversalSerialNumber();
    
    /**
     * Get the Shipped Operator Part Number.
     *
     * @return string
     */
    public function get_SUS_ShippedOperatorPartNumber();
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_SUS_SupplierCode();
    
    /**
     * Get the Shipped Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_SUS_ShippedUniversalSerialTrackingNumber();
    
    /**
     * Get the Shipped Part Modification Level.
     *
     * @return string
     */
    public function get_SUS_ShippedPartModificationLevel();
    
    /**
     * Get the Shipped Part Status Code.
     *
     * @return string
     */
    public function get_SUS_ShippedPartStatusCode();
    
    /**
     * Get the Removed Part Manufacturer Code.
     *
     * @return string
     */
    public function get_RLS_RemovedPartManufacturerCode();
    
    /**
     * Get the Removed Manufacturer Full Length Part Number.
     *
     * @return string
     */
    public function get_RLS_RemovedManufacturerFullLengthPartNumber();
    
    /**
     * Get the Removed Manufacturer Serial Number.
     *
     * @return string
     */
    public function get_RLS_RemovedManufacturerSerialNumber();
    
    /**
     * Get the Removal Date.
     *
     * @return date
     */
    public function get_RLS_RemovalDate();
    
    /**
     * Get the Removal Type Code.
     *
     * @return string
     */
    public function get_RLS_RemovalTypeCode();
    
    /**
     * Get the Removal Type Text.
     *
     * @return string
     */
    public function get_RLS_RemovalTypeText();
    
    /**
     * Get the Install Date of Removed Part.
     *
     * @return date
     */
    public function get_RLS_InstallDateOfRemovedPart();
    
    /**
     * Get the Removed Part Manufacturer Name.
     *
     * @return string
     */
    public function get_RLS_RemovedPartManufacturerName();
    
    /**
     * Get the Removed Manufacturer Part Number.
     *
     * @return string
     */
    public function get_RLS_RemovedManufacturerPartNumber();
    
    /**
     * Get the Overlength Part Number.
     *
     * @return string
     */
    public function get_RLS_OverlengthPartNumber();
    
    /**
     * Get the Removed Universal Serial Number.
     *
     * @return string
     */
    public function get_RLS_RemovedUniversalSerialNumber();
    
    /**
     * Get the Removal Reason Text.
     *
     * @return string
     */
    public function get_RLS_RemovalReasonText();
    
    /**
     * Get the Engine/APU Position Identifier.
     *
     * @return string
     */
    public function get_RLS_EngineApuPositionIdentifier();
    
    /**
     * Get the Part Position Code.
     *
     * @return string
     */
    public function get_RLS_PartPositionCode();
    
    /**
     * Get the Part Position.
     *
     * @return string
     */
    public function get_RLS_PartPosition();
    
    /**
     * Get the Removed Part Description.
     *
     * @return string
     */
    public function get_RLS_RemovedPartDescription();
    
    /**
     * Get the Removed Part Modification Level.
     *
     * @return string
     */
    public function get_RLS_RemovedPartModificationLevel();
    
    /**
     * Get the Removed Operator Part Number.
     *
     * @return string
     */
    public function get_RLS_RemovedOperatorPartNumber();
    
    /**
     * Get the Removed Operator Serial Number.
     *
     * @return string
     */
    public function get_RLS_RemovedOperatorSerialNumber();
    
    /**
     * Get the Supplier Code.
     *
     * @return string
     */
    public function get_RLS_SupplierCode();
    
    /**
     * Get the Removed Universal Serial Tracking Number.
     *
     * @return string
     */
    public function get_RLS_RemovedUniversalSerialTrackingNumber();
    
    /**
     * Get the Removal Reason Code.
     *
     * @return string
     */
    public function get_RLS_RemovalReasonCode();
}