<?php
$gregorian = "21 Feb"; // Format from API

$todayDay = date('j');
$todayMonthId = array('Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des')[date('n') - 1];
$todayMonthEn = date('M');
$todayPattern1 = $todayDay . ' ' . $todayMonthId;
$todayPattern2 = str_pad($todayDay, 2, '0', STR_PAD_LEFT) . ' ' . $todayMonthId;
$todayPattern3 = $todayDay . ' ' . $todayMonthEn;
$todayPattern4 = str_pad($todayDay, 2, '0', STR_PAD_LEFT) . ' ' . $todayMonthEn;

echo "Target Date: $gregorian\n";
echo "P1: $todayPattern1\n";
echo "P2: $todayPattern2\n";
echo "P3: $todayPattern3\n";
echo "P4: $todayPattern4\n";

$dstr = trim($gregorian);
$isToday = (strpos($dstr, $todayPattern1) !== false || 
            strpos($dstr, $todayPattern2) !== false || 
            strpos($dstr, $todayPattern3) !== false || 
            strpos($dstr, $todayPattern4) !== false);

echo "Is Today? " . ($isToday ? 'YES' : 'NO') . "\n";
?>
