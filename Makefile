build-production:
	php7.4 bin/console dump-static-site

dev:
	symfony server:start
