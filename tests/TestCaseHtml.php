<?php

namespace HtmlSerializer;

use PHPUnit\Framework\TestCase;


class TestCaseHtml extends TestCase {

    /**
     * @dataProvider provider
     */
    public function testToArray($data) {

        $html = new Html($data);
        $array = $html->toArray();
        $this->assertTrue(is_array($array));
        $this->assertNotEmpty($array);
    }

    /**
     * @dataProvider provider
     */
    public function testToJson($data) {

        $html = new Html($data);
        $json = $html->toJson();
        $array = json_decode($json, true);
        $this->assertTrue(is_array($array));
    }

    /**
     * @dataProvider withCssProvider
     */
    public function testCssAsString($data) {

        $html = new Html($data);
        $html->parseCss(false);
        $array = $html->toArray();

        $expected = "border: 1px; background: #fff url(img.png?foo;bar)";
        $this->assertEquals($expected, $array[0]['attributes']['style']);
    }

    /**
     * @dataProvider withCssProvider
     */
    public function testCssAsArray($data) {

        $html = new Html($data);
        $array = $html->toArray();

        $expected = [
            'border' => '1px',
            'background' => '#fff url(img.png?foo;bar)',
        ];

        $this->assertEquals($expected, $array[0]['attributes']['style']);
    }

    /**
     * @dataProvider emptyProvider
     */
    public function testEmpty($data) {

        $html = new Html($data);
        $array = $html->toArray();

        $expected = [];

        $this->assertEquals($expected, $array);
    }

    public function provider() {
        return [
            'simple' => ['<div><p>foo</p><p>bar</p></div>'],
            'with image' => ['<p ><strong>Hello World!</strong></p> <p> <img src="img.png" alt="" aria-readonly class="image"> </p>']
        ];
    }

    public function withCssProvider() {
        return [
            'with css' => ['<div style="border: 1px; background: #fff url(img.png?foo;bar)"></div>']
        ];
    }
    public function emptyProvider() {
        return [
            'empty' => ['']
        ];
    }


}