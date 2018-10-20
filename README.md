# Test for RedSoft

## Demo

Демо находится <http://redsoft.zipofar.ru> 

## Описание задания

Каталог содержащий два вида объектов
* Раздел
* Товар

### Раздел
Древовидная структура с неограниченной вложенностью

### Товар
Представляет собой объект со следующими полями
* ID
* Название
* Наличие
* Цена
* Производитель

Товар может быть привязан к нескольким разделам
 
### Функции каталога
Взаимодействие с пользователем происходит посредством HTTP запросов к API серверу. Все ответы представляют собой JSON объекты.

Сервер реализует следующие методы:
* Выдача товара по ID
* Выдача товаров по вхождению подстроки в названии
* Выдача товаров по производителю/производителям
* Выдача товаров по разделу (только раздел)
* Выдача товаров по разделу и вложенным разделам
  
###  Задание
* Спроектировать БД и написать скрипт по ее созданию
* Подготовить данные для заполнения БД
* Реализовать API с вышеуказанными методами
* Написать документацию по использованию этого API

Формат запросов и ответов определяется исполнителем.

## API

URL не должен заканчиваться символом слеш.
GET запросы возвращают ответы JSON и имеют структуру
```
{"meta":{"number_of_records":1},"payload":{"id":"1","name":"Updated Section"}}
```

В случае ошибки
```
{"meta":[],"payload":{},"errors":[]}
```

* Товар по ID
```
/api/products/{id}
```

* Фильтр товаров по полям (name, brand, price, availability)
```
/api/products?name=Name&brand=Brand&price=1.50&availability=1
/api/products?page=1&per_page=3
```

* Постраничный вывод товаров. По дефолту выводится страница 1, по 5 записей 
```
/api/products?page=1&per_page=3
```

* Фильтр по полю name может использовать часть имени
```
/api/products?name=beginWith%
/api/products?name=%endOn
/api/products?name=%inMiddle%
```

* Фильтр по полю brand (производитель) можно указать несколько производителей
```
/api/products?brand=Brand1|Brand2|Brand3
```

* Создать товар POST запрос
```
/api/products
```
с телом JSON и section_id для определения категории в которой сохранить товар
```
{"name":"New Product","availability":"1","price":"5.50","brand":"SomeBrand","section_id":"15"}
```
вернет ответ
```
Response code 201 and header Location:/api/products/{id}
```

* Обновить товар PUT запрос
```
/api/products/{id}
```
с телом JSON
```
{"name":"New Product","availability":"1","price":"5.50","brand":"SomeBrand"}
```
вернет ответ
```
Response code 201 and header Location:/api/products/{id}
```

* Удалить товар DELETE запрос
```
/api/products/{id}
```
вернет ответ
```
Response code 204
```


* Категория по ID
```
/api/sections/{id}
```

* Получить все категории в виде JSON Tree
```
/api/sections
```

* Получить все категории в виде HTML List Tree
```
/api/sections?pretty
```

* Получить все товары в категории по ID категории. К продуктам можно применить фильтр
```
/api/sections/{id}/products
/api/sections/{id}/products?brand=Brand&name=SomeName
```

* Получить все товары в категории и всех подкатегориях с помощью ID категории.
```
/api/sections/{id}/sub/products?brand=Brand&price=20.0&name=SomeName
```

* Создать категорию POST запрос
```
/api/sections
```
с телом JSON и parent_id для определения родительской категории 
```
{"name":"New Section","parent_id":"10"}
```
вернет ответ
```
Response code 201 and header Location:/api/sections/{id}
```

* Обновить товар PUT запрос
```
/api/sections/{id}
```
с телом JSON
```
{"name":"New Section"}
```
вернет ответ
```
Response code 201 and header Location:/api/sections/{id}
```

* Удалить товар DELETE запрос
```
/api/sections/{id}
```
вернет ответ
```
Response code 204
```
## Requirements

* Mac / Linux
* Docker
* Docker Compose

## Install

Clone repo

```
$ git clone https://github.com/zipofar/redsoft-test.git
```
[Install ansible](http://docs.ansible.com/ansible/latest/intro_installation.html)

```
$ cd redsoft-test
$ make ansible-development-setup
$ make run-dev
$ make development-setup
```

По умолчанию логи находятся в app/storage/logs. Для того, чтобы приложение могло создать в этой директории файл логов, необходимо изменить разрешения.
```
sudo chmod 777 app/storage/logs
```

Open <http://localhost:4000>

Так же в dev окружении настроен Xdebug на порту 9001
## Run

```
$ make run-dev
```

Open <http://localhost:4000>

## Test
Для запуска тестов, необходимо запустить проект
```
$ make run-dev
```
Запустить все тесты
```
$ make test-all
```
