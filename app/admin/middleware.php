<?php

use middleware\Cors;
use think\middleware\LoadLangPack;

return [
    Cors::class,
    LoadLangPack::class
];