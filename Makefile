.PHONY: test
test: composer-dump composer-validate phpstan psalm phpunit

.PHONY: monorepo
monorepo: validate merge

.PHONY: merge
merge:
	vendor/bin/monorepo-builder merge

.PHONY: validate
validate:
	vendor/bin/monorepo-builder validate

.PHONY: phpstan
phpstan:
	vendor/bin/phpstan analyse

.PHONY: psalm
psalm:
	vendor/bin/psalm

.PHONY: phpunit
phpunit:
	vendor/bin/phpunit --testdox -v

.PHONY: composer-dump
composer-dump:
	composer dump --optimize

.PHONY: composer-validate
composer-validate:
	composer validate --strict

.PHONY: php-cs-fixer
php-cs-fixer: tools/php-cs-fixer
	$< fix --config=.php-cs-fixer.dist.php --verbose --allow-risky=yes

.PHONY: tools/php-cs-fixer
tools/php-cs-fixer:
	phive install php-cs-fixer
