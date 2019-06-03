<?php

namespace app\modules\api;
use Yii;

/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\api\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        
        $headers = Yii::$app->request->headers;

        // возвращает значения заголовка Accept
        $accept = $headers->get('Accept-Language');
        
        
        
    }
}
