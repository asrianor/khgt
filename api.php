<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'scraper.php';

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y') - 579;
// Default to 1448 if not provided, just for testing as per prompt context
if (!isset($_GET['year'])) {
    $year = 1448;
}

$scraper = new HijriScraper();
$result = $scraper->getHtmlAndParse($year);

echo json_encode($result, JSON_PRETTY_PRINT);
