<?php

namespace WebServCo\Framework\Interfaces;

interface SettingsInterface
{
    public function setSetting($key, $value);
    public function setting($key, $defaultValue = false);
}
