<?php
/**
 * Report class.
 */
namespace Zeedhi\Report;

/**
 * Description of Report
 *
 * @author tuliopinto
 */
class LogoConfig {

    private $clientLogo;
    private $enterpriseLogo;
    private $productLogo;

    public function getClientLogo() {
        return $this->clientLogo;
    }

    public function setClientLogo($clientLogo) {
        $this->clientLogo = $clientLogo;
    }

    public function getEnterpriseLogo() {
        return $this->enterpriseLogo;
    }

    public function setEnterpriseLogo($enterpriseLogo) {
        $this->enterpriseLogo = $enterpriseLogo;
    }

    public function getProductLogo() {
        return $this->productLogo;
    }

    public function setProductLogo($productLogo) {
        $this->productLogo = $productLogo;
    }

}