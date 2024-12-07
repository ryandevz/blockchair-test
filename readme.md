# Blockchair Test Assignment

Assignment: [Link](assignment.md)

## Requirements
- `PHP` equal or higher version 8.3
- `PostgreSQL`equal or higher version 15
- `Docker` equal or higher version 27.1.1, build 6312585
- `Docker Compose` equal or higher version v2.29.1-desktop.1

## Installation
```bash
cp .env.example .env
docker compose up -d
```

Interactive
```bash
docker exec -it blockchair-test-php-1 /bin/sh
composer dump-autoload
```

Give access for web server for logging
```bash
sudo chown -R www-data:www-data /var/www/html/logs
sudo chmod 755 /var/www/html/logs
```

Directly run script:
```bash
docker compose run --rm php php public/index.php
```

Run monero single block sync
```bash
php monero.php 1873006
php zcash.php 106129
```

Run monero range block sync
```bash
php monero.php 1873000 1873009
php zcash.php 106120 106129
```

## Project structure
- docker
- monero
- zcash

## Research
Research: [Link](research.md)

## Authors
- [ryandev](https://github.com/ryandevz)