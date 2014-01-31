<?php
namespace Zeedhi\Security;

use Zeedhi\Application;

class AclDataSecurityProxyInterceptor implements \Zeedhi\Router\Interceptor\SecurityInterceptor{
    private $jsonObject;
    private $userGroupId;

    public function __construct()
    {
        $rootPath = dirname(Application::getInstance()->getBaseUri());
        $aclConfiguration = $rootPath."/../json/acl.json";
        $this->jsonObject = new AclObject($aclConfiguration);
    }

    public function beforeInvoke($route)
    {

    }

    public function afterInvoke($request, $response)
    {
        $this->processAclRules($request, $response);
    }

    private function processAclRules(\Zeedhi\Request\Request $request, \Zeedhi\DTO\Response $response)
    {
        $requestHeaders = $request->getAllHeaders();
        $this->userGroupId = $requestHeaders["Usergroup_Id"];

        $params           = $request->getParams();
        $containerName    = $params[\Zeedhi\Request\Request::CONTAINER_NAME_PARAM_KEY];
        $dataSets         = $response->getDataSets();

        // This method will just process the ACL rules for this container if those rules exist.
        if ($this->jsonObject && $this->jsonObject->getContainerAcl($containerName)) {
            foreach ($dataSets as &$dataSet) {
                $widgetName       = $dataSet->getId();
                $contents         = $dataSet->getContent();
                
                if ($contents) {
                    $sampleContents   = $contents[0];
                    $allowedFieldList = $this->getAllowedFieldList($sampleContents, $containerName, $widgetName);
                    $contents         = $this->createContentObject($contents, $allowedFieldList);
                    $dataSet->setContent($contents);
                }
            }
        }

    }

    private function getAllowedFieldList($sampleContents, $containerName, $widgetName)
    {
        $allowedFieldList = null;
        foreach ($sampleContents as $fieldName => $value) {
            $allowedFieldList = $this->getAllowedFieldsList(
                $containerName, $widgetName, $fieldName, $allowedFieldList
            );
        }
        return $allowedFieldList;
    }

    private function createContentObject($contents, $allowedFieldList)
    {
        foreach ($contents as &$content) {
            foreach ($content as $fieldName => $value) {
                if (!$allowedFieldList[$fieldName]) {
                    unset($content[$fieldName]);
                }
            }
        }
        return $contents;
    }

    private function getAllowedFieldsList($containerName, $widgetName, $fieldName, $allowedFieldList)
    {
        $fieldObj = $this->jsonObject->getFieldAcl($containerName, $widgetName, $fieldName);
        if ($fieldObj) {
            $fieldRule = $this->jsonObject->getFieldAclRule($containerName, $widgetName, $fieldName, $this->userGroupId);
            $allowedFieldList[$fieldName] = $fieldRule != "I";
            return $allowedFieldList;
        } else {
            $containerRule = $this->jsonObject->getContainerAclRule($containerName, $this->userGroupId);
            $widgetRule = $this->jsonObject->getWidgetAClRule($containerName, $widgetName, $this->userGroupId);

            $allowedFieldList[$fieldName] = $widgetRule != "I" || ($widgetRule == null && $containerRule != "I");
            return $allowedFieldList;
        }
    }
}