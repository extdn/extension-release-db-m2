<?php
//TODO call this as pre-commit hook
require 'vendor/autoload.php';

$dbWriter = new \Extdn\ExtensionReleaseDb\ProcessChangelogsToDb();
$dbWriter->execute();
