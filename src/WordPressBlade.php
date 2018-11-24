<?php

namespace Devmium\Blade;

use Devmium\Blade\Compilers\BladeCompiler;
use Devmium\Blade\Engines\CompilerEngine;

/**
 * Class WordPressBlade
 *
 * This class is used for integration with WordPress templating system.
 *
 * @package Devmium\Blade
 */
class WordPressBlade
{
    /**
     * Singleton instance
     *
     * @var static
     */
    protected static $instance = null;
    /**
     * Compiler
     *
     * @var \Devmium\Blade\Compilers\BladeCompiler
     */
    protected $compiler;
    /**
     * Engine
     *
     * @var \Devmium\Blade\Engines\CompilerEngine
     */
    protected $engine;
    /**
     * Finder
     *
     * @var \Devmium\Blade\FileViewFinder
     */
    protected $finder;
    /**
     * Filesystem
     *
     * @var \Devmium\Blade\Filesystem
     */
    protected $files;
    /**
     * Factory
     *
     * @var \Devmium\Blade\Factory
     */
    protected $factory;
    /**
     * Config
     *
     * @var array
     */
    protected $config = [
        'paths' => [],
    ];

    /**
     * Initialize Blade then hook it with WordPress templating system.
     */
    protected function __construct()
    {
        // Config
        $this->prepareConfig();
        // Prepare environment
        $this->prepareEnvironment();
        // Prepare services
        $this->prepareServices();
        // Register some handy directives
        $this->registerWordPressDirectives();
        // Hook into WordPress templating system
        add_filter('template_include', function ($template) {
            return $this->bindBladeToWordPress($template);
        });

        $templateTypes = [
            'index',
            '404',
            'archive',
            'author',
            'category',
            'tag',
            'taxonomy',
            'date',
            'embed',
            'home',
            'frontpage',
            'page',
            'paged',
            'search',
            'single',
            'singular',
            'attachment',
        ];

        foreach ($templateTypes as $type) {
            add_filter(
                "{$type}_template_hierarchy",
                function ($hierarchy) use ($type) {
                    $basePath = str_replace($this->config['paths']['base'], '', $this->config['paths']['views']);
                    $bladeTemplates = [];

                    foreach ($hierarchy as $template) {
                        $bladeTemplates[] = $basePath . str_replace('.php', '.blade.php', $template);
                        $bladeTemplates[] = $template;
                    }

                    return $bladeTemplates;
                }
            );
        }
    }

    /**
     * Prepare config.
     */
    protected function prepareConfig()
    {
        $this->config['paths']['base'] = trailingslashit(get_template_directory());
        $this->config['paths']['views'] = "{$this->config['paths']['base']}views/";
        $this->config['paths']['cache'] = "{$this->config['paths']['base']}cache/";
        // Hook for developers
        $this->config = apply_filters('blade/config', $this->config);
    }

    /**
     * Prepare environment.
     */
    protected function prepareEnvironment()
    {
        wp_mkdir_p($this->config['paths']['cache']);
    }

    /**
     * Prepare services.
     */
    protected function prepareServices()
    {
        // File system
        $this->files = new Filesystem();
        // Initialize the Blade compiler
        $this->compiler = new BladeCompiler($this->files, $this->config['paths']['cache']);
        // Ready the compiler engine
        $this->engine = new CompilerEngine($this->compiler);
        // Create the file finder
        $this->finder = new FileViewFinder($this->files, [$this->config['paths']['views']]);
        // Create the blade instance
        $this->factory = new Factory($this->engine, $this->finder);
    }

    /**
     * Extend blade with WordPress most used directives.
     *
     * @return void
     */
    protected function registerWordPressDirectives()
    {
        /**
         * WP Query Directives
         */
        $this->compiler->directive('wp_posts', function () {
            return '<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>';
        });
        $this->compiler->directive('wp_query', function ($expression) {
            $php = '<?php $wp_inline_query = new WP_Query' . $expression . '; ';
            $php .= 'if ( $wp_inline_query->have_posts() ) : ';
            $php .= 'while ( $wp_inline_query->have_posts() ) : ';
            $php .= '$wp_inline_query->the_post(); ?> ';

            return $php;
        });
        $this->compiler->directive('wp_empty', function () {
            return '<?php endwhile; ?><?php else: ?>';
        });
        $this->compiler->directive('wp_end', function () {
            return '<?php endif; wp_reset_postdata(); ?>';
        });
    }

    /**
     * Include the template
     *
     * @return string
     */
    protected function bindBladeToWordPress($template)
    {
        // Nothing interesting to process, proceed.
        if (!$template) {
            return $template;
        }

        // Template must be within template path
        if (stripos($template, $this->config['paths']['base']) === false) {
            return $template;
        }

        if (strpos($template, '.blade.php') !== false && $this->files->exists($template)) {
            // Render the view
            $view = str_replace([$this->config['paths']['views'], '.blade.php'], '', $template);
            echo $this->renderView($view);

            // Bail
            return '';
        }

        return $template;
    }

    /**
     * Render a view.
     *
     * @param string $template Path to the template
     * @param array $with Additional variables passed to view
     *
     * @return string           Compiled template
     */
    public function renderView($template, $with = [])
    {
        $template = apply_filters('blade/view/template', $template, $with);
        $with = apply_filters('blade/view/vars', $with, $template);
        $html = apply_filters('blade/view/html', $this->factory->make($template, $with)->render(), $template, $with);

        return $html;
    }

    /**
     * Render a view.
     *
     * @param       $view
     * @param array $with
     *
     * @return string
     */
    public static function render($view, $with = [])
    {
        return static::getInstance()->renderView($view, $with);
    }

    /**
     * Get Blade instance.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Register custom directive
     *
     * @param string $name Directive name.
     * @param callable $handler Directive handler.
     *
     * @return void
     */
    public static function directive($name, $handler)
    {
        static::getInstance()->getCompiler()->directive($name, $handler);
    }

    /**
     * @return BladeCompiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * @return CompilerEngine
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @return FileViewFinder
     */
    public function getFinder()
    {
        return $this->finder;
    }

    /**
     * Prevent clone.
     */
    protected function __clone()
    {

    }
}