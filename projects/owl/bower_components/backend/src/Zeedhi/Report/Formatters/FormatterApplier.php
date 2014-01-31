<?php
namespace Zeedhi\Report\Formatters;

class FormatterApplier {

    /**
     * Apply all field formatters if exist.
     * 
     * @param string $text
     * @param \Zeedhi\Report\Field $field
     * 
     * @return string
     */
    public function apply($text, \Zeedhi\Report\Field $field) {
        if ($field->getFormatterClass()) {
            $text = $this->executeAllFormatters($text, $field);
        }

        return $text;
    }

    /**
     * Execute all field formatters.
     * 
     * @param string $text
     * @param \Zeedhi\Report\Field $field
     * 
     * @return string
     */
    private function executeAllFormatters($text, \Zeedhi\Report\Field $field) {
        $formatterClass = $field->getFormatterClass();
        $params = $field->getFormatterParams();

        if (class_exists($formatterClass)) {
            $formatterObj = new $formatterClass;
            $text = $formatterObj->apply($text, $params);
        }

        return $text;
    }

}