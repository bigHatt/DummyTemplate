<?php
namespace App\Tests;

use App\Exceptions\InvalidTemplateException;
use App\Exceptions\ResultTemplateMismatchException;
use App\Template\ReverseTemplate;
use PHPUnit\Framework\TestCase;

class ReverseTemplateTest extends TestCase
{
    public function testSimple()
    {
        $template = 'Hello, my name is {{name}}.';
        $string = 'Hello, my name is Juni.';
        $rt = new ReverseTemplate($template, $string);
        $values = $rt->getProperties();
        $this->assertSame(['name' => "Juni"], $values);
    }

    public function testBrackets()
    {
        $this->expectException(InvalidTemplateException::class);
        $template = 'Hello, my name is {{name}.';
        $string = 'Hello, my name is Juni.';
        $rt = new ReverseTemplate($template, $string);
    }

    public function testTemplate()
    {
        $this->expectException(ResultTemplateMismatchException::class);
        $template = 'Hello, my name is {{name}}.';
        $string = 'Hello, my lastname is Juni.';
        $rt = new ReverseTemplate($template, $string);
    }

    public function testCheckNull()
    {
        $template = 'Hello, my name is {{name}}.';
        $string = 'Hello, my name is .';
        $rt = new ReverseTemplate($template, $string);
        $values = $rt->getProperties();
        $this->assertSame(['name' => ""], $values);
    }

    public function testNotEscape()
    {
        $template = 'Hello, my name is {name}.';
        $string = 'Hello, my name is <robot>.';
        $rt = new ReverseTemplate($template, $string);
        $values = $rt->getProperties();
        $this->assertSame(['name' => "<robot>"], $values);
    }

    public function testEscape()
    {
        $template = 'Hello, my name is {{name}}.';
        $string = 'Hello, my name is &lt;robot&gt;.';
        $rt = new ReverseTemplate($template, $string);
        $values = $rt->getProperties();
        $this->assertSame(['name' => "<robot>"], $values);
    }

    public function testNewBracket()
    {
        $template = 'Hello, my name is [{name}].';
        $string = 'Hello, my name is &lt;robot&gt;.';
        $newBracket = [
            'escape' => ['[{', '}]']
        ];
        $rt = new ReverseTemplate($template, $string, $newBracket);
        $values = $rt->getProperties();
        $this->assertSame(['name' => "<robot>"], $values);
    }
}