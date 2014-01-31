<?php
namespace Zeedhi\Data\Doctrine;

/**
 * Created by JetBrains PhpStorm.
 * User: paulopereira
 * Date: 24/07/13
 * Time: 12:31
 * To change this template use File | Settings | File Templates.
 */
class TypeValidator
{
    private $classMetadata;

    public function __construct(\Doctrine\ORM\Mapping\ClassMetadata $classMetadata)
    {
        $this->classMetadata = $classMetadata;
    }

    public function getTypifiedField($fieldName, $value)
    {
        $type = $this->classMetadata->getTypeOfField(\Doctrine\Common\Util\Inflector::camelize(strtolower($fieldName)));

        if ($type == \Doctrine\DBAL\Types\Type::DATETIME) {
            return $this->prepareDateTimeObject($value);
        }

        return $value;
    }

    public function prepareDateTimeObject($value)
    {
        if ((bool)$value) {
            if (strlen($value) < 15 ) {
                $value .= " 00:00:00";
            }
            return \DateTime::createFromFormat("d/m/Y H:i:s", $value);
        }

        return null;
    }

}
