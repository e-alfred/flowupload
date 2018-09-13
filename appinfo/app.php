<?php

\OC::$server->getNavigationManager()->add(array(
        'id'    => 'flowupload',
        'order' => 74,
        'href' => OC::$server->getURLGenerator()->linkToRoute('flowupload_index'),
        'icon' => OC::$server->getURLGenerator()->imagePath('flowupload', 'flowupload.svg'),
        'name' => 'Flowupload'
));
