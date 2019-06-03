<?php

namespace app\modules\api;
use Yii;

use app\models\UsersDeviceTokens;
use app\models\AppLogs;
/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\api\controllers';
    public $appLogId = null;
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        
        $headers = Yii::$app->request->headers;

        // возвращает значения заголовка Accept
        $accept = $headers->get('Accept-Language');
        
        // custom initialization code goes here
        
        if ($accept && in_array($accept, ['ru', 'en', 'es'])) {
           Yii::$app->language = $accept;
           
            $token_model = UsersDeviceTokens::find()->where(['user_id'=>Yii::$app->user->identity->id])->one();
            if($token_model) {
                $token_model->lang = $accept;
                $token_model->save(false);
            }
            
        }
        
        // Игнорируем локальные запросы
        if(stristr(Yii::$app->request->remoteIP, "192.168.100")===false) {
            $log = [
                'request'=>Yii::$app->request->pathInfo,
                'queries'=>(count(Yii::$app->request->queryParams)?json_encode(Yii::$app->request->queryParams):Yii::$app->request->rawBody),
                'ip'=>Yii::$app->request->remoteIP,
                'user_id'=>Yii::$app->user->identity->id,
                'user_agent'=>Yii::$app->request->userAgent,
                'date'=>date('Y-m-d H:i:s'),
            ];
            $model_log = new AppLogs();
            if($model_log->load($log, '')) {
                $model_log->save();
                $this->appLogId = $model_log->id;
            }
        }
        
        
    }
}
