# Blockchair Test Assignment

Assignment: [Link](assignment.md)

## Requirements
- `PHP` equal or higher version 8.3
- `PostgreSQL`equal or higher version 15
- `Docker` equal or higher version 27.1.1, build 6312585
- `Docker Compose` equal or higher version v2.29.1-desktop.1
- `Monero Daemon` equal or higher version v0.18.3.4
- `Zcash Daemon` equal or higher version v6.0.0

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

3. Enable PSR-4 autoload by entering to container
```bash
docker exec -it blockchair-test-php-1 /bin/sh
composer dump-autoload
```

4. Give access to web server for logging
```bash
chown -R www-data:www-data /var/www/html/logs
chmod 755 /var/www/html/logs
```

If you want directly run script from host machine
```bash
docker compose run --rm blockchair-test-php-1 php public/monero.php 1000
docker compose run --rm blockchair-test-php-1 php public/zcash.php 1000
```

Run monero single block sync by entering to container
```bash
docker exec -it blockchair-test-php-1 /bin/sh
php monero.php 1000
php zcash.php 1000
```

Run monero range block sync by entering to container
```bash
docker exec -it blockchair-test-php-1 /bin/sh
php monero.php 1000 1009
php zcash.php 1000 1009
```

## Rest API
```bash
curl --location 'http://localhost/api/monero'
curl --location 'http://localhost/api/zcash'
curl --location 'http://localhost/api/monero/block/{id}'
curl --location 'http://localhost/api/zcash/block/{id}'
curl --location 'http://localhost/api/monero/transaction/{id}'
curl --location 'http://localhost/api/zcash/transaction/{id}'
```

## Project structure
├── docker - PostgreSQL persistant data, docker files for php and nginx  
├── logs - application logs in app.log file  
├── migration - sql files for initializing database  
├── monero - docker monero files and config  
├── public - php api and cli scripts  
├── src - php classes  
├── vendor - used only for PSR-4 autoload class  
├── zcash - zcash files and config  
├── .env.example - example of .env file  
├── assignment.md - copy of original assignment just in case  
├── composer.json - PSR-4 autoload class  
├── docker-compose.yml - docker compose file  
├── monero_config.json - flat data for fork notification of monero  
├── research.md  - my knowledge base, to-do and personal thoughts  
└── zcash_config.json  - flat data for fork notification of zcash  

## Database structure documentation
**monero_blocks**
| column          | type           | 
| :-------------- | :------------- |
| `height`        | `int8`         |
| `hash`          | `varchar(128)` |
| `miner_tx_hash` | `varchar(128)` |
| `difficulty`    | `int8`         |
| `size`          | `int4`         |
| `timestamp`     | `int4`         |
| `transactions`  | `int4`         |
| `major_version` | `int2`         |
| `minor_version` | `int2`         |
| `block`         | `jsonb`        |
| `created_at`    | `timestamp(6)` |
| `updated_at`    | `timestamp(6)` |

**monero_transactions**
| column         | type           | 
| :------------- | :------------- |
| `height_id`    | `int8`         |
| `tx_hash`      | `varchar(128)` |
| `transaction`  | `jsonb`        |
| `created_at`   | `timestamp(6)` |
| `updated_at`   | `timestamp(6)` |


**zcash_blocks**
| column          | type           | 
| :-------------- | :------------- |
| `height`        | `int8`         |
| `hash`          | `varchar(128)` |
| `miner_tx_hash` | `varchar(128)` |
| `difficulty`    | `int8`         |
| `size`          | `int4`         |
| `timestamp`     | `int4`         |
| `transactions`  | `int4`         |
| `major_version` | `int2`         |
| `minor_version` | `int2`         |
| `block`         | `jsonb`        |
| `created_at`    | `timestamp(6)` |
| `updated_at`    | `timestamp(6)` |

**zcash_transactions**
| column         | type           | 
| :------------- | :------------- |
| `height_id`    | `int8`         |
| `txid`         | `varchar(128)` |
| `transaction`  | `jsonb`        |
| `created_at`   | `timestamp(6)` |
| `updated_at`   | `timestamp(6)` |

**metrics**
| column                         | type           | 
| :----------------------------- | :------------- |
| `id`                           | `int4`         |
| `blockchain`                   | `varchar(255)` |
| `total_capitalization`         | `varchar(255)` |
| `shielded_pool_capitalization` | `varchar(255)` |
| `shielded_percentage`          | `varchar(255)` |
| `calculated_at`                | `timestamp(6)` |

## Research
Research: [Link](research.md)

## Authors
- [ryandev](https://github.com/ryandevz)