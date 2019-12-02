## PHPStan

## 事前準備

```bash
docker container run --rm --volume $(PWD):/app composer install
```

## PHPStanの利用

```bash
docker-compose run --volume /path/to/your-project:/app/classes --rm phpstan
```

## カスタムルールのテスト

```bash
docker-compose run --rm test
```
