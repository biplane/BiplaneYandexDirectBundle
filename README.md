# BiplaneYandexDirectBundle

This bundle integrates the [biplane-yandex-direct](https://github.com/biplane/yandex-direct) 
library with Symfony project.

## Installation

Use [Composer](https://getcomposer.org/) to install this bundle:

```bash
$ composer require biplane/yandex-direct-bundle
```

Add the bundle in your application kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Biplane\Bundle\YandexDirectBundle\BiplaneYandexDirectBundle(),
    );
}
```

## Full configuration

```yaml
biplane_yandex_direct:
    # The locale for localize message of errors.
    locale:               ru # One of "ru"; "en"; "ua"    
    user:

        # The access token for OAuth authorization
        access_token:         ~ # Required

        # The Yandex's login. Required when the master_token is set.
        login:                ~

        # The master token needs for finance operations.
        master_token:         ~
        
    dump_listener:
        enabled:              false
        directory:            '%kernel.cache_dir%/api_dumps'
        dump:                 all # One of "all"; "only-fail"
```

## License

This bundle is under the MIT license, see the [LICENSE](Resources/meta/LICENSE) file for details.
