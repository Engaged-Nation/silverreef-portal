<?php

use EN\PortalCore\Acl\Client as AclClient;

/**
 * ACL
 */
AclClient::addPublicPaths(
    [
        '/embed/fzone/contents/includes/scratchcard.morongo/iframe.php',
        '/embed/fzone/contents/includes/e_wordsearch/iframe.php',
        '/embed/fzone/contents/includes/wordsearch/iframe.php'
    ]
);

AclClient::removePublicPaths(
    [

    ]
);


/**
 * Portal Overrides
 */
//ENUserFactory::setClient(new ENUserClient);
