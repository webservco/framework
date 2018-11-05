<?php
namespace WebServCo\Framework\Interfaces;

interface ConfigInterface
{
    public function get($setting, $defaultValue = false);
    public function load($setting, $pathProject);
}
