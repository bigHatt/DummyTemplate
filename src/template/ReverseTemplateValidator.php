<?php

namespace App\Template;

use App\Exceptions\InvalidTemplateException;
use App\Exceptions\ResultTemplateMismatchException;

class ReverseTemplateValidator
{

    public $reverseTemplate;
    private $_template;
    private $_brackets;

    public function __construct(ReverseTemplate $template)
    {
        $this->reverseTemplate = $template;
        $this->_template = $this->reverseTemplate->getTemplate();
        $this->_brackets = $this->reverseTemplate->getBrackets();
    }

    public function validate()
    {
        $this->validateBrackets();
        $this->validateTemplate();
        return true;
    }

    /**
     * Метод проверяет правильность скобок в шаблоне.
     *
     * @return void|InvalidTemplateException
     */
    private function validateBrackets()
    {        
        $copyTemplate = $this->_template;
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->_brackets));
        $flatten_brackets = iterator_to_array($it, false); // массив скобок приобразованный в одномерный
        foreach ($this->_brackets as $bracket) {
            $left = preg_quote($bracket[0]);
            $right = preg_quote($bracket[1]);
            preg_match_all("/$left(.*?)$right/", $copyTemplate, $matches);
            foreach ($matches[1] as $match) {
                foreach ($flatten_brackets as $fb) {
                    if (strpos($match, $fb) !== false) { // если при замене скобок остались части: {name}} -> name} 
                        throw new InvalidTemplateException();
                        break;
                    }
                }
            }
            foreach ($matches[0] as $match) {
                $copyTemplate = str_replace($match, '', $copyTemplate);
            }
            foreach ($bracket as $b) {
                if (strpos($copyTemplate, $b) !== false) {
                    throw new InvalidTemplateException();
                    break;
                }
            }
        }
    }

    /**
     * Проверяем текст шаблона на корректность.
     * Постепенная замена скобок на любую последовательность для регулярки.
     *
     * @return void|ResultTemplateMismatchException
     */
    private function validateTemplate()
    {
        $template = $this->reverseTemplate->getTemplate();
        $template = str_replace('.', '\.', $template);
        foreach ($this->_brackets as $bracket) {
            $left = preg_quote($bracket[0]);
            $right = preg_quote($bracket[1]);
            $template = preg_replace("/$left(.*?)$right/", '(.*)', $template);
        }     
        preg_match("/$template/", $this->reverseTemplate->getResult(), $re);
        if ($re[0] === $this->reverseTemplate->getResult()) {
            return true;
        } else {
            throw new ResultTemplateMismatchException();
        }
    }
}