<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KHGT Hijri Calendar API</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 20px; color: #333; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        code { font-family: "Consolas", "Monaco", monospace; color: #d63384; }
        .endpoint { background: #e9ecef; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .method { font-weight: bold; color: #0d6efd; margin-right: 10px; }
    </style>
</head>
<body>
    <h1>KHGT Hijri Calendar API Documentation</h1>
    <p>This API provides Hijri calendar data scraped from the Muhammadiyah KHGT website.</p>

    <div class="endpoint">
        <span class="method">GET</span> <code>/api.php?year={year}</code>
    </div>

    <h3>Parameters</h3>
    <ul>
        <li><code>year</code> (optional): The Hijri year to fetch (e.g., <code>1448</code>). If not provided, it is calculated from the <code>date</code> parameter or defaults to the current Hijri year.</li>
        <li><code>date</code> (optional): A Gregorian date (YYYY-MM-DD or D M) to filter by. Example: <code>2026-02-18</code>. If provided without a year, the Hijri year is automatically calculated.</li>
    </ul>

    <h3>Example Usage</h3>
    <pre><code>GET /khgt/api.php?year=1448
GET /khgt/api.php?date=2026-02-18</code></pre>
    <h3>Example Response</h3>
    <pre><code>{
    "status": "success",
    "year": 1448,
    "source": "https://khgt.muhammadiyah.or.id/kalendar-hijriah?year=1448",
    "data": [
        {
            "month": "Muharam 1448 H",
            "period": "Juni 2026 - Juli 2026",
            "days": [
                {
                    "gregorian": "16 Jun",
                    "hijri": "١",
                    "hijri_numeric": "1",
                    "pasaran": "Wage",
                    "holiday": "Hari Tahun baru Islam (Hijriah)"
                },
                ...
            ]
        },
        ...
    ]
}</code></pre>

    <h3>Notes</h3>
    <ul>
        <li>Data is scraped in real-time. Performance depends on the source website.</li>
        <li>The structure depends on the HTML of the source website; changes there may break this API.</li>
    </ul>
</body>
</html>
