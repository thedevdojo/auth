<?php

namespace Devdojo\Auth\Traits;

trait HasConfigs
{
    public $appearance = [];

    public $language = [];

    public $settings = [];

    public function loadConfigs()
    {
        $this->appearance = $this->configToArrayObject('devdojo.auth.appearance');
        $this->language = $this->configToArrayObject('devdojo.auth.language');
        $this->settings = $this->configToArrayObject('devdojo.auth.settings');
    }

    private function configToArrayObject($configPath)
    {
        $configArray = config($configPath);

        return $this->arrayToObject($configArray);
    }

    private function arrayToObject($array)
    {
        if (! is_array($array)) {
            return $array;
        }

        $object = new \stdClass;
        foreach ($array as $key => $value) {
            $object->$key = $this->arrayToObject($value);
        }

        return $object;
    }
}
