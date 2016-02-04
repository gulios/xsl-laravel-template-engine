<?php
namespace Gulios\LaravelXSLT;

/**
 * Class XSLTSimple
 * @package Gulios\LaravelXSLT
 */
final class XSLTSimple extends \SimpleXMLElement
{
    /**
     * @param $data
     * @param $rowTagName
     * @param bool $attributes
     * @return $this
     */
    private function addDataToXml($data, $attributes = false)
    {
        foreach ($data as $key => $value)
        {
            // clean key names
            $key = preg_replace('/[\W]/', '', $key);
            if ('' === $key or is_numeric($key))
            {
                $key = 'item';
            }

            if (is_array($value) or is_object($value))
            {
                $xml_child = $this->addChild($key);
                $xml_child->addDataToXml($value, $attributes);
            }
            else
            {
                if (true === $attributes)
                {
                    $this->addAttribute($key, htmlentities($value));
                }
                else
                {
                    $this->addChild($key, htmlentities($value));
                }
            }
        }
        return $this;
    }

    /**
     * @param $data
     * @param $tagName
     * @param string $rowTagName
     * @param bool $attributes
     * @return mixed
     */
    public function addData($data, $tagName, $attributes = false)
    {
        $xml_data = $this->addChild($tagName);

        return $xml_data->addDataToXml($data, $attributes);
    }
}