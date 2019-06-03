## Пример модуля для Yii2 реализующего геокодирование для Ростова-на-Дону:
  - Получить координаты долготы и широты для веденного адреса
  - Получить адрес для веденных координат долготы и широты
### Установка

##### 1. Перед использованием необходимо создать и заполнить таблицу данными
```
CREATE TABLE `street_coords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title_type` varchar(20) DEFAULT NULL,
  `title_string` varchar(250) DEFAULT NULL,
  `building_type` varchar(20) DEFAULT NULL,
  `building_num` varchar(20) DEFAULT NULL,
  `lat` varchar(20) DEFAULT NULL,
  `lon` varchar(20) DEFAULT NULL,
  `remote_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `remote_id` (`remote_id`),
  KEY `title_string_building_num` (`title_string`,`building_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
#### 2. Запустить импорт в консоли
```sh
php yii kladr/import
```

Скрипт распарсит из базы Кладр все улицы города и определит координаты каждой улицы через api геокодера яндекса. Поскольку апи от яндекса платное и позволяет в сутки получить только 25000 координат, то для 10 тыс. улиц Ростова необходимо запускать парсинг в течение 4-х дней.
### Использование

1.Получить координаты улицы
```
/api/search/street
POST: [text=Пушкин 120]
```
Ответ Json
```
[
    {
        "id": 76350,
        "title": "ул. Пушкинская д.120/48",
        "lat": "47.226037",
        "lon": "39.720376",
        "type": "street"
    },
    {
        "id": 76351,
        "title": "ул. Пушкинская д.120А",
        "lat": "47.226123",
        "lon": "39.720609",
        "type": "street"
    }
]
```
2.Найти улицу по координатам
```
/api/search/latlng
POST: [lat:47.226037","lon:39.720376"]
```
Ответ String
```
"ул. Пушкинская д. 120/48"
```

Можно использовать для любого города.