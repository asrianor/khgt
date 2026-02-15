<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'scraper.php';


// Determine Hijri Year
if (isset($_GET['year'])) {
    $year = intval($_GET['year']);
} elseif (isset($_GET['date'])) {
    // Estimate Hijri year from provided Gregorian date
    $timestamp = strtotime($_GET['date']);
    if ($timestamp) {
        $gYear = (int)date('Y', $timestamp);
        $gMonth = (int)date('n', $timestamp);
        // Hijri year roughly starts in mid-Gregorian year.
        // H = G - 579 (Jan-Jun)
        // H = G - 578 (Jul-Dec) roughly
        $year = $gYear - 579;
        if ($gMonth >= 7) {
             $year += 1;
        }
    } else {
        // Fallback if date parse fails
        $year = (int)date('Y') - 579;
        if ((int)date('n') >= 7) $year++;
    }
} else {
    // Default to current date
    $year = (int)date('Y') - 579;
    if ((int)date('n') >= 7) $year++;
}


$month = isset($_GET['month']) ? $_GET['month'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;

$scraper = new HijriScraper();
$result = $scraper->getHtmlAndParse($year);

if ($result['status'] === 'success') {
    if ($month) {
        $filteredData = [];
        foreach ($result['data'] as $m) {
            // Check if month matches (either index+1 or name search)
            // The scraper returns "Muharam 1448 H".
            // Let's support numeric (1-12) if we assume order, or string match.
            // Since Hijri months are standard, index 0 is Muharram.
            
            if (is_numeric($month)) {
                $monthIndex = (int)$month - 1;
                if (isset($result['data'][$monthIndex]) && $result['data'][$monthIndex] === $m) {
                     $filteredData[] = $m;
                }
            } else {
                if (stripos($m['month'], $month) !== false) {
                    $filteredData[] = $m;
                }
            }
        }
        $result['data'] = $filteredData;
    }

    if ($date) {
        $filteredData = [];
        $found = false;
        foreach ($result['data'] as $m) {
            foreach ($m['days'] as $d) {
                // Gregorian date in scraper is "16 Jun". 
                // We need to match with YYYY-MM-DD.
                // This is tricky because scraper doesn't have year in day, only in "period" or implicitly.
                // But we can try to match broadly or improve scraper.
                // Let's stick to string match for now or basic conversion.
                // Actually, the user might want Hijri date or Gregorian date.
                // Let's assume Gregorian "YYYY-MM-DD" input.
                // The scraper output "16 Jun". We know the year is in $year (Hijri) or "Juni 2026" in period.
                
                // Better approach: filter by what we have.
                // If user sends "16 Jun", we match.
                // If user sends "2026-06-16", we need to parse.
                
                // Let's try to match simply first.
                if (stripos($d['gregorian'], $date) !== false || stripos($d['hijri'], $date) !== false) {
                     $filteredData[] = [
                        'month' => $m['month'],
                        'day' => $d
                     ];
                     $found = true;
                }
            }
        }
        if ($found) {
             $result['data'] = $filteredData;
        } else {
             // If strict date format provided (YYYY-MM-DD), try to convert to "d M" format (e.g. 16 Jun)
             $timestamp = strtotime($date);
             if ($timestamp) {
                 $dateFormatted = date('j M', $timestamp); // 16 Jun
                 // Translation for months might be needed if generic locale is not ID.
                 // Indonesian months: Jan, Feb, Mar, Apr, Mei, Jun, Jul, Agt, Sep, Okt, Nov, Des.
                 // PHP 'M' gives English short names.
                 // Simple mapping:
                 $en = ['May', 'Aug', 'Oct', 'Dec'];
                 $id = ['Mei', 'Agt', 'Okt', 'Des'];
                 $dateFormatted = str_replace($en, $id, $dateFormatted);
                 
                 $filteredData = [];
                 foreach ($result['data'] as $m) {
                    foreach ($m['days'] as $d) {
                        if (stripos($d['gregorian'], $dateFormatted) !== false) {
                            $filteredData[] = [
                                'month' => $m['month'],
                                'day' => $d
                            ];
                        }
                    }
                 }
                 $result['data'] = $filteredData;
             }
        }
    }
}

echo json_encode($result, JSON_PRETTY_PRINT);
