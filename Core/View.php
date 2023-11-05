<?php

namespace Core;

/**
 * View
 *
 * PHP version 7.2.0
 */
class View
{

    static $twig = null;
    /**
     * Render a view file
     *
     * @param string $view  The view file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/App/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function renderTemplate($template, $args = [])
    {
        echo static::getTemplate($template, $args);
    }

    /**
     * Get the contents of a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return string
     */
    public static function getTemplate($template, $args = [])
    {
      //  static $twig = null;

        if (self::$twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
            self::$twig = new \Twig\Environment($loader);
            self::$twig -> addGlobal('flash_messages', \App\Flash::getMessages());
        }

        return self::$twig->render($template, $args);
    }
}
