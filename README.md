# HTML Serializer
Convert HTML to Array or JSON

## Installation
    $ composer require shtrihstr/html-serializer

## Usage
#### To JSON
```php
$html = new HtmlSerializer\Html('<p class="color" style="color: red;">Hello World!</p>');
$json = $html->toJson();
```
#### Result
```json
[
  {
    "node": "p",
    "attributes": {
      "class": "color",
      "style": {
        "color": "red"
      }
    },
    "children":[
      {
        "node": "text",
        "text": "Hello World!"
      }
    ]
  }
]
```