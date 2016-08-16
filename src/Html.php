<?php

namespace HtmlSerializer;

use DOMElement;
use DOMDocument;
use InvalidArgumentException;

class Html {

    /**
     * @var DOMElement
     */
    protected $_root;

    /**
     * @var []
     */
    protected $_array = null;

    protected $_removeEmptyStrings = true;
    protected $_parseCss = true;

    public function __construct($html) {

        if (false === mb_strpos(mb_strtolower(mb_substr($html, 0, 80)), '<!doctype') ) {
            $html = "<!DOCTYPE html><html><head><meta charset='UTF-8' /></head><body>$html</body></html>";
        }

        $dom = new DOMDocument('1.0', 'UTF-8');

        @libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        @libxml_clear_errors();


        $body = $dom->getElementsByTagName('body');
        if (0 == $body->length) {
            throw new InvalidArgumentException('Invalid HTML.');
        }

        $this->_root = $body[0];
    }


    /**
     * Get serialized html as associative array
     * @return array
     */
    public function toArray() {

        if (null === $this->_array) {

            $array = $this->_domElementToArray($this->_root);

            if(empty($array) || ! isset($array['children'])) {
                $this->_array = [];
            }
            else {
                $this->_array = $array['children'];
            }
        }

        return $this->_array;
    }

    /**
     * Get serialized html as json
     * @return string
     */
    public function toJson() {
        return json_encode($this->toArray());
    }

    /**
     * Set parse inline css to object
     * @param bool $value
     */
    public function parseCss($value = true) {
        $this->_parseCss = boolval($value);
    }

    /**
     * Set remove nodes with empty whitespaces
     * @param bool $value
     */
    public function removeEmptyStrings($value = true) {
        $this->_removeEmptyStrings = boolval($value);
    }

    protected function _parseInlineCss($css) {

        $urls = [];

        // fix issue with ";" symbol in url()
        $css = preg_replace_callback('/url(\s+)?\(.*\)/i', function ($match) use (&$urls) {
            $index = count($urls) + 1;
            $index = "%%$index%%";
            $urls[$index] = $match[0];
            return $index;
        }, $css);


        $arr = explode(';', $css);
        $result = [];

        foreach ($arr as $item) {

            list ($attribute, $value) = array_map('trim', explode(':', $item));

            // restore original url()
            if (preg_match('/%%\d+%%/', $value)) {
                $value = preg_replace_callback('/%%\d+%%/', function ($match) use ($urls) {

                    if (isset($urls[$match[0]])) {
                        return $urls[$match[0]];
                    }
                    else {
                        return $match[0];
                    }
                }, $value);
            }

            $result[$attribute] = $value;
        }

        return $result;
    }


    protected function _domElementToArray(DOMElement $element) {
        $node = mb_strtolower($element->tagName);

        $attributes = [];
        foreach ($element->attributes as $attribute) {
            $attr = mb_strtolower($attribute->name);
            $value = $attribute->value;

            if ('style' == $attr && $this->_parseCss) {
                $value = $this->_parseInlineCss($value);
            }

            $attributes[$attr] = $value;
        }

        $children = [];
        if ($element->hasChildNodes()) {
            foreach ($element->childNodes as $childNode) {

                if (XML_ELEMENT_NODE === $childNode->nodeType) {

                    $children[] = $this->_domElementToArray($childNode);
                }
                elseif (XML_TEXT_NODE === $childNode->nodeType ) {

                    $text = $childNode->nodeValue;

                    if (!$this->_removeEmptyStrings || "" != trim($text)) {
                        $children[] = [
                            'node' => 'text',
                            'text' => $text
                        ];
                    }
                }
            }
        }

        $result = [
            'node' => $node,
        ];

        if (count($attributes) > 0) {
            $result['attributes'] = $attributes;
        }

        if (count($children) > 0) {
            $result['children'] = $children;
        }

        return $result;
    }

}