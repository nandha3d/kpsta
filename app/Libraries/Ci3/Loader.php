<?php

namespace App\Libraries\Ci3;

use Closure;
use App\Libraries\Ci3\Database\Connection as Ci3Connection;
use Config\Database;
use RuntimeException;

/**
 * CodeIgniter 3's $this->load.
 *
 * Views are rendered by including the file with $this bound to the controller,
 * which is what CI3 did. That is not cosmetic: 22 view files reach for
 * $this->uri->segment(), $this->session->userdata() and $this->aauthGroupId
 * directly. Rendering them through CI4's View service would bind $this to the
 * renderer instead and break every one of those.
 */
class Loader
{
    /** @var array<string, mixed> Data shared with every subsequent view. */
    private array $sharedVars = [];

    /**
     * @var array<string, mixed> Data passed to any view so far this request.
     *                           CI3 kept this and made it visible to later views.
     */
    private array $cachedVars = [];

    /** @var array<string, object> */
    private array $loadedModels = [];

    /** @var array<string, object> */
    private array $loadedLibraries = [];

    /** @var array<string, Ci3Connection> */
    private array $connections = [];

    public function __construct(private object $controller)
    {
    }

    /**
     * Load a model and expose it on the controller under its CI3 name.
     *
     * @param string      $model  e.g. 'news_model' or 'membership/Config_model'
     * @param string|null $alias  Property name to use instead of the class name.
     */
    public function model($model, $alias = '', $dbConn = false): object
    {
        if (is_array($model)) {
            foreach ($model as $m) {
                $this->model($m);
            }

            return $this->controller;
        }

        $model = str_replace('.php', '', trim($model, '/'));
        $parts = explode('/', $model);
        $name  = array_pop($parts);

        $property = $alias !== '' ? $alias : $name;

        if (isset($this->loadedModels[$property])) {
            $this->controller->{$property} = $this->loadedModels[$property];

            return $this->controller;
        }

        $namespace = 'App\\Models';
        foreach ($parts as $segment) {
            $namespace .= '\\' . ucfirst($segment);
        }

        // CI3 capitalised the class name but exposed the model under the name
        // as written, so load->model('news_model') gave $this->news_model
        // backed by class News_model. Both spellings are in use here.
        $class = $namespace . '\\' . ucfirst($name);

        if (! class_exists($class)) {
            $fallback = $namespace . '\\' . $name;

            if (! class_exists($fallback)) {
                throw new RuntimeException(sprintf(
                    'Unable to locate the model "%s" (looked for %s).',
                    $model,
                    $class
                ));
            }

            $class = $fallback;
        }

        $instance = new $class();

        $this->loadedModels[$property] = $instance;
        $this->controller->{$property}  = $instance;

        return $this->controller;
    }

    /**
     * Load a library and expose it on the controller.
     *
     * CI3 lower-cased the property name, so load->library('Aauth') became
     * $this->aauth. That is preserved.
     */
    public function library($library, $params = null, $objectName = null): object
    {
        if (is_array($library)) {
            foreach ($library as $lib) {
                $this->library($lib, $params);
            }

            return $this->controller;
        }

        $library = str_replace('.php', '', trim($library, '/'));
        $parts   = explode('/', $library);
        $name    = array_pop($parts);

        $property = $objectName ?? strtolower($name);

        if (isset($this->loadedLibraries[$property])) {
            $this->controller->{$property} = $this->loadedLibraries[$property];

            return $this->controller;
        }

        // CI3's built-in libraries. Some already hang off the controller, some
        // map onto a shim, and a couple ('driver' in particular, which Aauth
        // loads purely as a CI 2.x dependency) have no CI4 counterpart and are
        // simply nothing to do.
        switch (strtolower($name)) {
            case 'database':
                $this->database();

                return $this->controller;

            case 'driver':
            case 'user_agent':
                return $this->controller;

            case 'session':
            case 'form_validation':
            case 'pagination':
                // Set up by BaseController for every request already.
                return $this->controller;

            case 'email':
                $this->controller->email = new Email();

                return $this->controller;

            case 'upload':
                $this->controller->upload = new Upload(is_array($params) ? $params : []);

                return $this->controller;
        }

        $candidates = [
            'App\\Libraries\\' . $name,
            'App\\Libraries\\' . ucfirst($name),
        ];

        $class = null;
        foreach ($candidates as $candidate) {
            if (class_exists($candidate)) {
                $class = $candidate;
                break;
            }
        }

        if ($class === null) {
            throw new RuntimeException(sprintf('Unable to locate the library "%s".', $library));
        }

        $instance = $params === null ? new $class() : new $class($params);

        $this->loadedLibraries[$property] = $instance;
        $this->controller->{$property}     = $instance;

        return $this->controller;
    }

    /**
     * Render a view.
     *
     * @param string $view   Path under app/Views, without the extension.
     * @param array  $data   Data for this render, merged over the shared vars.
     * @param bool   $return TRUE to get the markup back instead of buffering it.
     */
    public function view(string $view, array $data = [], bool $return = false)
    {
        $file = APPPATH . 'Views/' . ltrim(str_replace('.php', '', $view), '/') . '.php';

        if (! is_file($file)) {
            throw new RuntimeException(sprintf('Unable to load the requested file: Views/%s.php', $view));
        }

        // CI3 accumulated view data for the whole request: every array passed to
        // view() was merged into a cache that later views also saw. Several
        // views here depend on it -- admin/download/download.php reads
        // $menuType, which is only ever passed to the admin/download/content
        // partial rendered earlier in the same request. Dropping that would
        // turn those into undefined variables.
        if ($data !== []) {
            $this->cachedVars = array_merge($this->cachedVars, $data);
        }

        $merged = array_merge($this->sharedVars, $this->cachedVars, $data);

        // Bind $this to the controller so views keep CI3's semantics.
        $render = Closure::bind(
            function (string $__ci_path, array $__ci_data): string {
                extract($__ci_data, EXTR_SKIP);
                ob_start();

                try {
                    include $__ci_path;

                    return (string) ob_get_clean();
                } catch (\Throwable $e) {
                    ob_end_clean();

                    throw $e;
                }
            },
            $this->controller,
            $this->controller::class
        );

        $output = $render($file, $merged);

        if ($return) {
            return $output;
        }

        $this->controller->appendOutput($output);

        return $this->controller;
    }

    /**
     * Data shared with every view rendered afterwards (CI3's load->vars()).
     *
     * @param string|array $vars
     */
    public function vars($vars, $value = null): object
    {
        if (is_array($vars)) {
            $this->sharedVars = array_merge($this->sharedVars, $vars);
        } else {
            $this->sharedVars[$vars] = $value;
        }

        return $this->controller;
    }

    /**
     * @return array<string, mixed>
     */
    public function get_vars(): array
    {
        return $this->sharedVars;
    }

    public function get_var(string $key)
    {
        return $this->sharedVars[$key] ?? null;
    }

    /**
     * CI4 autoloads helpers by name; this keeps the CI3 call sites valid.
     */
    public function helper($helpers): object
    {
        foreach ((array) $helpers as $name) {
            $name = str_replace(['_helper', '.php'], '', trim($name, '/'));

            // url/form/text and friends resolve to CI4's own helpers; the
            // application's live in app/Helpers with the same naming.
            helper($name);
        }

        return $this->controller;
    }

    /**
     * Connect to a database group.
     *
     * @param string $group     Connection group; '' means the default.
     * @param bool   $returnObj TRUE to hand back the connection instead of
     *                          assigning it to $this->db.
     */
    public function database($group = '', bool $returnObj = false, bool $queryBuilder = true)
    {
        $key = $group === '' ? 'default' : $group;

        if (! isset($this->connections[$key])) {
            $this->connections[$key] = new Ci3Connection(Database::connect($group === '' ? null : $group));
        }

        if ($returnObj) {
            return $this->connections[$key];
        }

        $this->controller->db = $this->connections[$key];

        return $this->controller;
    }

    /**
     * Load a CI3-style config file from app/Config/Ci3.
     */
    public function config(string $file, bool $useSections = false, bool $failGracefully = false): bool
    {
        $path = APPPATH . 'Config/Ci3/' . str_replace('.php', '', $file) . '.php';

        if (! is_file($path)) {
            if ($failGracefully) {
                return false;
            }

            throw new RuntimeException(sprintf('The configuration file %s.php does not exist.', $file));
        }

        $config = [];
        include $path;

        if ($useSections) {
            Registry::mergeConfig([$file => $config]);
        } else {
            Registry::mergeConfig($config);
        }

        return true;
    }

    /**
     * CI3's load->file(). Rarely used, kept for completeness.
     */
    public function file(string $path, bool $return = false)
    {
        if (! is_file($path)) {
            throw new RuntimeException(sprintf('Unable to load the requested file: %s', $path));
        }

        ob_start();
        include $path;
        $output = (string) ob_get_clean();

        if ($return) {
            return $output;
        }

        $this->controller->appendOutput($output);

        return $this->controller;
    }
}
