# Blade templating engine for WordPress

The simple yet powerful Blade templating engine ported to WordPress for themes developers.

# Requirements
Before proceeding to installation, please make sure your environment met these requirements:

* [PHP](https://secure.php.net/manual/en/install.php) >= 7.1+
* [Composer](https://getcomposer.org/download/)
* [WordPress](https://wordpress.org/) >= 4.5+

# Installation
```composer require devmium/blade-wp```

# Usage
```php
use Devmium\Blade\WordPressBlade;

// Initialize WordPressBlade instance
$blade = WordPressBlade::getInstance();

// Render a specific template
WordPressBlade::render($view, $with = []);

// Register a custom directive
WordPressBlade::directive($name, $handler);
```

After initialing WordPressBlade service (usually in theme's functions.php file), it will automatically attempt to load template file first from **theme/views/{TEMPLATE_FILE}.blade.php** then fallback to **theme/{TEMPLATE_FILE}.php**

# Examples

Giving the following directory structure:
```
theme
- assets/
- includes/
- views/
    - single.blade.php
    - archive.blade.php
    - index.blade.php
- index.php
- style.css
```

Assuming a single post request (is_single() === true), the service will load **theme/views/single.blade.php**. If **theme/views/single.blade.php** doesn't exist, the service will fallback to **theme/single.php** and then **theme/index.php**

# Built-in WordPress custom directives

Simple loop:
---
```blade
@wp_posts()
    {{ the_title() }}
    {!! the_content() !!}
@wp_empty()
    {{ 'No posts' }}
@wp_end()
```

# Notes
TBD

# Blade documentation
You can browse [Blade's documentation](https://laravel.com/docs/5.7/blade) directly from Laravel documentation.
