<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use app\models\StreetCoords;
use app\components\TranslitWidget;


class StreetCoordsSearch extends Places
{
    public $text;

    
    public function rules()
    {
        return [
            [['text'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    public function apiSearchStreets($params)
    {

        $query = StreetCoords::find();
        
        $this->load($params, '');
        
		if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return [];
        }
        
        $search_string = ['б-р.', 'гск.', 'днп.', 'н/п.', 'пер.', 'пл.', 'пр-кт.', 'сад.', 'снт.', 'ст.', 'тер.', 'тер. ОНТ', 'тер. СНТ', 'ул.', 'б-р', 'бульвар',  'гск', 'днп', 'н/п', 'парк', 'пер', 'переулок', 'пл', 'площадь', 'пр-кт', 'проспект', 'сад', 'снт', 'ст', 'станция', 'тер', 'тер. ОНТ', 'тер. СНТ', 'ул', 'улица', 'стр.', 'стр', 'строение', 'дом.', 'дом', 'д.', 'д', 'пос', 'пос.'];
        $text_arr = explode(" ", $this->text);
   
        $clear_text_ru = [];
        $clear_text_en = [];
        foreach($text_arr as $t) {
            $t = trim($t);
            if(!in_array($t, $search_string)) {
                $t = str_replace($search_string, [], $t);
                $clear_text_ru[] = $t;
                $clear_text_en[] = TranslitWidget::widget(['str' => $t ]);
            }
        }
        
        $query->where(['not', ['lat'=>null, 'lon'=>null]]);
        $query->andFilterWhere([
            'or',
            ['like', 'title_string', $clear_text_ru[0]],
            ['like', 'title_string', $clear_text_en[0]],
        ]);

       if(isset($clear_text_ru[1])) {
            $query->andFilterWhere([
                'or',
                ['like', 'building_num', $clear_text_ru[1]],
                ['like', 'building_num', $clear_text_en[1]],
            ]);
            
       }

        
        $data = $query->asArray()->all();

        $result = [];
        foreach($data as $a=>$row) {
            
            $result[$a] = [
                'id' => (int)$row['id'],
                'title' => $row['title_type'].'. '.$row['title_string'].' '.$row['building_type'].'.'.$row['building_num'],
                'lat' => $row['lat'],
                'lon' => $row['lon'],
                'type' => 'street'
            ];
        }
        return $result;

    }

    public function apiSearchLatLng($params)
    {
        
        $data = StreetCoords::find()->where(['not', ['lat'=>null, 'lon'=>null]])->asArray()->all();

        $result = [];
        foreach($data as $a=>$row) {
            
            $distance = self::distance($row['lat'], $row['lon'], $params['lat'], $params['lon']);
            $result[$distance] = $row['title_type'].'. '.$row['title_string'].(!empty($row['building_type'])?' '.$row['building_type']:'').(!empty($row['building_num'])?'. '.$row['building_num']:'');
        }
        ksort($result);

        return array_shift($result);

    }


    private static function distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo) {

        $rad = M_PI / 180;
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin($latitudeFrom * $rad) * sin($latitudeTo * $rad) +  cos($latitudeFrom * $rad) * cos($latitudeTo * $rad) * cos($theta * $rad);
    
        return round(acos($dist) / $rad * 60 *  1.852 * 1000, 2);
    }
   
}
