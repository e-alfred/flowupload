<?php

\OC::$server->getNavigationManager()->add(array(
	'id'    => 'flowupload',
	'order' => 74,
	'href' => \OCP\Util::linkToRoute('flowupload_index'),
	'icon'  => \OCP\Util::imagePath('flowupload', 'flowupload.svg'),
	'name' => \OC::$server->getL10N('flowupload')->t('FlowUpload')
));
