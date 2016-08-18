# HTML Serializer
Convert HTML to Array or JSON

## Installation
    $ composer require shtrihstr/html-serializer

## Usage
### HTML to JSON
```php
$html = new HtmlSerializer\Html('<div class="content"><p>Hello World</p><img src="img.png" alt="" class="image" /></div>');
$json = $html->toJson();
```
#### Result
```json
[
    {
        "node": "div",
        "attributes": {
            "class": "content"
        },
        "children": [
            {
                "node": "p",
                "children": [
                    {
                        "node": "text",
                        "text": "Hello World"
                    }
                ]
            },
            {
                "node": "img",
                "attributes": {
                    "src": "img.png",
                    "alt": "",
                    "class": "image"
                }
            }
        ]
    }
]
```
### HTML to Array
```php
$array = $html->toArray();
```
#### Result
```php
[
    [
        'node' => 'div',
        'attributes' => [
            'class' => 'content',
        ],
        'children' => [
            [
                'node' => 'p',
                'children' => [
                    [
                        'node' => 'text',
                        'text' => 'Hello World',
                    ],
                ]
            ],
            [
                'node' => 'img',
                'attributes' => [
                    'src' => 'img.png',
                    'alt' => '',
                    'class' => 'image',
                ]
            ],
        ],
    ],
]
```
### Inline CSS
```php
$html = new HtmlSerializer\Html('<div style="color: red; background: url(img.png?foo;bar)">Hello World</div>');
$html->parseCss(); // enabled by default
$json = $html->toJson();
```
#### Result
```json
[
    {
        "node": "div",
        "attributes": {
            "style": {
                "color": "red",
                "background": "url(img.png?foo;bar)"
            }
        },
        "children": [
            {
                "node": "text",
                "text": "Hello World"
            }
        ]
    }
]
```
```php
$html->parseCss(false);
```
#### Result
```json
[
    {
        "node": "div",
        "attributes": {
            "style": "color: red; background: url(img.png?foo;bar)"
        },
        "children": [
            {
                "node": "text",
                "text": "Hello World"
            }
        ]
    }
]
```

### Remove empty strings
```php
$html = new HtmlSerializer\Html('<div> <p> foo</p> <span> bar </span>  </div>');
$html->removeEmptyStrings(); // enabled by default
$json = $html->toJson();
```
#### Result
```json
[
    {
        "node": "div",
        "children": [
            {
                "node": "p",
                "children": [
                    {
                        "node": "text",
                        "text": " foo"
                    }
                ]
            },
            {
                "node": "span",
                "children": [
                    {
                        "node": "text",
                        "text": " bar "
                    }
                ]
            }
        ]
    }
]
```
```php
$html->removeEmptyStrings(false)
```
#### Result
```json
[
    {
        "node": "div",
        "children": [
            {
                "node": "text",
                "text": " "
            },
            {
                "node": "p",
                "children": [
                    {
                        "node": "text",
                        "text": " foo"
                    }
                ]
            },
            {
                "node": "text",
                "text": " "
            },
            {
                "node":"span",
                "children": [
                    {
                        "node": "text",
                        "text": " bar "
                    }
                ]
            },
            {
                "node": "text",
                "text": "  "
            }
        ]
    }
]
```