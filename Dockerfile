FROM phpstan/phpstan:0.11.19

WORKDIR /app

ENTRYPOINT ["phpstan"]
