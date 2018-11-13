<?php
require_once APPLICATION_PATH . "/models/Filters.php";

if(!empty($param))
{
    require_once APPLICATION_PATH . "/controllers/TyreController.php";
}
else
{
    $transType = $_GET['transType'];
    $wheelDiam = $_GET["wheelDiam"];
    $tyreSize = $_GET["tyreSize"];

    $currentTransType = ['text' => DEFAULT_TRANSTYPE,
                            'value' => ''];
    $currentWheelDiam = ['text' => DEFAULT_WHEELDIAM,
                            'value' => ''];
    $currentTyreSize = ['text' => DEFAULT_TYRESIZE,
                            'value' => ''];
    if(!empty($transType))
    {
        $currentTransType['text'] = selectTransTypeNameById($transType);
        $currentTransType['value'] = $transType;
    }
    if(!empty($wheelDiam))
    {
        $currentWheelDiam['text'] = selectWheelDiamNameById($wheelDiam);
        $currentWheelDiam['value'] = $wheelDiam;
    }
    if(!empty($tyreSize))
    {
        $currentTyreSize['text'] = selectTyreSizeNameById($tyreSize);
        $currentTyreSize['value'] = $tyreSize;
    }

    $transStr = selectTransportTypeList();
    $diamStr = selectWheelDiameterList($transType);
    $tyreStr = selectTyreSizeList($transType,$wheelDiam);

    $options = ['transType'=>$transType,'wheelDiam' => $wheelDiam, 'tyreSize' => $tyreSize];
    $catalog = getCatalogBy($options);

    require_once APPLICATION_PATH . "/views/catalog.phtml";
}