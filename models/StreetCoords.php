<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "street_coords".
 *
 * @property int $id
 * @property string $title_type
 * @property string $title_string
 * @property string $building_type
 * @property string $building_num
 * @property string $lat
 * @property string $lon
 * @property int $remote_id
 */
class StreetCoords extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'street_coords';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['remote_id'], 'integer'],
            [['title_type', 'building_type', 'building_num', 'lat', 'lon'], 'string', 'max' => 20],
            [['title_string'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title_type' => 'Title Type',
            'title_string' => 'Title String',
            'building_type' => 'Building Type',
            'building_num' => 'Building Num',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'remote_id' => 'Remote ID',
        ];
    }
}
