<img src="https://raw.githubusercontent.com/apie-lib/apie-lib-monorepo/main/docs/apie-logo.svg" width="100px" align="left" />
<h1>apie-bundle</h1>






 [![Latest Stable Version](http://poser.pugx.org/apie/apie-bundle/v)](https://packagist.org/packages/apie/apie-bundle) [![Total Downloads](http://poser.pugx.org/apie/apie-bundle/downloads)](https://packagist.org/packages/apie/apie-bundle) [![Latest Unstable Version](http://poser.pugx.org/apie/apie-bundle/v/unstable)](https://packagist.org/packages/apie/apie-bundle) [![License](http://poser.pugx.org/apie/apie-bundle/license)](https://packagist.org/packages/apie/apie-bundle) [![PHP Version Require](http://poser.pugx.org/apie/apie-bundle/require/php)](https://packagist.org/packages/apie/apie-bundle) [![Code coverage](https://raw.githubusercontent.com/apie-lib/apie-bundle/main/coverage_badge.svg)](https://apie-lib.github.io/coverage/apie-bundle/index.html)  

[![PHP Composer](https://github.com/apie-lib/apie-bundle/actions/workflows/php.yml/badge.svg?event=push)](https://github.com/apie-lib/apie-bundle/actions/workflows/php.yml)

This package is part of the [Apie](https://github.com/apie-lib) library.
The code is maintained in a monorepo, so PR's need to be sent to the [monorepo](https://github.com/apie-lib/apie-lib-monorepo/pulls)

## Documentation
Include this package in a Symfony application and you can use apie in a Symfony application.

### Configuration
If Symfony flex is installed requiring this package will register ApieBundle and install apie.yaml inside config/packages. In apie.yaml you can configure your Apie application.

### Recommended Symfony bundles.
Apie/cms uses CSRF for form submits, however CSRF is a setting inside the Symfony framework bundle. Some settings are also required from the security bundle. Technically you can run Apie without these generic Symfony bundles, but it's recommended not to run Apie without the framework bundle and security bundle enabled. The Doctrine Bundle is also not a hard requirement even if you use apie/doctrine-entity-datalayer package for storing Apie resources.

### Doctrine bundle linking
Apie creates his own Dotrine entity manager if using the Doctrine Entity datalayer. Since this one is not managed by
the Doctrine Bundle, you need to configure linking Apie with Doctrine. Doing so will make the doctrine console commands work with --em=apie_manager. Also if the Symfony webprofiler is installed you can see all the queries that Apie executed in the request.

Apie's default value is to link it with the Doctrine Bundle if the DoctrineBundle class exists. This linking can be disabled in apie.yaml with:

```yaml
apie:
    enable_doctrine_bundle_connection: false
```

Without the Doctrine bundle, a programmer has to configure his own setting for database migrations. It's not recommended
to enable the automatic database migrations in production.

### apie/cms customization
TODO

#### Changing logo and other files
TODO

#### Overwrite apie/cms templates
TODO
