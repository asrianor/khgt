# KHGT Hijri Calendar Scraper API

This project provides a PHP-based API that scrapes Hijri calendar data from the Muhammadiyah KHGT website.

## Features
- Scrapes Gregorian, Hijri, and Javanese market days (Pasaran).
- API Endpoint returning JSON data.
- Simple documentation page.

## Installation
1. Clone this repository to your web server (e.g., XAMPP `htdocs`).
2. Ensure you have PHP installed.
3. Access the API at `http://localhost/khgt/api.php`.

## Usage
- **API Endpoint:** `GET /api.php?year=1448`
- **Documentation:** `http://localhost/khgt/index.php`

## Deployment to GitHub
1. Create a new repository on GitHub (e.g., `khgt-api`).
2. Run the following commands in this directory:
   ```bash
   git remote add origin https://github.com/YOUR_USERNAME/khgt-api.git
   git branch -M main
   git push -u origin main
   ```
