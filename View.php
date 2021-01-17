<?php

namespace app\core;

/**
 * Class View
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

class View
{
    public $title = '';

    public static function renderPartial($view, $params = [])
    {
        extract($params); // Saving params keys as variables
        ob_start();
        include Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }

    public function renderView($view, $params = [])
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function renderContent($viewContent)
    {
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected function layoutContent()
    {
        if (Application::$app->controller) {
            $layout = Application::$app->controller->layout;
        } else {
            $layout = Application::$app->layout;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();
    }

    public function renderOnlyView($view, $params = [])
    {
        extract($params); // Saving params keys as variables
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }
}