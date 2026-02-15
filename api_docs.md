# KHGT Hijri Calendar API Documentation

## Overview
This API allows you to retrieve Hijri calendar data including Gregorian dates, Hijri dates, Javanese market days (Pasaran), and holidays.

## Base URL
Assuming the project is hosted at `http://localhost/khgt`:
`http://localhost/khgt/api.php`

## Endpoints

### Get Calendar Data

**Request:**
`GET /api.php`

**Parameters:**


| Parameter | Type    | Required | Description | Default |
|-----------|---------|----------|-------------|---------|
| `year`    | Integer | No       | The Hijri year to fetch (e.g., 1448) | 1448 |
| `month`   | Mixed   | No       | Filter by month index (1-12) or name (e.g., "Muharam") | null |
| `date`    | String  | No       | Filter by date (YYYY-MM-DD or d M, e.g., "2026-06-16" or "16 Jun") | null |

**Example Requests:**
```http
GET http://localhost/khgt/api.php?year=1448
GET http://localhost/khgt/api.php?year=1448&month=1
GET http://localhost/khgt/api.php?year=1448&date=2026-06-16
```


**Success Response:**

```json
{
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
                }
            ]
        }
    ]
}
```

**Error Response:**

```json
{
    "status": "error",
    "message": "Failed to fetch data from source"
}
```
