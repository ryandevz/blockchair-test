# Blockchair Test Assignment

Assignment: [Link](assignment.md)

## Requirements
- `PHP` equal or higher version 8.3
- `PostgreSQL`equal or higher version 15
- `Docker` equal or higher version 27.1.1, build 6312585
- `Docker Compose` equal or higher version v2.29.1-desktop.1

## Installation
1. Install Docker with Docker compose | [Instuction](https://docs.docker.com/engine/install/)
2. Create copy of enviroment file
```bash
cp .env.example .env
```
3. Run containers
```bash
docker compose up -d
```

3. Enable PSR-4 autoload
```bash
docker exec -it blockchair-test-php-1 /bin/sh
composer dump-autoload
```

4. Give access to web server for logging
```bash
chown -R www-data:www-data /var/www/html/logs
chmod 755 /var/www/html/logs
```

If you want directly run script:
```bash
docker compose run --rm blockchair-test-php-1 php public/monero.php 1873006
docker compose run --rm blockchair-test-php-1 php public/zcash.php 106129
```

Run monero single block sync
```bash
docker exec -it blockchair-test-php-1 /bin/sh
php monero.php 1873006
php zcash.php 106129
```

Run monero range block sync
```bash
docker exec -it blockchair-test-php-1 /bin/sh
php monero.php 1873000 1873009
php zcash.php 106120 106129
```

## Rest API
```bash
curl --location 'http://localhost/api/monero'
curl --location 'http://localhost/api/zcash'
```

## Project structure
- docker
- logs
- migration
- monero
- public
- src
- vendor
- zcash
- .env.example
- assignment.md
- composer.json
- docker-compose.yml
- monero_config.json
- research.md
- zcash_config.json

## Database structure documentation
- monero_blocks
- monero_transactions
- zcash_blocks
- zcash_transactions
- metrics

## Research
Research: [Link](research.md)

## Authors
- [ryandev](https://github.com/ryandevz)