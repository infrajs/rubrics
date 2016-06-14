***Не готово к использованию***

# rubrics
Расширение для Infrajs
## Подключение
````json
{
	"external":"-rubrics/rubrics.layer.json"
}
```
В текущем адресном пространстве будут обрабатываться все имена и согласно ```config.rubrics.main``` при наличии в папке ```~pages``` 
одноимённого файла(```docx```,```html```,```tpl```) покажется статья.
По умолчанию расширение включает в себя обработку разделов ```pages```, ```blog```, ```events```, ```files```.
Разделы с параметрами перечислены в конфиге ```-rubrics/.infra.json```. 

## Лента новостей на главной странице
Данные для ленты на главной беруться из файла ```index.php``` которые обрабатывает параметры 
- ```type``` - имя раздела из конфига
- ```list``` - требуется список
- ```chunk``` - количество ```array_chunk```
- ```show``` - требуется полный текст
- ```lim``` - ограничение по количеству записей в списке start,lenght

```json
{
	"json":"-rubrics/?type=events&list&lim=0,10"
}
```

Формат данных на основе разбора файла с текcтом

```
{
    "list": [
        {
            "id": 30,
            "name": "1-evro-60-rubley",
            "fname": "151103 1-evro-60-rubley",
            "file": "151103 1-evro-60-rubley.docx",
            "date": "151103",
            "ext": "docx",
            "modified": 1446563947,
            "heading": "1 евро = 60 рублей",
            "title": "1-evro-60-rubley",
            "images": [
                {
                    "src": "cache/docx/51b77a7d46b00f7438b701d284a31059/word/media/image1.jpeg"
                }
            ],
            "preview": "<p>«Слоган «1 евро = 60 рублей» - это не просто рекламная уловка, – говорит директор по продажам компании Кемппи. - Конечно, мы не имеем права менять валюту, тем более по такому курсу. Однако, стоимость нашего оборудования, которое изготавливается в Финляндии и поставляется исключительно оттуда, формируется, исходя именно из такого соотношения рубля к евро. </p>",
            "size": 0.06,
            "links": [
                {
                    "href": "/contacts",
                    "title": "Контакты"
                }
            ]
        },
    ...
```

## Имя файла
Имя файла интерпретируется согласно правилам [infrajs/load](https://github.com/infrajs/load)
```yymmdd name@id.ext```

## API

```php
use infrajs\rubrics\Rubrics;
$res = Rubrics::search('events', id); //id - порядковый номер файла или номер указанный в имени файла после @ или имя файла без учёта даты и номера файла
//Поддерживаются расширения 'docx', 'mht', 'tpl', 'html', 'txt', 'php'
$html = Rubrics::article('~events/'.$res['file']); //Содержимое файла в html
```