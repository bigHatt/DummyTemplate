# DummyTemplate

Имеется простенький шаблонизатор, младший брат smarty или mustache, назовем его Dummy. Все что он умеет это подставлять экранированные и неэкранированные (html-escaped) переменные в строку.

### Термины
- *шаблон* - строка содержащая 0 и более переменных для шаблонизации

`Hello, my name is {{name}}.`

`Ваш код для входа: {{pin}}`

- *переменная* - строковое значение подлежащее шаблонизации (подстановке в шаблон). имеет наименование и значение, и следующий синтаксис:

`{{name}}` - подставить экранированное значение
  
`{not_escaped_name}` - подставить неэкраниованное значение

- *результат* - строка, образовавшаяся в результате шаблонизации

Избыточные переменные игнорируются, пример работы шаблонизатора:

`Hello, my name is {{name}}.` + `['name' => "Juni", 'occupation' => "templator"]` = `Hello, my name is Juni.`

### Задача
Необходимо реализовать инструмент для "обратной шаблонизации", который на основе строки результата и шаблона восстанавливает переменные, необходимо реализовать валидацию и учесть граничные условия.

На вход подаются `шаблон` и `результат`, на выходе ожидается `массив переменных`

- *шаблон* `Hello, my name is {{name}}.` *результат* `Hello, my name is Juni.` -> `['name' => "Juni"]`
- *шаблон* `Hello, my name is {{name}.` *результат* `Hello, my name is Juni.` -> `throw new InvalidTemplateException('Invalid template.')`
- *шаблон* `Hello, my name is {{name}}.` *результат* `Hello, my lastname is Juni.` -> `throw new ResultTemplateMismatchException('Result not matches original template.')`
- *шаблон* `Hello, my name is {{name}}.` *результат* `Hello, my name is .` -> `['name' => ""]`
- *шаблон* `Hello, my name is {name}.` *результат* `Hello, my name is <robot>.` -> `['name' => "<robot>"]`
- *шаблон* `Hello, my name is {{name}}.` *результат* `Hello, my name is &lt;robot&gt;.` -> `['name' => "<robot>"]`

Реализованное решение необходимо выложить в публичный репозиторий на github и прислать ссылку.
Текст этого задания может быть дополнен ответами или пояснениями на ваши вопросы. В `README.md` должен содержаться пример использования.

#### Преимущество при выполнении работы дает:
- наличие тестов
- возможность параметризации шаблонизатора (напр. возможность поменять токен с `{{` на `[[`) либо другие "ручки" на ваше усмотрение
- комментарии по мере необходимости
