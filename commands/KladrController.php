<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;


use yii\console\Controller;
use app\models\StreetCoords;
use Yii;


class KladrController extends Controller
{
   
    public $letters = ['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Э', 'Ю', 'Я', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0];
    public function actionImport()
    {
        // Инициализация api, в качестве параметров указываем токен и ключ для доступа к сервису
        $api = new \Kladr\Api('51dfe5d42fb2b43e3300006e', '86a2c2a06f1b2451a87d05512cc2c3edfdf41969');

        $count = 0;
        $count_query = 0;

        foreach($this->letters as $letter) {
            $query = new \Kladr\Query();
            $query->ContentName = $letter;
            $query->ParentType  = \Kladr\ObjectType::City;
            $query->ParentId    = "6100000100000";
            $query->ContentType = \Kladr\ObjectType::Street;
            $query->WithParent = FALSE;
            $query->Limit      = 400;
            // Получение данных в виде ассоциативного массива
            $streets = $api->QueryToArray($query);
            
            $count += count($streets);
            foreach($streets as $street) {

                $street_name = $street['typeShort'].'. '.$street['name'];
                echo "{$street_name} \r\n";
                $query1 = new \Kladr\Query();
                $query1->ContentName = '';
                $query1->ParentType  = \Kladr\ObjectType::Street;
                $query1->ParentId    = $street['id'];
                $query1->ContentType = \Kladr\ObjectType::Building;
                $query1->WithParent = FALSE;
                $query1->Limit      = 400;
                // Получение данных в виде ассоциативного массива
                $buildings = $api->QueryToArray($query1);
                if(count($buildings)) {
                    foreach($buildings as $building) {
                        $building['name'] = str_replace(['двлд', 'влд'], [], $building['name']);
                        $building['name'] = str_replace('стр', ' стр. ', $building['name']);
                        
                        
                        $str = $street_name.' '.$building['typeShort'].'.'.$building['name'];
                        echo "{$building['typeShort']} {$building['name']} \r\n";
                        $flag = true;
                        $model = StreetCoords::find()->where(['title_string'=>$street['name'], 'building_num'=>$building['name']])->one();
                        if(!$model) {
                            $model = new StreetCoords;
                            $flag = false;
                        }
                        if($flag && !empty($model->lat)) {
                            echo "пропускаем {$street['name']} {$building['name']} \r\n";
                            continue;
                        }
                        $lat = null;
                        $lon = null;
                       
                        // Делаем запрос на получение координат
                        $coords = @file_get_contents("https://geocode-maps.yandex.ru/1.x/?format=json&apikey=bd291b9c-f00b-4a83-8f23-b6179d11e2bc&geocode=Ростов-на-Дону, $str");
                        
                        $coords = json_decode($coords, true);
                        if(count($coords['response']['GeoObjectCollection']['featureMember'])) {
                            
                            $pos = $coords['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
                            $pos = explode(" ", $pos);
                            $lat = $pos[1];
                            $lon = $pos[0];
                        }
                        else {
                            print_r($coords);
                            echo "Не удалось получить координаты \r\n";
                        }
                            
                        

                        $model->title_type = $street['typeShort'];
                        $model->title_string = $street['name'];
                        $model->building_type = $building['typeShort'];
                        $model->building_num = $building['name'];
                        $model->lat = $lat;
                        $model->lon = $lon;
                        $model->save(false);
                    }
                }
                else {

                    $flag = true;
                    $model = StreetCoords::find()->where(['title_string'=>$street['name']])->one();
                    if(!$model) {
                        $model = new StreetCoords;
                        $flag = false;
                    }
                    if($flag && !empty($model->lat)) {
                        echo "пропускаем {$street['name']} \r\n";
                        continue;
                    }
                    $lat = null;
                    $lon = null;
                    
                    $coords = @file_get_contents("https://geocode-maps.yandex.ru/1.x/?format=json&apikey=bd291b9c-f00b-4a83-8f23-b6179d11e2bc&geocode=Ростов-на-Дону, $street_name");
            
                    $coords = json_decode($coords, true);
                    if(count($coords['response']['GeoObjectCollection']['featureMember'])) {
                        
                        $pos = $coords['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
                        $pos = explode(" ", $pos);
                        $lat = $pos[1];
                        $lon = $pos[0];
                    }
                    else {
                        print_r($coords);
                        echo "Не удалось получить координаты \r\n";
                    }
                        
                    

                    $model->title_type = $street['typeShort'];
                    $model->title_string = $street['name'];
                    $model->building_type = null;
                    $model->building_num = null;
                    $model->lat = $lat;
                    $model->lon = $lon;
                    $model->save(false);
                }
            } 
        } 
    }
}
