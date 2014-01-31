<?php

namespace Zeedhi\Report;

class Report 
{

    private $title;
    private $name;
    private $bands;
    private $groups;
    private $clientLogo;
    private $enterpriseLogo;
    private $productLogo;
    private $issuanceDate;
    private $version;
    private $numColumns;

    const DATE_FORMAT = "d/m/Y";
    const HOUR_FORMAT = "H:i:s";

    public function __construct() 
    {
        $this->setIssuanceDate(new \DateTime());
    }

    public function setName($name) 
    {
        $this->name = $name;
    }

    public function getName() 
    {
        return $this->name;
    }

    public function setTitle($title) 
    {
        $this->title = $title;
    }

    public function getTitle() 
    {
        return $this->title;
    }

    public function setBands($bands) 
    {
        $this->bands = $bands;
    }

    public function getBands() 
    {
        return $this->bands;
    }

    public function setClientLogo($logo) 
    {
        $this->clientLogo = $logo;
    }

    public function getClientLogo() 
    {
        return $this->clientLogo;
    }

    public function setEnterpriseLogo($logo) 
    {
        $this->enterpriseLogo = $logo;
    }

    public function getEnterpriseLogo() 
    {
        return $this->enterpriseLogo;
    }

    public function setProductLogo($logo) 
    {
        $this->productLogo = $logo;
    }

    public function getProductLogo() 
    {
        return $this->productLogo;
    }

    public function setIssuanceDate($date) 
    {
        $this->date = $date;
    }

    public function getIssuanceDate() 
    {
        return $this->date->format(self::DATE_FORMAT);
    }

    public function getIssuanceHour() 
    {
        return $this->date->format(self::HOUR_FORMAT);
    }

    public function setVersion($version) 
    {
        $this->version = $version;
    }

    public function getVersion() 
    {
        return $this->version;
    }

    public function getGroups() {
        return $this->groups;
    }

    public function setGroups($groups) {
        $this->groups = $groups;
    }

    public function getNumColumns() {
        return $this->numColumns;
    }

    public function setNumColumns($numColumns) {
        $this->numColumns = $numColumns;
    }

}