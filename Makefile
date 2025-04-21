clean:
	rm -rf vendor
	rm composer.lock

drupal10: clean
	composer require drupal/core:^10
	git restore composer.json

drupal10-baseline: drupal10
	vendor/bin/phpstan analyze -c phpstan10.neon --generate-baseline phpstan-baseline10.neon

drupal11: clean
	composer update

drupal11-baseline: drupal11
	vendor/bin/phpstan analyze -c phpstan.neon --generate-baseline
