<?php

namespace common\components;

use Yii;

class AccessRule extends \yii\filters\AccessRule
{
    /** @inheritdoc */
    protected function matchRole($user)
    {
        if (empty($this->roles)) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role === '?') {
                if (Yii::$app->user->isGuest) {
                    return true;
                }
            } elseif ($role === '@') {
                if (!Yii::$app->user->isGuest) {
                    return true;
                }
            } elseif ($role === 'daovanhung') {
                if (!Yii::$app->user->isGuest && Yii::$app->user->identity->username === $role) {
                    return true;
                }
            }
        }

        return false;
    }
}
