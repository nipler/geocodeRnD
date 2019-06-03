<?php


namespace app\components;

use yii\base\Widget;


class TranslitWidget extends Widget
{
    public $rus;
    public $lat;
    public $str;
    private $route;

    public function init(){
        parent::init();
     
        mb_http_input('UTF-8');
        mb_http_output('UTF-8');
        mb_internal_encoding("UTF-8");
        
        $this->rus = [
            'ж',
            'ч',  
            'ш', 
            'щ',
            'ю', 
            'я',
            'а', 
            'б', 
            'в', 
            'г', 
            'д', 
            'е', 
            'ё',
            'з', 
            'и', 
            'й',
            'к', 
            'л', 
            'м', 
            'н', 
            'о', 
            'п',
            'р',
            'с', 
            'т', 
            'у', 
            'ф', 
            'х', 
            'ц', 
            'ъ', 
            'ы', 
            'ь', 
            'э',
        ];
        $this->lat = [
            'gh',
            'ch', 
            'sh',
            'sch', 
            'yu', 
            'ya',
            'a', 
            'b', 
            'v', 
            'g', 
            'd',
            'e',
            'e', 
            'z', 
            'i', 
            'y', 
            'k', 
            'l', 
            'm', 
            'n', 
            'o',
            'p', 
            'r', 
            's', 
            't', 
            'u', 
            'f', 
            'h', 
            'c', 
            '', 
            'y', 
            '', 
            'e', 
        ];

        
        if(empty($route)) {
            $this->route();
        }
        else {
            $this->route = $route;
        }
        
        //var_dump($this->str);
    }
    // возвращаем результат
    public function run(){

        $string = '';

        if($this->route == 'en-ru') {
            $string = str_ireplace($this->lat, $this->rus, $this->str);
        }
        elseif($this->route == 'ru-en') {
            $string = str_ireplace($this->rus, $this->lat, $this->str);
        }
        else {
            throw new Exception('Неизвестное направление транслитерации');
        }

        return $string;
    }


    private function route()
    {
        if(preg_match('#[A-Za-z]+#', $this->str)) {
            $this->route = 'en-ru';
        }
        else {
            $this->route = 'ru-en';
        }
    }
}