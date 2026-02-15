<?php

require_once 'scraper.php';
require_once 'lang.php'; // Load Translations

// Language Configuration
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'id';
if (!array_key_exists($lang, $translations)) $lang = 'id';

$t = $translations[$lang];
$dir = isset($t['dir']) ? $t['dir'] : 'ltr';

// Determine Year
$year = isset($_GET['year']) ? intval($_GET['year']) : (int)date('Y') - 579;
if (!isset($_GET['year']) && (int)date('n') >= 7) {
    $year++;
}

// Fetch Data
$scraper = new HijriScraper();
$result = $scraper->getHtmlAndParse($year);
$data = $result['data'] ?? [];

// Helper functions (unchanged)
function getStartDayOfWeek($gregorianDateStr, $monthPeriod) {
    if (preg_match('/(\d{4})/', $monthPeriod, $matches)) {
        $year = $matches[1];
    } else {
        $year = date('Y');
    }
    $fullDate = $gregorianDateStr . ' ' . $year;
    $id = ['Mei', 'Agt', 'Okt', 'Des'];
    $en = ['May', 'Aug', 'Oct', 'Dec'];
    $fullDate = str_replace($id, $en, $fullDate);
    $timestamp = strtotime($fullDate);
    if (!$timestamp) return 0;
    return (int)date('w', $timestamp);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?> - <?php echo $year; ?> H</title>
    <style>
        :root {
            --primary-color: #1a237e;
            --secondary-color: #304ffe;
            --accent-color: #ffca28;
            --bg-color: #f5f7fa;
            --text-color: #333;
            --holiday-color: #e53935;
            --border-radius: 8px;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        .lang-switcher {
            position: absolute;
            top: 0;
            right: 0;
            background: white;
            padding: 5px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        html[dir="rtl"] .lang-switcher {
            right: auto;
            left: 0;
        }
        
        .header h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
            margin-top: 40px; 
        }
        .nav-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
            align-items: center;
        }
        .btn {
            padding: 10px 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 20px;
            text-decoration: none;
            color: var(--primary-color);
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .btn:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .month-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .month-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            text-align: center;
        }
        .month-title {
            font-size: 1.25em;
            font-weight: bold;
            margin: 0;
        }
        .month-period {
            font-size: 0.85em;
            opacity: 0.9;
            margin-top: 5px;
        }
        
        .days-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background-color: #e8eaf6;
            padding: 5px 0;
            text-align: center;
            font-weight: bold;
            font-size: 0.8em;
            color: #555;
            border-bottom: 1px solid #eee;
        }
        .days-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            padding: 10px;
            gap: 5px;
            flex-grow: 1;
        }
        
        .day-cell {
            border: 1px solid #f0f0f0;
            border-radius: 4px;
            min-height: 60px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 5px;
            background: #fff;
            position: relative;
        }
        .day-cell.empty {
            background: transparent;
            border: none;
        }
        .day-cell.holiday {
            background-color: #ffebee;
            border-color: #ffcdd2;
        }
        
        .hijri-date {
            font-size: 1.2em;
            font-weight: bold;
            color: var(--primary-color);
            align-self: flex-end; 
            line-height: 1;
        }
        
        .gregorian-date {
            font-size: 0.7em;
            color: #666;
            margin-top: 2px;
        }
        .pasaran {
            font-size: 0.65em;
            color: #888;
            font-style: italic;
        }
        .holiday-badge {
            font-size: 0.6em;
            color: var(--holiday-color);
            margin-top: auto;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Docs Section */
        .docs-section {
            margin-top: 60px;
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .docs-section h2 {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; direction: ltr; text-align: left; }
        code { font-family: "Consolas", "Monaco", monospace; color: #d63384; }
        .endpoint-box { background: #e9ecef; padding: 10px; border-radius: 5px; margin-bottom: 20px; direction: ltr; text-align: left; }
        .method { font-weight: bold; color: #0d6efd; margin-right: 10px; }

        @media (max-width: 768px) {
            .calendar-grid {
                grid-template-columns: 1fr;
            }
            .header h1 { font-size: 1.5em; }
            .lang-switcher {
                position: static;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="lang-switcher">
            <select onchange="window.location.href='?year=<?php echo $year; ?>&lang='+this.value">
                <?php foreach ($translations as $code => $langData): ?>
                    <option value="<?php echo $code; ?>" <?php echo $lang == $code ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($langData['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <h1><?php echo $t['title']; ?></h1>
        
        <div class="nav-buttons">
            <a href="?year=<?php echo $year - 1; ?>&lang=<?php echo $lang; ?>" class="btn">&laquo; <?php echo $year - 1; ?> <?php echo $t['prev_year']; ?></a>
            <span style="font-size: 1.5em; font-weight: bold; padding: 5px 15px;"><?php echo $year; ?> <?php echo $t['next_year']; ?></span>
            <a href="?year=<?php echo $year + 1; ?>&lang=<?php echo $lang; ?>" class="btn"><?php echo $year + 1; ?> <?php echo $t['next_year']; ?> &raquo;</a>
            <a href="api.php?year=<?php echo $year; ?>" class="btn" target="_blank"><?php echo $t['api_json']; ?></a>
        </div>
    </div>

    <?php if (empty($data)): ?>
        <p style="text-align: center;"><?php echo $t['no_data']; ?> <?php echo $year; ?>.</p>
    <?php else: ?>
        <div class="calendar-grid">
            <?php foreach ($data as $month): 
                if (!is_array($month)) continue;
            ?>
                <div class="month-card">
                    <div class="month-header">
                        <div class="month-title"><?php echo htmlspecialchars($month['month']); ?></div>
                        <div class="month-period"><?php echo htmlspecialchars($month['period']); ?></div>
                    </div>
                    
                    <div class="days-header">
                        <?php foreach($t['days'] as $d): ?>
                            <div><?php echo $d; ?></div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="days-grid">
                        <?php
                        $firstDay = $month['days'][0] ?? null;
                        $pad = 0;
                        if ($firstDay) {
                            $pad = getStartDayOfWeek($firstDay['gregorian'], $month['period']);
                        }
                        
                        for ($i = 0; $i < $pad; $i++) {
                            echo '<div class="day-cell empty"></div>';
                        }
                        
                        foreach ($month['days'] as $day): 
                            $isHoliday = !empty($day['holiday']);
                            $cellClass = 'day-cell' . ($isHoliday ? ' holiday' : '');
                        ?>
                            <div class="<?php echo $cellClass; ?>" title="<?php echo $isHoliday ? htmlspecialchars($day['holiday']) : ''; ?>">
                                <div class="hijri-date"><?php echo $day['hijri_numeric']; ?></div>
                                <div class="gregorian-date"><?php echo htmlspecialchars($day['gregorian']); ?></div>
                                <div class="pasaran"><?php echo htmlspecialchars($day['pasaran']); ?></div>
                                <?php if ($isHoliday): ?>
                                    <div class="holiday-badge" title="<?php echo htmlspecialchars($day['holiday']); ?>">•</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- API Documentation Section -->
    <div class="docs-section">
        <h2><?php echo $t['docs_title']; ?></h2>
        <p><?php echo $t['docs_desc']; ?></p>

        <div class="endpoint-box">
            <span class="method">GET</span> <code>/api.php</code>
        </div>

        <h3><?php echo $t['params']; ?></h3>
        <ul>
            <li><code>year</code>: <?php echo $t['param_year']; ?></li>
            <li><code>date</code>: <?php echo $t['param_date']; ?></li>
        </ul>

        <h3><?php echo $t['usage_example']; ?></h3>
        <pre><code>GET /khgt/api.php?year=1448
GET /khgt/api.php?date=2026-02-18</code></pre>

        <h3><?php echo $t['response_example']; ?></h3>
        <pre><code>{
    "status": "success",
    "year": 1448,
    "source": "https://khgt.muhammadiyah.or.id/kalendar-hijriah?year=1448",
    "data": [
        {
            "month": "Muharam 1448 H",
            "period": "Juni 2026 - Juli 2026",
            "days": [ ... ]
        }
    ]
}</code></pre>

        <h3><?php echo $t['notes']; ?></h3>
        <ul>
            <li><?php echo $t['note_1']; ?></li>
        </ul>
    </div>
    
    <footer style="text-align: center; margin-top: 50px; color: #888; font-size: 0.9em;">
        <p><?php echo $t['footer']; ?></p>
    </footer>
</div>

</body>
</html>
