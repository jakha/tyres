<?php
    $tread = $param;
    $treads = getTyreBy($tread);
    require_once APPLICATION_PATH . "/views/tyre.phtml";