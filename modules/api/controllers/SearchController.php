<?php


namespace app\modules\api\controllers;
 
use yii\filters\VerbFilter;
use yii\web\Response;
use app\models\search\StreetCoordsSearch;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use Yii;

class SearchController extends \yii\rest\ActiveController
{

    public $modelClass = 'StreetCords';

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['delete'], $actions['create'], $actions['view'], $actions['update'], $actions['index']);
        
        $actions['options'] = [
            'class' => 'yii\rest\OptionsAction',
            // optional:
            'collectionOptions' => ['GET', 'POST', 'HEAD', 'OPTIONS'],
            'resourceOptions' => ['GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        ];
        return $actions;
    }


    public static function allowedDomains()
    {
        return [
            '*',
        ];
    }

    public function behaviors()
    {
        return ArrayHelper::merge([
            [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => $this->allowedDomains(),
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                ],
            ],
        ], parent::behaviors());
    }


    public function actionStreet() {
        $searchModel = new StreetCoordsSearch();
		
        $request = Yii::$app->request;
        $filter = $request->post(); 
      

        $dataStreets = $searchModel->apiSearchStreets($filter);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        return $dataStreets;

    }

    public function actionLatlng() {
        $searchModel = new StreetCoordsSearch();
		
        $request = Yii::$app->request;
        $filter = $request->post(); 

        $dataCoords = $searchModel->apiSearchLatLng($filter);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        return $dataCoords;

    }
}