<?php

namespace App\Controllers;

use App\Libraries\Ci3\Config as Ci3Config;
use App\Libraries\Ci3\Email as Ci3Email;
use App\Libraries\Ci3\Encrypt as Ci3Encrypt;
use App\Libraries\Ci3\FormValidation;
use App\Libraries\Ci3\Lang;
use App\Libraries\Ci3\Input;
use App\Libraries\Ci3\Loader;
use App\Libraries\Ci3\Output;
use App\Libraries\Ci3\Pagination;
use App\Libraries\Ci3\Registry;
use App\Libraries\Ci3\Session as Ci3Session;
use App\Libraries\Ci3\Uri;
use CodeIgniter\Controller;
use App\Libraries\Ci3\Database\Connection as Ci3Connection;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use Psr\Log\LoggerInterface;

/**
 * Base controller providing the CodeIgniter 3 accessors the application uses.
 *
 * CI3 controllers reached everything through magic properties that its loader
 * populated -- $this->input, $this->uri, $this->session, $this->db,
 * $this->load, $this->output, plus whatever models and libraries had been
 * loaded. CI4 has no such mechanism, so those are set up here and backed by
 * CI4 services.
 *
 * Controllers echo through $this->load->view() rather than returning a string,
 * so output is buffered on the instance and flushed by _remap()'s return.
 */
abstract class BaseController extends Controller
{
    /** @var list<string> Helpers loaded for every request. */
    protected $helpers = ['url', 'form', 'text', 'filetype', 'format'];

    public Input $input;
    public Uri $uri;
    public Ci3Session $session;
    public Loader $load;
    public Output $output;
    public FormValidation $form_validation;
    public Pagination $pagination;
    public Lang $lang;
    public Ci3Config $config;
    public ?Ci3Email $email = null;
    public Ci3Encrypt $encrypt;
    public ?\App\Libraries\Ci3\Upload $upload = null;

    /** CI3-shaped query builder over the CI4 connection. */
    public Ci3Connection $db;

    /** Accumulated view output for this request. */
    private string $bufferedOutput = '';

    /**
     * Set to a ResponseInterface by a controller that wants to bypass the
     * buffered-view path (a file download, say).
     */
    protected ?ResponseInterface $overrideResponse = null;

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        // get_instance() and the CI3-style global helpers resolve through this.
        Registry::setController($this);

        if ($request instanceof IncomingRequest || $request instanceof CLIRequest) {
            $this->input = new Input($request);
        }

        $this->uri             = new Uri();
        $this->session         = new Ci3Session(service('session'));
        $this->load            = new Loader($this);
        $this->output          = new Output($response);
        $this->form_validation = new FormValidation($this);
        $this->pagination      = new Pagination();
        $this->lang            = new Lang();
        $this->config          = new Ci3Config();
        $this->encrypt         = new Ci3Encrypt();
        $this->db              = new Ci3Connection(Database::connect());

        $this->loadCi3Config();

        // CI3 did this work in the controller constructor, but at that point
        // CI4 has not yet attached the request, response or session -- and the
        // access checks in the admin and membership bases need all three. So
        // the ported constructors live in ci3Init() and run here instead.
        $this->ci3Init();
    }

    /**
     * Stands in for the CI3 controller constructor.
     *
     * Subclasses override this and chain with parent::ci3Init() exactly as
     * they chained parent::__construct() before.
     */
    public function ci3Init(): void
    {
    }

    /**
     * Make the ported CI3 config arrays reachable via config_item().
     */
    private function loadCi3Config(): void
    {

        // Both files already namespace their own keys ($config['aauth'] is
        // the whole array), so they load flat rather than under a section.
        $this->config->load('aauth', false, true);
        $this->config->load('mail', false, true);
    }

    /**
     * Collect view output instead of echoing it, so the framework still owns
     * the response.
     */
    public function appendOutput(string $output): void
    {
        $this->bufferedOutput .= $output;
    }

    public function getBufferedOutput(): string
    {
        return $this->bufferedOutput;
    }

    /**
     * Route every action through here so buffered view output becomes the
     * response body.
     *
     * Controllers written for CI3 return nothing and rely on the loader having
     * echoed; some also echo JSON directly and exit. Both work: an explicit
     * return wins, buffered output is used otherwise.
     */
    public function _remap(string $method, ...$params)
    {
        if (! method_exists($this, $method)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forMethodNotFound($method);
        }

        $result = $this->{$method}(...$params);

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        if ($this->overrideResponse instanceof ResponseInterface) {
            return $this->overrideResponse;
        }

        if (is_string($result) && $result !== '') {
            return $this->response->setBody($this->bufferedOutput . $result);
        }

        if ($this->bufferedOutput !== '') {
            return $this->response->setBody($this->bufferedOutput);
        }

        return $result;
    }
}
