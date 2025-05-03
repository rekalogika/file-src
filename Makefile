include .env
-include .env.local

export APP_ENV

.PHONY: all
all: test

.PHONY: js
js:
	cd packages/file-filepond/assets && npm update && npm run build

.PHONY: test
test: composer-dump composer-validate phpstan psalm phpunit

.PHONY: phpstan
phpstan:
	$(PHP) vendor/bin/phpstan analyse

.PHONY: psalm
psalm:
	$(PHP) vendor/bin/psalm

.PHONY: phpunit
phpunit:
	$(eval c ?=)
	rm -rf tests/var
	$(PHP) vendor/bin/phpunit $(c)

.PHONY: composer-dump
composer-dump:
	composer dump --optimize

.PHONY: composer-validate
composer-validate:
	composer validate --strict

.PHONY: php-cs-fixer
php-cs-fixer: tools/php-cs-fixer
	PHP_CS_FIXER_IGNORE_ENV=1 $(PHP) $< fix --config=.php-cs-fixer.dist.php --verbose --allow-risky=yes

.PHONY: tools/php-cs-fixer
tools/php-cs-fixer:
	phive install php-cs-fixer

.PHONY: rector
rector:
	$(PHP) vendor/bin/rector process > rector.log
	make php-cs-fixer

.PHONY: monorepo
monorepo: monorepo-validate monorepo-merge

.PHONY: monorepo-validate
monorepo-validate:
	vendor/bin/monorepo-builder validate

.PHONY: monorepo-merge
monorepo-merge:
	$(PHP) vendor/bin/monorepo-builder merge

.PHONY: monorepo-release-%
monorepo-release-%:
	git update-index --really-refresh > /dev/null; git diff-index --quiet HEAD || (echo "Working directory is not clean, aborting" && exit 1)
	[ $$(git branch --show-current) == main ] || (echo "Not on main branch, aborting" && exit 1)
	$(PHP) vendor/bin/monorepo-builder release $*
	git switch -c release/$*
	git add .
	git commit -m "release: $*"

.PHONY: serve
serve:
	cd tests/assets/controllers/rekalogika/file-filepond && for A in ../../../../../packages/file-filepond/assets/dist/* ; do ln -sf $$A ; done
	$(PHP) tests/bin/console cache:clear
	$(PHP) tests/bin/console importmap:install
	$(PHP) tests/bin/console asset-map:compile
	$(PHP) tests/bin/console asset:install tests/public/
	cd tests && sh -c "$(SYMFONY) server:start --document-root=public"

.PHONY: dump
dump:
	$(PHP) tests/bin/console server:dump
