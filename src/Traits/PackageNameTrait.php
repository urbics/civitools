<?php

namespace Urbics\Civitools\Traits;

trait PackageNameTrait
{
    protected $packageName = 'urbics/civitools';

    public function packageName()
    {
        return $this->packageName;
    }

    public function packagePath()
    {
        return base_path("vendor/{$this->packageName}");
    }
}
