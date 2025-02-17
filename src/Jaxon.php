<?php

namespace Jaxon\AjaxBundle;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Jaxon
{
    use \Jaxon\Sentry\Traits\Armada;

    /**
     * The application debug option
     *
     * @var bool
     */
    protected $debug;

    /**
     * The template engine
     *
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $template;

    /**
     * The bundle configuration
     *
     * @var array
     */
    public $configs;

    /**
     * Create a new Jaxon instance.
     *
     * @return void
     */
    public function __construct($template, $configs, $debug)
    {
        $this->template = $template;
        // The application debug option
        $this->configs = $configs;
        // The application debug option
        $this->debug = $debug;
        // Initialize the Jaxon plugin
        $this->_jaxonSetup();
    }

    /**
     * Set the module specific options for the Jaxon library.
     *
     * @return void
     */
    protected function jaxonSetup()
    {
        // The application URL
        $baseUrl = '//' . $_SERVER['SERVER_NAME'];
        // The application web dir
        $baseDir = $_SERVER['DOCUMENT_ROOT'];

        // Jaxon library settings
        $jaxon = jaxon();
        $sentry = $jaxon->sentry();
        $jaxon->setOptions($this->configs, 'lib');

        /// Jaxon application settings
        $this->appConfig = $jaxon->newConfig();
        $this->appConfig->setOptions($this->configs, 'app');

        // Jaxon library default settings
        $sentry->setLibraryOptions(!$this->debug, !$this->debug, $baseUrl . '/jaxon/js', $baseDir . '/jaxon/js');

        // Set the default view namespace
        $sentry->addViewNamespace('default', '', '.html.twig', 'twig');
        $this->appConfig->setOption('options.views.default', 'default');

        // Add the view renderer
        $template = $this->template;
        $sentry->addViewRenderer('twig', function () use ($template) {
            return new View($template);
        });

        // Set the session manager
        $sentry->setSessionManager(function () {
            return new Session();
        });
    }

    /**
     * Set the module specific options for the Jaxon library.
     *
     * This method needs to set at least the Jaxon request URI.
     *
     * @return void
     */
    protected function jaxonCheck()
    {
        // Todo: check the mandatory options
    }

    /**
     * Wrap the Jaxon response into an HTTP response.
     *
     * @param  $code        The HTTP Response code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function httpResponse($code = '200')
    {
        // Create and return a Symfony HTTP response
        $response = new HttpResponse();
        $response->headers->set('Content-Type', $this->ajaxResponse()->getContentType());
        $response->setCharset($this->ajaxResponse()->getCharacterEncoding());
        $response->setStatusCode($code);
        $response->setContent($this->ajaxResponse()->getOutput());
        // prints the HTTP headers followed by the content
        $response->send();
    }
}
