# Pass Generator for Karrot
This generator allows users to generate a pass image by using their Karrot name, photo and ID.
The pass also features a QR code that encodes an URL pointing to the same image on the server.
It is configured to be limited to one group on Karrot.
These users can log in, generate a pass as well as view, download and delete their pass.
At the end, they can log out.
It uses the [Karrot API](https://karrot.world/docs/).

## Technologies
PHP 8 is used.

### Libraries used
- [Guzzle](https://docs.guzzlephp.org/en/stable/) as HTTP client
- [monolog](https://seldaek.github.io/monolog/) for logging
- [QR Code](https://github.com/endroid/qr-code) as QR code image generator
- [Slim](https://www.slimframework.com/) as web framework
- [Twig](https://twig.symfony.com/) as template engine

### Development libraries used
- [Behat](https://docs.behat.org/en/latest/) for behaviour-driven tests
- [phan](https://github.com/phan/phan/) for static analysis
- [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) for enforcing code convention 
- [phpspec](https://www.phpspec.net/en/stable/) for unit tests

## Development setup
1. Install the dependencies: `composer install`
2. Add logo for website as `public/assets/images/logo.png`.
3. Add pass background image as `public/assets/images/pass-background.png`.
4. Copy `public/config/config.sample.php` to `public/config/config.php` and adapt its content.
5. Create the folder for the passes manually; It needs to be the same you have set in the config file (see previous step).

## Commands
### Dependencies
- Update the dependencies: `composer update`
- Check for direct dependency updates: `composer outdated --direct`

### Tests
- Execute behat: `vendor/bin/behat`
  - A code coverage report is generated in the folder `/coverage-behat`.
- Add automatically generated snippets: `vendor/bin/behat --dry-run --append-snippets`
- Execute tests via phpspec with code coverage report generation: `vendor/bin/phpspec run`
  - A code coverage report is generated in the folder `/coverage`.
  - Add `-v` to see more details.
  - Add the path to a test file to only execute that class.
- Generate test class via phpspec: `vendor/bin/phpspec desc "App\Config"`
- Run the built-in server: `php -S localhost:8888 -t public/`

### Code analysis
- Execute CodeSniffer: `vendor/bin/phpcs`
- Execute Phan: `vendor/bin/phan`

## Deployment
- Copy the following folders and files:
  - `log/*`
  - `public/*` without the folder for passes
  - `composer.json`
  - `composer.lock`
- Copy `public/config/config.sample.php` to `public/config/config.php` and adapt its content.
- Create the folder for the passes manually; It needs to be the same you have set in the config file (see previous step).
- Run: `composer install --no-dev`
- Make sure the server serves the `public` folder only.
- Configure your server to not pass `favicon.ico` requests to this application to not spam the `app.log` file.
