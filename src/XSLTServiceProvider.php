<?php
namespace Gulios\LaravelXSLT;

use Illuminate\Support\ServiceProvider;

/**
 * Class XSLTServiceProvider
 * @package Gulios\LaravelXSLT
 */
class XSLTServiceProvider extends ServiceProvider
{
    /**
     *
     */
    public function register()
    {
        $this->app->singleton('view', function ($app) {
            $factory = new XSLTFactory($app['view.engine.resolver'], $app['view.finder'], $app['events'], $this->app['config'], new XSLTSimple('<App/>'));
            $factory->setContainer($app);
            return $factory;
        });

        $this->app['view']->addExtension('xsl', 'xslt', function ()
        {
            return new XSLTEngine($this->app['view']->getXSLTSimple());
        });
    }
}
