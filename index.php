<?php

require_once 'scraper.php';
require_once 'lang.php'; // Load Translations

// Language Configuration
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'id';
if (!array_key_exists($lang, $translations)) $lang = 'id';

$t = $translations[$lang];
$dir = isset($t['dir']) ? $t['dir'] : 'ltr';

// Determine Year dynamically based on today's Gregorian Date
function getApproximateHijriYearPHP() {
    $y = (int)date('Y');
    $m = (int)date('n'); // 1-12
    $d = (int)date('j');
    
    $hijriYear = (int)floor(($y - 622) * (33 / 32));
    
    if ($m > 6 || ($m === 6 && $d >= 16)) {
        $hijriYear += 1;
    }
    
    return $hijriYear;
}

$year = isset($_GET['year']) ? intval($_GET['year']) : getApproximateHijriYearPHP();

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
            --primary-color: #3b5998;
            --secondary-color: #2c3e50;
            --accent-color: #3498db;
            --bg-color: #f4f6f9;
            --text-color: #333;
            --holiday-color: #ef5350;
            --border-radius: 12px;
        }
        .top-navbar {
            background-color: var(--primary-color);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .top-navbar .brand {
            font-size: 20px;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        .top-navbar .nav-links {
            display: flex;
            gap: 20px;
        }
        .top-navbar .nav-links a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
            font-size: 15px;
        }
        .top-navbar .nav-links a:hover {
            color: white;
        }
        @media (max-width: 768px) {
            .top-navbar {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }
            .top-navbar .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        .lang-switcher {
            position: absolute;
            top: 0;
            right: 0;
            background: white;
            padding: 5px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }
        html[dir="rtl"] .lang-switcher {
            right: auto;
            left: 0;
        }
        
        .header h1 {
            color: var(--secondary-color);
            margin-bottom: 10px;
            margin-top: 40px; 
            font-size: 32px;
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
            border-radius: 8px;
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .btn:hover {
            background-color: #f0f0f0;
            transform: translateY(-2px);
        }
        
        .calendar-grid {
            display: flex;
            flex-direction: column;
            gap: 40px;
        }
        
        /* Modern Layout CSS */
        .month-wrapper {
            background: #fdfdfd;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 30px;
            border: 1px solid #eaeaea;
        }

        .layout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .month-header h2 {
            margin: 0 0 5px 0;
            font-size: 28px;
            color: var(--secondary-color);
        }

        .month-header p {
            margin: 0 0 20px 0;
            color: #888;
            font-size: 16px;
        }

        .cal-days-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }

        .cal-days-header div {
            background: var(--primary-color);
            color: white;
            text-align: center;
            padding: 8px 0;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }

        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
        }

        .cal-cell {
            background: white;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            min-height: 90px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: transform 0.2s;
            position: relative;
        }
        
        .cal-cell:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        /* Today highlight handled in JS before, we can keep or adjust based on PHP */
        .cal-cell.is-today {
            border: 2px solid var(--accent-color);
            background: #f0f8ff;
        }

        .cal-cell.empty {
            background: repeating-linear-gradient(
                -45deg,
                #ffffff,
                #ffffff 10px,
                #f8f8f8 10px,
                #f8f8f8 20px
            );
            border: 1px solid #eee;
            box-shadow: none;
        }
        .cal-cell.empty:hover {
            transform: none;
            box-shadow: none;
        }

        .cal-greg {
            font-size: 11px;
            color: #999;
        }

        .cal-hijri {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin: auto 0;
            line-height: 1;
        }

        .cal-pasaran {
            font-size: 11px;
            color: #aaa;
            text-align: center;
            margin-bottom: 3px;
        }

        .cal-dot {
            width: 5px;
            height: 5px;
            background: var(--accent-color);
            border-radius: 50%;
            margin: 0 auto;
        }

        .events-col h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: var(--secondary-color);
        }

        .events-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .event-card {
            background: white;
            border: 1px solid #eee;
            border-left: 4px solid var(--accent-color);
            border-radius: 6px;
            padding: 12px 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .event-date {
            font-size: 13px;
            font-weight: 700;
            color: #555;
            margin-bottom: 4px;
        }

        .event-name {
            font-size: 12px;
            color: #888;
        }
        
        /* Docs Section */
        .docs-section {
            margin-top: 60px;
            background: white;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #eaeaea;
        }
        .docs-section h2 {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: var(--secondary-color);
        }
        pre { background: #f4f4f4; padding: 15px; border-radius: 8px; overflow-x: auto; direction: ltr; text-align: left; }
        code { font-family: "Consolas", "Monaco", monospace; color: #d63384; }
        .endpoint-box { background: #e9ecef; padding: 10px; border-radius: 8px; margin-bottom: 20px; direction: ltr; text-align: left; font-size: 15px; }
        .method { font-weight: bold; color: #0d6efd; margin-right: 10px; }

        @media (max-width: 900px) {
            .layout-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 768px) {
            .header h1 { font-size: 1.5em; }
            .cal-days-header div { font-size: 11px; }
            .cal-hijri { font-size: 20px; }
            .lang-switcher {
                position: static;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>

<?php 
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$widgetUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/contoh_kalender.html"; 
?>

<nav class="top-navbar">
    <a href="index.php" class="brand">KHGT Web</a>
    <div class="nav-links">
        <a href="#kalender">Kalender</a>
        <a href="#api-docs">API</a>
        <a href="#blogger">Blogger Widget</a>
        <a href="#wordpress">Wordpress Widget</a>
        <a href="contoh_kalender.html" target="_blank">Contoh</a>
    </div>
</nav>

<div class="container" id="kalender">
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
            <?php 
                $prevDisabled = ($year <= 1447) ? 'pointer-events: none; opacity: 0.5; background: #eee;' : '';
                $nextDisabled = ($year >= 1492) ? 'pointer-events: none; opacity: 0.5; background: #eee;' : '';
            ?>
            <a href="?year=<?php echo $year - 1; ?>&lang=<?php echo $lang; ?>" class="btn" style="<?php echo $prevDisabled; ?>">&laquo; <?php echo $year - 1; ?> <?php echo $t['prev_year']; ?></a>
            <span style="font-size: 1.5em; font-weight: bold; padding: 5px 15px;"><?php echo $year; ?> <?php echo $t['next_year']; ?></span>
            <a href="?year=<?php echo $year + 1; ?>&lang=<?php echo $lang; ?>" class="btn" style="<?php echo $nextDisabled; ?>"><?php echo $year + 1; ?> <?php echo $t['next_year']; ?> &raquo;</a>
            <a href="api.php?year=<?php echo $year; ?>" class="btn" target="_blank"><?php echo $t['api_json']; ?></a>
        </div>
    </div>

    <?php if ($year < 1447 || $year > 1492): ?>
        <p style="text-align: center; color: #7f8c8d; font-size: 16px; margin: 40px;">Tidak ada data untuk tahun <?php echo $year; ?>. Data tersedia untuk tahun 1447 hingga 1492.</p>
    <?php elseif (empty($data)): ?>
        <p style="text-align: center;"><?php echo $t['no_data']; ?> <?php echo $year; ?>.</p>
    <?php else: ?>
        <div class="calendar-grid">
            <?php foreach ($data as $month): 
                if (!is_array($month)) continue;
                
                // Siapkan HTML untuk events/Hari Istimewa di bulan ini
                $eventsHtml = '';
                foreach ($month['days'] as $day) {
                    if (!empty($day['holiday'])) {
                        // Ambil format "1 Muharam 1448" dan pisahkan bulannya jika perlu, atau gunakan hijri_numeric + month text
                        $hijriText = $day['hijri_numeric'] . ' ' . trim(preg_replace('/\s\d{4}\sH$/', '', $month['month']));
                        $gregParts = explode(' ', $day['gregorian']);
                        $gregText = isset($gregParts[0]) && isset($gregParts[1]) ? $gregParts[0] . ' ' . $gregParts[1] : $day['gregorian'];
                        
                        $eventsHtml .= '
                        <div class="event-card">
                            <div class="event-date">' . htmlspecialchars($hijriText) . ' / ' . htmlspecialchars($gregText) . '</div>
                            <div class="event-name">' . htmlspecialchars($day['holiday']) . '</div>
                        </div>';
                    }
                }
            ?>
                <div class="month-wrapper">
                    <div class="layout-grid">
                        <div class="cal-main">
                            <div class="month-header">
                                <h2><?php echo htmlspecialchars($month['month']); ?></h2>
                                <p><?php echo htmlspecialchars($month['period']); ?></p>
                            </div>
                            
                            <div class="cal-days-header">
                                <?php foreach($t['days'] as $d): ?>
                                    <div><?php echo $d; ?></div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="cal-grid">
                                <?php
                                $firstDay = $month['days'][0] ?? null;
                                $pad = 0;
                                if ($firstDay) {
                                    $pad = getStartDayOfWeek($firstDay['gregorian'], $month['period']);
                                }
                                
                                for ($i = 0; $i < $pad; $i++) {
                                    echo '<div class="cal-cell empty"></div>';
                                }
                                
                                foreach ($month['days'] as $index => $day): 
                                    $isHoliday = !empty($day['holiday']);
                                    
                                    // Highlight today's date based on server date matches
                                    // $day['gregorian'] e.g "21 Feb 2026" or "21 Feb"
                                    $todayDay = date('j');
                                    $todayMonthId = array('Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des')[date('n') - 1];
                                    $todayMonthEn = date('M');
                                    $todayPattern1 = $todayDay . ' ' . $todayMonthId;
                                    $todayPattern2 = str_pad($todayDay, 2, '0', STR_PAD_LEFT) . ' ' . $todayMonthId;
                                    $todayPattern3 = $todayDay . ' ' . $todayMonthEn;
                                    $todayPattern4 = str_pad($todayDay, 2, '0', STR_PAD_LEFT) . ' ' . $todayMonthEn;
                                    
                                    $dstr = trim($day['gregorian']);
                                    // Pastikan kita berada di tahun Masehi yang tepat untuk menghindari penandaan hari di tahun Hijriah lain
                                    $isCurrentGregYear = (strpos($month['period'], (string)date('Y')) !== false);
                                    $isToday = $isCurrentGregYear && (strpos($dstr, $todayPattern1) !== false || 
                                                strpos($dstr, $todayPattern2) !== false || 
                                                strpos($dstr, $todayPattern3) !== false || 
                                                strpos($dstr, $todayPattern4) !== false);
                                                
                                    $cellClasses = 'cal-cell';
                                    if ($isToday) $cellClasses .= ' is-today';

                                    // Deteksi hari (0 = Ahad, 5 = Jumat)
                                    $dayOfWeek = ($pad + $index) % 7;
                                    $hijriColor = '#455a64';
                                    if ($dayOfWeek === 0) $hijriColor = '#ef5350';
                                    if ($dayOfWeek === 5) $hijriColor = '#66bb6a';

                                    $gregParts = explode(' ', $day['gregorian']);
                                    $gregText = isset($gregParts[0]) && isset($gregParts[1]) ? $gregParts[0] . ' ' . $gregParts[1] : $day['gregorian'];

                                    $titleAttr = [];
                                    if ($isHoliday) $titleAttr[] = 'Libur: ' . $day['holiday'];
                                    if (!empty($day['pasaran'])) $titleAttr[] = 'Pasaran: ' . $day['pasaran'];
                                    $titleStr = !empty($titleAttr) ? htmlspecialchars(implode(' | ', $titleAttr)) : '';
                                ?>
                                    <div class="<?php echo $cellClasses; ?>" title="<?php echo $titleStr; ?>">
                                        <div class="cal-greg"><?php echo htmlspecialchars($gregText); ?></div>
                                        <div class="cal-hijri" style="color: <?php echo $hijriColor; ?>;"><?php echo $day['hijri']; ?></div>
                                        <div class="cal-pasaran"><?php echo htmlspecialchars($day['pasaran']); ?></div>
                                        <?php if ($isHoliday): ?>
                                            <div class="cal-dot"></div>
                                        <?php else: ?>
                                            <div style="height:5px"></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="events-col">
                            <h3>Hari Istimewa</h3>
                            <div class="events-list">
                                <?php echo !empty($eventsHtml) ? $eventsHtml : '<p style="color:#999; font-size:13px;">Tidak ada hari istimewa bulan ini.</p>'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- API Documentation Section -->
    <div class="docs-section" id="api-docs">
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
    
    <!-- Blogger Widget Section -->
    <div class="docs-section" id="blogger">
        <h2>Blogger Widget</h2>
        <p>Pasang kalender Hijriah KHGT di blog Blogger Anda dengan menyalin kode di bawah ini ke dalam widget HTML/JavaScript.</p>
        <pre><code style="color: #4CAF50;">&lt;iframe src="<?php echo htmlspecialchars($widgetUrl); ?>" width="100%" height="750" style="border:none; border-radius:12px; overflow:hidden; background: #fff;" scrolling="no"&gt;&lt;/iframe&gt;</code></pre>
    </div>

    <!-- Wordpress Widget Section -->
    <div class="docs-section" id="wordpress">
        <h2>Wordpress Widget</h2>
        <p>Tambahkan kalender ke situs Wordpress Anda dengan menggunakan blok "Custom HTML" atau widget Text/HTML dan masukkan kode berikut.</p>
        <pre><code style="color: #4CAF50;">&lt;iframe src="<?php echo htmlspecialchars($widgetUrl); ?>" width="100%" height="750" style="border:none; border-radius:12px; overflow:hidden; background: #fff;" scrolling="no"&gt;&lt;/iframe&gt;</code></pre>
    </div>
    
    <footer style="text-align: center; margin-top: 50px; color: #888; font-size: 0.9em; padding-bottom: 20px;">
        <p><?php echo $t['footer']; ?></p>
    </footer>
</div>

</body>
</html>
