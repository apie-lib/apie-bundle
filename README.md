<img src="https://raw.githubusercontent.com/apie-lib/apie-lib-monorepo/main/docs/apie-logo.svg" width="100px" align="left" />
<h1>apie-bundle</h1>






 [![Latest Stable Version](https://poser.pugx.org/apie/apie-bundle/v)](https://packagist.org/packages/apie/apie-bundle) [![Total Downloads](https://poser.pugx.org/apie/apie-bundle/downloads)](https://packagist.org/packages/apie/apie-bundle) [![Latest Unstable Version](https://poser.pugx.org/apie/apie-bundle/v/unstable)](https://packagist.org/packages/apie/apie-bundle) [![License](https://poser.pugx.org/apie/apie-bundle/license)](https://packagist.org/packages/apie/apie-bundle) [![PHP Composer](https://apie-lib.github.io/projectCoverage/coverage-apie-bundle.svg)](https://apie-lib.github.io/projectCoverage/apie-bundle/index.html)  

[![PHP Composer](https://github.com/apie-lib/apie-bundle/actions/workflows/php.yml/badge.svg?event=push)](https://github.com/apie-lib/apie-bundle/actions/workflows/php.yml)

This package is part of the [Apie](https://github.com/apie-lib) library.
The code is maintained in a monorepo, so PR's need to be sent to the [monorepo](https://github.com/apie-lib/apie-lib-monorepo/pulls)

## Documentation
Include this package in a Symfony application and you can use apie in a Symfony application. In general adding an apie package would add functionality
to your application. For example add apie/rest-api and you will get functionality for a Rest API in your Symfony application.

### Configuration
If Symfony flex is installed requiring this package will register ApieBundle and install apie.yaml inside config/packages. In apie.yaml you can configure
your Apie application. In general you can configure the location of the domain objects per bounded context. In most cases you require apie/doctrine-entity-datalayer
in which case you need to set up the connection parameters for Doctrine:

```yaml
# config/packages/apie.yaml
apie:
  doctrine:
    connection_params:
      dbname:               project
      host:                 mysql
      port:                 3306
      user:                 project
      password:             project
      driver:               pdo_mysql
  datalayers:
    default_datalayer: 'Apie\DoctrineEntityDatalayer\DoctrineEntityDatalayer'
  bounded_contexts:
    example:
      entities_folder: "%kernel.project_dir%/src/ApiePlayground/Example/Resources/"
      entities_namespace: 'App\ApiePlayground\Example\Resources'
      actions_folder: "%kernel.project_dir%/src/ApiePlayground/Example/Actions/"
      actions_namespace: 'App\ApiePlayground\Example\Actions'
    permission:
      entities_folder: "%kernel.project_dir%/src/ApiePlayground/Permission/Resources/"
      entities_namespace: 'App\ApiePlayground\Permission\Resources'
      actions_folder: "%kernel.project_dir%/src/ApiePlayground/Permission/Actions/"
      actions_namespace: 'App\ApiePlayground\Permission\Actions'
```

Or you could scan_bounded_contexts to auto-register new bounded contexts by just adding a new directory:
```yaml
# config/packages/apie.yaml
apie:
  scan_bounded_contexts:
    search_path: "%kernel.project_dir%/src/ApiePlayground"
    search_namespace: 'App\ApiePlayground'
```
You can always run ```bin/console config:dump-reference apie``` to see all available options.
### Recommended Symfony bundles.
Because of Symfony's modular structure it is recommended to add some bundles and settings active in the Symfony application with Apie.

- Package apie/cms adds an admin panel for Apie and it uses CSRF for form submits, however CSRF is a setting inside the Symfony framework bundle.
- Some settings are also required from the security bundle, for example logging in with Apie should also mark the login in Symfony. Technically you can run Apie without these generic Symfony bundles, but it's recommended not to run Apie without the framework bundle and security bundle enabled.
- The Doctrine Bundle is also not a hard requirement even if you use apie/doctrine-entity-datalayer package for storing Apie resources, but if you do add it you can have a query log of all executed queries of a request.

### Doctrine bundle linking
Apie creates his own Dotrine entity manager if using the Doctrine Entity datalayer. Since this one is not managed by
the Doctrine Bundle, you need to configure linking Apie with Doctrine. Doing so will make the doctrine console commands work with --em=apie_manager.
Also if the Symfony webprofiler is installed you can see all the queries that Apie executed in the request.

Apie's default value is to link it with the Doctrine Bundle if the DoctrineBundle class exists. This linking can be disabled in apie.yaml with:

```yaml
apie:
    enable_doctrine_bundle_connection: false
```

Without the Doctrine bundle, a programmer has to configure his own setting for database migrations. It's not recommended
to enable the automatic database migrations in production.

### apie/cms customization
The dashboard and error pages rendered by apie/cms are configurable Twig templates. You can point them to your
own templates in `apie.yaml`:

```yaml
apie:
    cms:
        dashboard_template: '@Apie/dashboard.html.twig'
        error_template: '@Apie/error.html.twig'
```

#### Overwrite apie/cms templates
Since these templates are shipped as regular bundle templates, you can also override them the standard Symfony way
by placing a file with the same name under `templates/bundles/ApieBundle/` in your application, without changing
the configuration above.
