<?php
// www/routing.php
if (preg_match('/\.(?:png|jpg|jpeg|gif|rdf|ttl|n3|json|jsonld)$/', $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    include __DIR__ . '/testinfo.php';
    exit;
}