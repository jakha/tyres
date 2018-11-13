<?php

include_once(ROOT_PATH . "/public_html/wordpress/wp-includes/wp-db.php");
include_once(ROOT_PATH . "/public_html/wordpress/wp-load.php");

define('DEFAULT_TRANSTYPE','Выберите тип транспорта');
define('DEFAULT_WHEELDIAM', 'Выберите диаметр колеса');
define('DEFAULT_TYRESIZE','Выберите размер шины');
define('ANY', 'Любой');

function selectTransportTypeList()
{
    global $wpdb;
    $query = "SELECT * FROM transport_type";
    $transList = $wpdb->get_results($query);
    $selectStr = '<li class="option" value="' . DEFAULT_TRANSTYPE . '">' . ANY . '</li>';
    foreach($transList as $value)
    {
        $selectStr .= '<li class="option" value="' . $value->id . '">' .                      $value->transport_name . '</li>';
    }
    return $selectStr;
}

function selectWheelDiameterList($transType = null)
{
    global $wpdb;
    $query = "SELECT DISTINCT * FROM wheel_diameter";
    if($transType)
    {
        $query = sprintf("SELECT DISTINCT wheel_diameter.id, wheel_diameter.size
                FROM catalog
                CROSS JOIN wheel_diameter ON (wheel_diameter.id = catalog.wheel_diameter)
                WHERE catalog.transport_type = %d", $transType);
    }
    $diamList = $wpdb->get_results($query);
    $selectStr = '<li class="option" value="' . DEFAULT_WHEELDIAM . '">' . ANY . '</li>';
    foreach($diamList as $value)
    {
        $selectStr .= '<li class="option" value="' . $value->id . '">' .
        $value->size . '</li>';
    }
    return $selectStr;
}

function selectTyreSizeList($transType = null, $wheelDiam = null)
{
    global $wpdb;
    $query = "SELECT DISTINCT id, standard_size FROM catalog";

    $where = '';
    if($transType)
    {
        $where .= sprintf($where . " transport_type = %d", $transType);
    }

    if($wheelDiam)
    {
        if($where != '')
            $where .= " AND ";
        $where = sprintf($where . " wheel_diameter = %d ", $wheelDiam);
    }

    if($where != '')
        $query .= " WHERE " . $where;

    $query .= " ORDER BY standard_size";
    $tyreList = $wpdb->get_results($query);
    $selectStr = '<li class="option" value="' . DEFAULT_TYRESIZE . '">' . ANY . '</li>';
    foreach($tyreList as $value)
    {
        $selectStr .= '<li class="option" value="' . $value->id . '">' .                      $value->standard_size . '</li>';
    }
    return $selectStr;
}

function getCatalogBy($options)
{
    global $wpdb;
    $query = "SELECT tread_pattern
                FROM catalog";
    $whereStr = '';
    if($options['tyreSize'])
    {
        $whereStr = sprintf($whereStr . " catalog.id = %d ", $options['tyreSize']);
    }

    if($options['wheelDiam'])
    {
        if($whereStr != '')
            $whereStr .= " AND ";
        $whereStr = sprintf($whereStr . " catalog.wheel_diameter = %d ", $options['wheelDiam']);
    }

    if($options['transType'])
    {
        if($whereStr != '')
            $whereStr .= " AND ";
        $whereStr = sprintf($whereStr . " catalog.transport_type = %d ", $options['transType']);
    }

    if($whereStr != '')
        $query .= " WHERE " . $whereStr;

    $pattList = $wpdb->get_results($query, 'ARRAY_A');
    $patterns ='';
    foreach($pattList as $val)
    {
        $patterns .= $val['tread_pattern'] . ",";
    }
    $patterns = trim($patterns, ",");
    $query = "SELECT transport_type.transport_name, wheel_diameter.size,
                    catalog.standard_size, tread_pattern.tread_pattern_name,
                    tread_pattern.description, catalog.code_tra, catalog.strength_index
                FROM catalog
                CROSS JOIN wheel_diameter ON (wheel_diameter.id = catalog.wheel_diameter)
                CROSS JOIN transport_type ON (transport_type.id = catalog.transport_type)
                CROSS JOIN tread_pattern ON (tread_pattern.id = catalog.tread_pattern)
                WHERE catalog.tread_pattern IN(" . $patterns . ")
                ORDER BY tread_pattern.tread_pattern_name";
    $tyreList = $wpdb->get_results($query, 'ARRAY_A');
    $catalog = groupByTreadPatternName($tyreList);
    return $catalog;
}

function groupByTreadPatternName($array)
{
    $items = [];
    foreach ($array as $value) {
        $items[$value["tread_pattern_name"]]['description'] = $value['description'];
        $items[$value["tread_pattern_name"]]['sizes'][] = $value;
    }
    return $items;
}

function selectTransTypeNameById($id)
{
    global $wpdb;
    $query = "SELECT transport_name FROM transport_type WHERE id = " . $id;
    $transTypeName = $wpdb->get_results($query, "ARRAY_A");
    return $transTypeName[0]['transport_name'];
}

function selectWheelDiamNameById($id)
{
    global $wpdb;
    $query = "SELECT size FROM wheel_diameter WHERE id = " . $id;
    $wheelDiamName = $wpdb->get_results($query, "ARRAY_A");
    return $wheelDiamName[0]['size'];
}

function selectTyreSizeNameById($id)
{
    global $wpdb;
    $query = "SELECT standard_size FROM catalog WHERE id = " . $id;
    $transTypeName = $wpdb->get_results($query, "ARRAY_A");
    return $transTypeName[0]['standard_size'];
}

function getTyreBy($tread)
{
    global $wpdb;
    $query = "SELECT * FROM catalog
                CROSS JOIN tread_pattern ON (tread_pattern.id = catalog.tread_pattern)
                CROSS JOIN tread_char ON (tread_char.tread_pattern = catalog.tread_pattern)
                WHERE tread_pattern.tread_pattern_name = '" . $tread . "'";
    $tyres = $wpdb->get_results($query, "ARRAY_A");

    $typeSize = [];
    $chars = [];

    foreach($tyres as $tyre)
    {
        $typeSize[$tyre['standard_size']] =  $tyre;
        $chars[$tyre['headline']] =  $tyre;
    }
    $treads['name'] = $tyres[0]['tread_pattern_name'];
    $treads['description'] = $tyres[0]['description'];
    $treads['sizes'] = $typeSize;
    $treads['chars'] = $chars;
    return $treads;
}

function recordUserAction($mail, $phone = '', $name = '', $pdfName = "")
{
    global $wpdb;
    $timestamp = date("Y-m-d H:i:s");
    $data = [
        'email' => $mail,
        'phone' => $phone,
        'user_name' => $name,
        'file_name' => $pdfName,
        'timestamp' => $timestamp
    ];
    $wpdb->insert('customer_data', $data);
}
