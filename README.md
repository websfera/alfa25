# Alfa25 - PHP Development Project

Projekt s PHP 8.2, MySQL 8.0 a Docker devstackem pro lokální vývoj.

## 🚀 Požadavky

- Docker Desktop
- Docker Compose
- Git (volitelné)

## 📦 Technologie

- **PHP**: 8.2 (Apache)
- **Databáze**: MySQL 8.0
- **Správa DB**: phpMyAdmin
- **Závislosti**: Composer
- **Debugger**: Tracy

### PHP Rozšíření

Projekt má nainstalována následující PHP rozšíření:
- `pdo`, `pdo_mysql`, `mysqli` - databázové připojení
- `yaml` - práce s YAML soubory
- `zip` - práce s ZIP archivy
- `gd` - práce s obrázky
- `intl` - internacionalizace
- `soap` - SOAP webové služby
- `opcache` - PHP cache pro vyšší výkon
- `mbstring` - práce s multibyte stringy
- `xml`, `xmlreader`, `xmlwriter` - práce s XML
- `bcmath`, `exif`, `pcntl` a další

## 🏃 Spuštění projektu

### První spuštění

```bash
# Build Docker images
docker-compose build

# Spuštění kontejnerů
docker-compose up -d
```

### Běžné použití

```bash
# Spuštění
docker-compose up -d

# Zastavení
docker-compose down

# Restart
docker-compose restart

# Zobrazení logů
docker-compose logs -f web
```

## 🌐 Přístupové body

| Služba | URL | Port |
|--------|-----|------|
| **Webserver** | http://localhost | 80 |
| **phpMyAdmin** | http://localhost:8301 | 8301 |
| **MySQL** | localhost:33061 | 33061 |

### Databázové připojení

- **Host**: `db` (uvnitř Docker sítě) nebo `localhost:33061` (z host systému)
- **Uživatel**: `root`
- **Heslo**: `alfadb`
- **Databáze**: `alfa24`

## 📁 Struktura projektu

```
/
├── docker/
│   └── build/
│       └── Dockerfile          # PHP 8.2 + Apache konfigurace
├── docker-compose.yml          # Docker Compose konfigurace
├── classes/                    # PHP třídy
│   ├── User.php
│   ├── GenderEnum.php
│   ├── Obdelnik.php
│   └── Ctverec.php
├── template/                   # HTML šablony
│   ├── login.html
│   ├── messenger.html
│   └── css/
│       └── style.css
├── vendor/                     # Composer závislosti
├── index.php                   # Hlavní soubor aplikace
├── .htaccess                   # Apache URL rewriting
├── composer.json               # Composer závislosti
└── README.md                   # Tento soubor
```

## 🔧 Composer závislosti

- `ramsey/uuid` - generování UUID
- `tracy/tracy` - debugging tool
- `brick/math` - matematické operace

### Instalace nových balíčků

```bash
# Spuštění Composer uvnitř kontejneru
docker exec -it alfa-web composer require nazev/balicku

# Nebo přímo z host systému (pokud máte Composer lokálně)
composer require nazev/balicku
```

## 🛠️ Užitečné příkazy

### Docker příkazy

```bash
# Přístup do web kontejneru
docker exec -it alfa-web bash

# Přístup do MySQL kontejneru
docker exec -it alfa-mysql bash

# Spuštění PHP skriptu v kontejneru
docker exec alfa-web php /var/www/html/script.php

# Kontrola PHP verze
docker exec alfa-web php -v

# Seznam PHP rozšíření
docker exec alfa-web php -m

# Zobrazení phpinfo
docker exec alfa-web php -r "phpinfo();" | less
```

### MySQL příkazy

```bash
# Připojení k MySQL CLI
docker exec -it alfa-mysql mysql -uroot -palfadb

# Backup databáze
docker exec alfa-mysql mysqldump -uroot -palfadb alfa24 > backup.sql

# Restore databáze
docker exec -i alfa-mysql mysql -uroot -palfadb alfa24 < backup.sql
```

## 🐛 Debugging

Projekt používá **Tracy Debugger** pro zobrazení chyb a ladění.

```php
use Tracy\Debugger;

// Zapnutí debuggeru (výchozí)
Debugger::enable();

// Vypnutí debuggeru pro produkci
Debugger::enable(false);

// Dump proměnné
Debugger::dump($variable);

// Barva dump
bdump($variable);

// Log do souboru
Debugger::log('zpráva');
```

## 📝 Poznámky

### Apache konfigurace

Apache má povolený `mod_rewrite` a `AllowOverride All` pro `.htaccess` soubory.

### Timezone

Projekt je nastaven na časovou zónu: **Europe/Prague**

### Composer autoload

Všechny třídy v `classes/` adresáři je třeba includovat ručně nebo upravit `composer.json` pro PSR-4 autoloading:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "classes/"
        }
    }
}
```

Po úpravě spustit:
```bash
docker exec alfa-web composer dump-autoload
```

## 🔄 Rebuild Docker obrazu

Po změnách v Dockerfile:

```bash
# Zastavit kontejnery
docker-compose down

# Rebuild bez cache
docker-compose build --no-cache

# Spustit znovu
docker-compose up -d
```

## 🆘 Řešení problémů

### Port již používán

Pokud port 80 nebo 8301 už je obsazený:

```bash
# Změnit porty v docker-compose.yml
ports:
  - "8080:80"  # místo 80:80
```

### Práva k souborům

```bash
# Pokud máte problémy s právy
docker exec alfa-web chown -R www-data:www-data /var/www/html
```

### Vymazání všech dat a restart

```bash
# Smazat vše včetně volumes
docker-compose down -v

# Rebuild a start
docker-compose build --no-cache
docker-compose up -d
```

## 📚 Další informace

- [PHP 8.2 Documentation](https://www.php.net/manual/en/)
- [MySQL 8.0 Documentation](https://dev.mysql.com/doc/refman/8.0/en/)
- [Tracy Debugger](https://tracy.nette.org/)
- [Docker Documentation](https://docs.docker.com/)

---

**Autor**: Martin Miláček  
**Email**: martin@milacek.eu  
**Datum vytvoření**: 13. 11. 2025
