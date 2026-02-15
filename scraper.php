<?php


class HijriScraper {
    private $baseUrl = 'https://khgt.muhammadiyah.or.id/kalendar-hijriah';

    public function getHtmlAndParse($year = null) {
        if (!$year) {
            $year = 1448;
        }

        $url = $this->baseUrl . '?year=' . $year;
        
        $html = $this->fetchUrl($url);
        
        if (!$html) {
            return [
                'status' => 'error', 
                'message' => 'Failed to fetch data from source',
                'url' => $url
            ];
        }

        return $this->parseHtmlContent($html, $year, $url);
    }

    public function parseHtmlContent($html, $year, $url) {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $data = [];
        $monthsContainer = $xpath->query('//div[@id="calendar-hijri"]/div[contains(@class, "p-4")]');

        if ($monthsContainer->length === 0) {
             return [
                'status' => 'error',
                'message' => 'No calendar data found in HTML',
                'debug_html_len' => strlen($html)
            ];
        }

        foreach ($monthsContainer as $monthDiv) {
            $monthData = [];
            
            // Extract Month Name
            $headerNode = $xpath->query('.//h2', $monthDiv)->item(0);
            $monthName = $headerNode ? trim($headerNode->textContent) : 'Unknown Month';
            $monthData['month'] = $monthName;

            // Extract Period (Gregorian)
            $periodNode = $xpath->query('.//p[contains(@class, "text-muted")]', $monthDiv)->item(0);
            $monthData['period'] = $periodNode ? trim($periodNode->textContent) : '';

            $days = [];
            // Days parsing
            $dayNodes = $xpath->query('.//div[contains(@class, "row-cols-7")]/div[contains(@class, "py-1")]', $monthDiv);

            foreach ($dayNodes as $dayNode) {
                $dateBox = $xpath->query('.//div[contains(@class, "bg-white")]', $dayNode)->item(0);
                
                if ($dateBox) {
                    $dayInfo = [];
                    
                    // Gregorian Date
                    $gregNode = $xpath->query('.//div[contains(@class, "align-self-start")]', $dateBox)->item(0);
                    $dayInfo['gregorian'] = $gregNode ? trim($gregNode->textContent) : '';

                    // Hijri Date
                    $hijriNode = $xpath->query('.//div[contains(@style, "font-family: amiri")]', $dateBox)->item(0);
                    $hijriRaw = $hijriNode ? trim($hijriNode->textContent) : '';
                    $dayInfo['hijri'] = $hijriRaw;
                    $dayInfo['hijri_numeric'] = $this->arabicToLatin($hijriRaw);

                    // Pasaran
                    $pasaranNode = $xpath->query('.//div[contains(@class, "text-muted small")]', $dateBox)->item(1);
                    // Use a more specific path if item(1) is risky, but based on structure it's the second text-muted small.
                    // Actually let's use the one inside flex-fill to be safe
                    $pasaranNodeV2 = $xpath->query('.//div[contains(@class, "flex-fill")]//div[contains(@class, "text-muted")]', $dateBox)->item(0);
                    
                    $dayInfo['pasaran'] = $pasaranNodeV2 ? trim($pasaranNodeV2->textContent) : '';

                    // Holiday
                    $holidayDot = $xpath->query('.//div[@title]', $dateBox)->item(0);
                    $dayInfo['holiday'] = $holidayDot ? $holidayDot->getAttribute('title') : null;

                    $days[] = $dayInfo;
                }
            }
            $monthData['days'] = $days;
            $data[] = $monthData;
        }

        return [
            'status' => 'success',
            'year' => $year,
            'source' => $url,
            'data' => $data
        ];
    }

    private function arabicToLatin($string) {
        $arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $latin = ['0','1','2','3','4','5','6','7','8','9'];
        return str_replace($arabic, $latin, $string);
    }

    private function fetchUrl($url) {
        $options = [
            'http' => [
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n",
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ];
        $context = stream_context_create($options);
        return @file_get_contents($url, false, $context);
    }
}

