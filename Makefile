install:
	composer install

autoload:
	composer dump-autoload

validate:
	composer validate

test:
	composer exec --verbose phpunit tests

test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

lint:
	composer exec --verbose phpcs -- --standard=PSR12 app

nodev-install:
	composer install --no-dev