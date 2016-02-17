<?php
namespace Gulios\LaravelXSLT;

use Illuminate\View\Engines\EngineInterface;
use Illuminate\Support\Facades\URL;
use Request;
use App;
use Illuminate\Support\Facades\Route;

/**
 * Class XSLTEngine
 * @package Gulios\LaravelXSLT
 */
class XSLTEngine implements EngineInterface
{
    /**
     * @param string $path
     * @param array $data
     * @return string
     */
    public function get($path, array $data = [])
    {
        return $this->evaluatePath($path, $data);
    }

    /**
     * @param XSLTSimple $XSLTSimple
     */
    public function __construct(XSLTSimple $XSLTSimple)
    {
        $this->XSLTSimple = $XSLTSimple;
    }

    /**
     * @param $path
     * @param array $data
     * @return string
     */
    protected function evaluatePath($path, array $data = [])
    {
        $preferences = $this->XSLTSimple->addChild('Preferences');
        $url = $preferences->addChild('url');
        $url->addAttribute('isHttps', Request::secure());
        $url->addAttribute('currentUrl', Request::url());
        $url->addAttribute('baseUrl', URL::to(''));
        $url->addAttribute('previousUrl', URL::previous());

        $server = $preferences->addChild('server');
        $server->addAttribute('curretnYear', date('Y'));
        $server->addAttribute('curretnMonth', date('m'));
        $server->addAttribute('curretnDay', date('d'));
        $server->addAttribute('currentDateTime', date('Y-m-d H:i:s'));

        $language = $preferences->addChild('language');
        $language->addAttribute('current', App::getLocale());

        $default_language = \Config::get('app.default_language');
        if (isset($default_language)) {
            $language->addAttribute('default', $default_language);
        }

        $languages = \Config::get('app.available_languages');

        if (is_array($languages)) {

            foreach ($languages as $lang) {

                $language->addChild('item', $lang);
            }
        }

        // from form generator
        if (isset($data['form']))
        {
            $this->XSLTSimple->addChild('Form', form($data['form']));
        }

        // adding form errors to xml
        if (isset($data['errors']))
        {
            $this->XSLTSimple->addData($data['errors']->all(), 'FormErrors', false);
        }

        // "barryvdh/laravel-debugbar":
        // adding XML tab
        if (true === class_exists('Debugbar'))
        {
            $dom = dom_import_simplexml($this->XSLTSimple)->ownerDocument;
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $prettyXml = $dom->saveXML();

            // add new tab and append xml to it
            if (false === \Debugbar::hasCollector('XML'))
            {
                \Debugbar::addCollector(new \DebugBar\DataCollector\MessagesCollector('XML'));
            }
            \Debugbar::getCollector('XML')->addMessage($prettyXml, 'info', false);
        }

        $xsl_processor = new \XsltProcessor();
        $xsl_processor->registerPHPFunctions();
        $xsl_processor->importStylesheet(simplexml_load_file($path));
        return $xsl_processor->transformToXML($this->XSLTSimple);
    }
}
