# AbstractForm

## `\WebServCo\Framework\AbstractForm`

---

## Examples

### Implementation

`filter` and `validate` are called in the parent constructor, so make sure to do all initialization before calling it.

```php
class MyForm extends \WebServCo\Framework\AbstractForm
{
    public function __construct($defaultData = [])
    {
        // custom code, before calling parent constructor

        parent::__construct(
            [
                'meta' => [
                    'name' => __('Name'),
                    'email' => __('Email address'),
                ],
                'required' => [
                    'name',
                    'email',
                ],
                'trim' => [
                    'name',
                    'email',
                ],
                'minimumLength' => [
                    'name' => 2,
                    'email' => 5,
                ],
            ], // settings
            $defaultData,
            ['submitFieldName1', 'submitFieldName2'] // submitFields
        );
    }

    protected function filter()
    {
        foreach ($this->setting('trim', []) as $item) {
            $this->setData($item, trim($this->data($item)));
        }
        foreach ($this->setting('filterNumeric', []) as $item) {
            $this->setData($item, preg_replace('/[^0-9]/', '', $this->data($item)));
        }
        foreach ($this->setting('numberFix', []) as $item) {
            $this->setData($item, floatval(str_replace(',', '.', $this->data($item))) ?: '');
        }
        return true;
    }

    protected function validate()
    {
        foreach ($this->setting('required', []) as $item) {
            if (empty($this->data($item))) {
                $this->errors[$item][] = sprintf(__('This field is mandatory: %s'), $this->setting('meta/'.$item));
            }
        }
        foreach ($this->setting('minimumLength', []) as $item => $minimumLength) {
            if (mb_strlen($this->data($item)) < $minimumLength) {
                $this->errors[$item][] = sprintf(
                    __('This field is too short: %s'),
                    $this->setting('meta/'.$item)
                );
            }
        }
        if (!empty($this->errors)) {
            return false;
        }

        return true;
    }
}
```

## Usage

```php
$defaultData = [
    'name' => 'test',
    'email' => 'test@test.test',
];
$form = new MyForm($defaultData);
if ($form->isSent() && $form->isValid()) {
    // ok, go for processing

    // get the name of the submit field
    $submitField = $form->getSubmitField();
    // use a form field named "email"
    $email = $form->data('email');
}

// get the errors for a field named email
echo $this->form->errors('email');
```

---
