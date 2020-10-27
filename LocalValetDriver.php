<?php

class LocalValetDriver extends BasicValetDriver
{
    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param string $sitePath
     * @param string $siteName
     * @param string $uri
     * @return string
     */
    public function frontControllerPath($sitePath, $siteName, $uri)
    {
        $uri = $this->rewriteMultisite($sitePath, $uri);
        $_SERVER['PHP_SELF'] = $uri;
        $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
        if (0 === strpos($uri, '/wp/')) {
            return is_dir($sitePath.'/public'.$uri)
                ? $sitePath.'/public'.$this->forceTrailingSlash($uri).'/index.php'
                : $sitePath.'/public'.$uri;
        }
        return $sitePath.'/public/index.php';
    }

    /**
     * Determine if the incoming request is for a static file.
     *
     * @param string $sitePath
     * @param string $siteName
     * @param string $uri
     * @return string|false
     */
    public function isStaticFile($sitePath, $siteName, $uri)
    {
        $uri = $this->rewriteMultisite($sitePath, $uri);
        $staticFilePath = $sitePath.'/public'.$uri;
        if ($this->isActualFile($staticFilePath)) {
            return $staticFilePath;
        }
        return false;
    }

    /**
     * Determine if the driver serves the request.
     *
     * @param string $sitePath
     * @param string $siteName
     * @param string $uri
     *
     * @return bool
     */
    public function serves($sitePath, $siteName, $uri)
    {
        return is_dir($sitePath.'/public/app/')
            && is_dir($sitePath.'/public/wp/')
            && file_exists($sitePath.'/public/wp-config.php')
            && file_exists($sitePath.'/env.php');
    }

    /**
     * Redirect to uri with trailing slash.
     *
     * @param string $uri
     * @return string
     */
    protected function forceTrailingSlash($uri)
    {
        if ('/wp/wp-admin' == substr($uri, -1 * strlen('/wp/wp-admin'))) {
            header('Location: '.$uri.'/');
            die;
        }
        return $uri;
    }

    /**
     * Determine if the application is Multisite.
     *
     * @param $sitePath
     * @return bool
     */
    protected function isMultisite($sitePath)
    {
        $app = file_get_contents($sitePath.'/env.php');
        return (bool) preg_match("/define\(\s*('|\")MULTISITE\\1\s*,\s*true\s*\)/mi", $app);
    }

    /**
     * Imitate the rewrite rules for a multisite .htaccess.
     *
     * @param $sitePath
     * @param $uri
     * @return string
     */
    protected function rewriteMultisite($sitePath, $uri)
    {
        if (!$this->isMultisite($sitePath)) {
            return $uri;
        }
        if (preg_match('#^(/[^/]+)?(?!/wp-json)(/wp-.*)#', $uri, $matches) || preg_match('#^(/[^/]+)?(/.*\.php)#', $uri, $matches)) {
            return "/wp{$matches[2]}";
        }
        return $uri;
    }
}
