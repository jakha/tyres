<?php
require_once APPLICATION_PATH . "/models/Filters.php";

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
{
    $data = [];
    $transType = $_GET['transType'];
    $wheelDiam = $_GET['wheelDiam'];
    $data['wheelDiamList'] = selectWheelDiameterList($transType);
    $data['tyreSizeList'] = selectTyreSizeList($transType, $wheelDiam);
    header('Content-Type: application/json');
    echo json_encode($data);
    return;
}else {
    require_once APPLICATION_PATH . "/views/layouts/tyre-header.phtml";
    $currentTransType = ['text' => DEFAULT_TRANSTYPE,
                            'value' => ''];
    $currentWheelDiam = ['text' => DEFAULT_WHEELDIAM,
                            'value' => ''];
    $currentTyreSize = ['text' => DEFAULT_TYRESIZE,
                            'value' => ''];
    $diamStr = selectWheelDiameterList();
    $transStr = selectTransportTypeList();
    $tyreStr = selectTyreSizeList();
    require_once APPLICATION_PATH . "/views/index.phtml";
    require_once APPLICATION_PATH . "/views/layouts/tyre-footer.phtml";
}
