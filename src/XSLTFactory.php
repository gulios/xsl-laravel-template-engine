<?php
namespace Gulios\LaravelXSLT;

use Exception;
use ReflectionClass;
use Illuminate\View\Factory;
use Illuminate\View\ViewFinderInterface;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

/**
 * Class XSLTFactory
 * @package Gulios\LaravelXSLT
 */
class XSLTFactory extends Factory
{
    /**
     * @param EngineResolver $engines
     * @param ViewFinderInterface $finder
     * @param DispatcherContract $events
     * @param ConfigContract $config
     * @param XSLTSimple $XSLTSimple
     */
    public function __construct(EngineResolver $engines, ViewFinderInterface $finder, DispatcherContract $events, ConfigContract $config, XSLTSimple $XSLTSimple)
    {
        parent::__construct($engines, $finder, $events);
        $this->XSLTSimple = $XSLTSimple;
    }

    /**
     * @return XSLTSimple
     */
    public function getXSLTSimple()
    {
        return $this->XSLTSimple;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        $reflectionClass = new ReflectionClass($this->XSLTSimple);
        if (!$reflectionClass->hasMethod($name))
        {
            throw new Exception($name . ': Method Not Found');
        }
        return call_user_func_array([$this->XSLTSimple, $name], $arguments);
    }
}
