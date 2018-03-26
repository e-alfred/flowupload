<?php

$l = \OC::$server->getL10N('flowupload');
$g = \OC::$server->getURLGenerator();

\OC::$server->getNavigationManager()->add(array(
        'id'    => 'flowupload',
        'order' => 74,
        'href' => $g->linkToRoute('flowupload_index'),
        'icon' => $g->imagePath('flowupload', 'flowupload.svg'),
        'name' => 'Flowupload'
));
