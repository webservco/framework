<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Framework as Fw;

class OutputLoader
{
    final public function __construct()
    {
    }
    
    public function html($data, $template)
    {
        Fw::output('html')->setTemplate($template);
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                Fw::output('html')->setData($key, $value);
            }
        }
        return Fw::output('html')->render($template);
    }
    
    public function json($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                Fw::output('json')->setData($key, $value);
            }
        }
        return Fw::output('json')->render();
    }
}
