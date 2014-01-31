<?php
namespace Zeedhi\Security;

/**
 * User: paulopereira
 * Date: 19/07/13
 * Time: 09:21
 * To change this template use File | Settings | File Templates.
 */
class AclObject
{

    private $jsonObject;

    public function __construct($jsonFilePath)
    {
        $this->jsonObject = file_get_contents($jsonFilePath);
        $this->jsonObject  = json_decode($this->jsonObject, true);
    }


    public function getContainerAcl($containerName)
    {
        return isset($this->jsonObject[$containerName]) ? $this->jsonObject[$containerName] : null;
    }

    public function getContainerAclRule($containerName, $userGroup)
    {
        $containerObj = $this->getContainerAcl($containerName);
        return isset($containerObj["userGroups"][$userGroup]["rule"])
            ? $containerObj["userGroups"][$userGroup]["rule"]
            : null;
    }

    public function getWidgetACl($containerName, $widgetName)
    {
        $containerObj = $this->getContainerAcl($containerName);
        return isset($containerObj["children"][$widgetName])
            ? $containerObj["children"][$widgetName]
            : null;
    }

    public function getWidgetAClRule($containerName, $widgetName, $userGroup)
    {
        $widgetObj = $this->getWidgetACl($containerName, $widgetName);
        return isset($widgetObj["userGroups"][$userGroup]["rule"])
            ? $widgetObj["userGroups"][$userGroup]["rule"]
            : null;
    }

    public function getFieldAcl($containerName, $widgetName, $fieldName)
    {
        $widgetObj = $this->getWidgetACl($containerName, $widgetName);
        return isset($widgetObj["children"][$fieldName])
            ? $widgetObj["children"][$fieldName]
            : null;
    }

    public function getFieldAclRule($containerName, $widgetName, $fieldName, $userGroup)
    {
        $fieldObj = $this->getFieldAcl($containerName, $widgetName, $fieldName);
        return isset($fieldObj["userGroups"][$userGroup]["rule"])
            ? $fieldObj["userGroups"][$userGroup]["rule"]
            : null;
    }

}
