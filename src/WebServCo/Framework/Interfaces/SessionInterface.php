<?php

namespace WebServCo\Framework\Interfaces;

interface SessionInterface
{
    public function clear($setting);
    public function destroy();
    public function get($setting, $defaultValue = false);
    public function has($setting);
    public function regenerate();
    public function remove($setting);
    public function set($setting, $value);
    public function start($storagePath = null);
}
