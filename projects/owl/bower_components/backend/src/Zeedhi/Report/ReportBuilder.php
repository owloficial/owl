<?php
namespace Zeedhi\Report;

use \Zeedhi\Report\Report;
use \Zeedhi\Report\LogoConfig;
use \Zeedhi\Report\Reader\IReader;

class ReportBuilder 
{

    private $reader;
    private $targetFile;
    private $templatePath;
    private $cachePath;
    private $templateName = 'report.php.twig';
    private $logoConfig;

    public function __construct(IReader $reader, LogoConfig $logoConfig, $templatePath, $cachePath, $targetFile) 
    {
        $this->reader = $reader;
        $this->logoConfig = $logoConfig;
        $this->targetFile = $targetFile;
        $this->templatePath = $templatePath;
        $this->cachePath = $cachePath;
    }

    public function buildReport() 
    {
        $reader = $this->getReader();
        $report = $reader->read();
        $this->buildFile($report);
    }

    protected function buildFile(Report $report) {
	$template = $this->createTemplateEngine();
	$html = $template->render(array("report" => $report, "logo" => $this->getLogoConfig()));
	file_put_contents($this->getTargetFile(), $html);
    }

    protected function createTemplateEngine() {
        \Twig_Autoloader::register();
	$loader = new \Twig_Loader_Filesystem($this->getTemplatePath());
	$twig   = new \Twig_Environment($loader, array(/*'cache' => $this->getCachePath() */));
	$twig->addGlobal('formatterApplier', new \Zeedhi\Report\Formatters\FormatterApplier());
	$template = $twig->loadTemplate($this->getTemplateName());
        return $template;
    }

    public function getReader() {
        return $this->reader;
    }

    public function getTemplatePath() {
        return $this->templatePath;
    }

    public function getCachePath() {
        return $this->cachePath;
    }

    public function getTemplateName() {
        return $this->templateName;
    }

    public function getTargetFile() {
        return $this->targetFile;
    }

    public function getLogoConfig() {
        return $this->logoConfig;
    }

    public function setLogoConfig($logoConfig) {
        $this->logoConfig = $logoConfig;
    }

}