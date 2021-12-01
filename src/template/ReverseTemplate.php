<?php

namespace App\Template;

use Exception;

class ReverseTemplate
{

    private string $_template;
    private string $_result;
    private array $_brackets;
    private array $_defaultBrackets = [
        'non-escape' =>  ['{', '}'],
        'escape' => ['{{', '}}'],
    ];

    public function __construct(string $template, string $result, array $brackets = null)
    {
        $this->_template = $template;
        $this->_result = $result;  
        $this->_brackets = $brackets ?? $this->_defaultBrackets; 
        $this->sortBracketsByLength();  
        $validator = new ReverseTemplateValidator($this);
        $validated = $validator->validate();
        if ($validated) {
            $this->getProperties();
        }
    }

    public function getTemplate()
    {
        return $this->_template ?? null;
    }

    public function getResult()
    {
        return $this->_result ?? null;
    }

    public function getBrackets()
    {
        return $this->_brackets ?? null;
    }

    /**
     * Сортируем скобки по уменьшению длины
     * Во избежание случаев, когда сначала парсится {}, а потом {{}}
     *
     * @return void
     */
    private function sortBracketsByLength()
    {
        $sortByLength = function ($a, $b) {
            $strLenA = strlen($a[0]);
            $strLenB = strlen($b[0]);
            if ($strLenA == $strLenB) {
                return 0;
            }
            return ($strLenA < $strLenB) ? 1 : -1;
        };
        usort($this->_brackets, $sortByLength);
    }

    /**
     * Получаем массив исходных параметров.
     * Ключ - имя параметра, значение - значение параметра.
     *
     * @return array
     */
    public function getProperties()
    {
        $propTemplateValue = [];
        $copyTemplate = str_replace('.', '\.', $this->_template); //увы костыль, чтобы при подставлении в регулярное точка воспринималась как надо.
        foreach ($this->_brackets as $bracket) {
            $left = preg_quote($bracket[0]); // экранируем чтобы можно было использовать скобки [{}] и тд.
            $right = preg_quote($bracket[1]);
            $copyTemplate = preg_replace("/$left(.*?)$right/", '(.*)', $copyTemplate);
        }
        preg_match("/$copyTemplate/", $this->_template, $propTemplates);
        preg_match("/$copyTemplate/", $this->_result, $propValues);
        if (empty($propTemplates) || (count($propTemplates) != count($propValues))) {
            throw new Exception('Some properties doesnt found');
        }
        for ($i = 1; $i < count($propTemplates); $i++) {
            $propTemplateValue[$propTemplates[$i]] = $propValues[$i];
        }
        $result = [];
        foreach ($this->_brackets as $bracketType => $bracket) {
            $left = preg_quote($bracket[0]);
            $right = preg_quote($bracket[1]);
            foreach ($propTemplateValue as $temp => $value) {
                preg_match("/$left(.*?)$right/", $temp, $propName);
                if (!empty($propName[1])) {
                    $result[$propName[1]] = $bracketType == 'escape' ? htmlspecialchars_decode($value) : $value; // экранируется в зависимости от типа скобок
                    unset($propTemplateValue[$temp]);
                }
            }
        }
        return $result;
    }
}
