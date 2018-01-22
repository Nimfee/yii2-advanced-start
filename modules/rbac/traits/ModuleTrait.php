<?php

namespace modules\rbac\traits;

use Yii;
use modules\rbac\Module;

/**
 * Trait ModuleTrait
 *
 * @property-read Module $module
 * @package modules\rbac\traits
 */
trait ModuleTrait
{
    /**
     * @return Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('rbac');
    }
}
