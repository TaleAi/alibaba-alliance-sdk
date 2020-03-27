<?php
/**
 * Created by PhpStorm.
 * User: Jarvan
 * Date: 20/3/26
 * Time: 15:52.
 */

namespace AlibabaAllianceSdk;

class APIId
{
    public $namespace;
    public $name;
    public $version;

    public function __construct(string $namespace, string $name, int $version)
    {
        $this->namespace = $namespace;
        $this->name      = $name;
        $this->version   = $version;
    }
}
