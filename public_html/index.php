<?php
    defined('ROOT_PATH')
        || define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
    defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

    $siteSections = json_decode(file_get_contents(APPLICATION_PATH . "/configs/siteSections.json"), true);

    $uri = explode("?", $_SERVER['REQUEST_URI'])[0];
    $param = extractParamFromURL($uri);

    switch ($uri)
    {
        case $siteSections['backoffice']:
            header('Location: '.'../wordpress/wp-admin/index.php');
            exit();
        case $siteSections['catalogue']:
            require_once APPLICATION_PATH . "/views/layouts/tyre-header.phtml";
            require_once APPLICATION_PATH .         "/controllers/CatalogController.php";
            require_once APPLICATION_PATH . "/views/layouts/tyre-footer.phtml";
            break;
        case $siteSections['sendMail']:
            require_once APPLICATION_PATH . "/controllers/MailSendController.php";
            break;
        case $siteSections['callMe']:
            require_once APPLICATION_PATH . "/controllers/CallMeController.php";
            break;
        case $siteSections['company']:
            require_once APPLICATION_PATH . "/views/layouts/tyre-header.phtml";
            require_once APPLICATION_PATH . "/views/about.phtml";
            require_once APPLICATION_PATH . "/views/layouts/tyre-footer.phtml";
            break;
        case $siteSections['contacts']:
            require_once APPLICATION_PATH . "/views/layouts/tyre-header.phtml";
            require_once APPLICATION_PATH . "/views/contact.phtml";
            require_once APPLICATION_PATH . "/views/layouts/tyre-footer.phtml";
            break;
        case $siteSections['privacy']:
            require_once APPLICATION_PATH . "/views/layouts/tyre-header.phtml";
            require_once APPLICATION_PATH . "/views/privacy_policy.phtml";
            require_once APPLICATION_PATH . "/views/layouts/tyre-footer.phtml";
            break;
        default:
            require_once APPLICATION_PATH . "/controllers/IndexController.php";
            break;
    }

    function extractParamFromURL(&$uri)
    {
        $splitedUri = explode("/", $uri);
        global $siteSections;

        if($splitedUri[1] == 'catalogue')
        {
            if(count($splitedUri) == 3 && !empty($splitedUri[2]))
            {
                $uri = $siteSections['catalogue'];
                $param = explode("_", $splitedUri[2]);
                unset($param[0]);
                $param = implode(" ", $param);
            }
        }
        return $param;
    }