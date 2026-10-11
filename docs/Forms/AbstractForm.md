# AbstractForm

`\WebServCo\Framework\AbstractForm`

A form class reads the submitted values, or your defaults when nothing was submitted. It filters and validates them and keeps everything the view will ask for, such as labels, required flags, help texts and errors.

Forms are [libraries](../Libraries/AbstractLibrary.md) too, with the configuration in the settings and the field values in the data.

---

## Settings

| Key | Format | Use |
| --- | --- | --- |
| `meta` | field => label | **The list of fields.** Only these are read from the request. |
| `required` | list of fields | `required()`, and your validation |
| `help` | field => text | `help()` |
| `custom` | anything | your own settings, also in `toArray()` |

You can add any other key you like (`trim`, `minimumLength`, ...) and read it in `filter()` and `validate()`.

---

## Implementation

```php
use WebServCo\Framework\AbstractForm;

final class ContactForm extends AbstractForm
{
    /**
     * @param array<string,bool|int|float|string|null> $defaultData
     */
    public function __construct(array $defaultData = [])
    {
        // Initialization that filter() or validate() need goes before the parent constructor.

        parent::__construct(
            // settings
            [
                'meta' => [
                    'name' => __('Name'),
                    'email' => __('Email address'),
                    'message' => __('Message'),
                ],
                'required' => ['name', 'email', 'message'],
                'help' => [
                    'email' => __('We will never share your email.'),
                ],
                'trim' => ['name', 'email', 'message'],
                'minimumLength' => [
                    'name' => 2,
                    'message' => 10,
                ],
            ],
            // defaultData: used when the form isn't sent
            $defaultData,
            // submitFields: names of the submit buttons (optional)
            ['send'],
        );
    }

    public function validate(): bool
    {
        foreach ($this->setting('required', []) as $item) {
            if (empty($this->data($item))) {
                $this->errors[$item][] = sprintf(__('This field is mandatory: %s'), $this->meta($item));
            }
        }
        foreach ($this->setting('minimumLength', []) as $item => $minimumLength) {
            if (mb_strlen((string) $this->data($item)) < $minimumLength) {
                $this->errors[$item][] = sprintf(__('This field is too short: %s'), $this->meta($item));
            }
        }
        if (!filter_var($this->data('email'), FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'][] = __('Invalid email address.');
        }

        return $this->errors === [];
    }

    protected function filter(): bool
    {
        foreach ($this->setting('trim', []) as $item) {
            $this->setData($item, trim((string) $this->data($item)));
        }

        return true;
    }
}
```

A lot happens inside `parent::__construct()`:

1. Each `meta` field is read, from the POST data if the form was sent and from `$defaultData` if not.
2. `filter()` runs.
3. `validate()` runs, but only for a sent form.

Your own constructor code after the parent call therefore runs after filtering and validation, which is too late for anything they depend on.

---

## Usage

```php
$form = new ContactForm(['name' => $userName]);

if ($form->isSent() && $form->isValid()) {
    // Name of the submit button used: "send".
    $submitField = $form->getSubmitField();
    $email = $form->data('email');

    // ... process, then redirect (Post/Redirect/Get).
    return $this->getRedirectResponse('contact/thanks');
}

$this->setData('form', $form->toArray());

return $this->outputHtml($this->getData(), 'contact/form');
```

How `isSent()` decides depends on the submit fields. If you listed some, one of them has to be in the POST data. With none listed, any POST request counts.

That's also how you keep two forms on one page apart. Give each one its own submit button name.

### Methods

```php
$form->data('email');            // field value
$form->getData();                // all values
$form->meta('email');            // label
$form->help('email');            // help text
$form->required('email');        // bool
$form->errors('email');          // list of errors for the field, or null
$form->getAllErrors();           // field => list of errors
$form->isSent();
$form->isValid();
$form->getSubmitField();         // submit field name, or false
$form->clear();                  // empty the values and errors (after processing, for example)
$form->toArray();                // for the view, see below
```

The array from `toArray()` has six keys, `custom`, `data`, `errors`, `help`, `meta` and `required` (that last one maps each field to `true`).

---

## Frontend

Passing the form object itself to the view (`$this->setData('form', $form)`) lets the template call its methods:

```php
<?php $form = $this->data('form'); ?>
<form method="post" action="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="email"><?=$form->meta('email')?></label>
        <input type="email" class="form-control<?=$form->errors('email') ? ' is-invalid' : ''?>"
            id="email" name="email" aria-describedby="emailHelp"
            value="<?=htmlspecialchars((string) $form->data('email'))?>"<?=$form->required('email') ? ' required' : ''?>>
        <?php if ($form->errors('email')) { ?>
            <div class="invalid-feedback"><?=implode('<br>', $form->errors('email'))?></div>
        <?php } elseif ($form->help('email')) { ?>
            <small id="emailHelp" class="form-text text-muted"><?=$form->help('email')?></small>
        <?php } ?>
    </div>
    <button type="submit" name="send">Send</button>
</form>
```

If you passed `toArray()` instead, the same values are paths, like `$this->data('form/meta/email')` or `$this->data('form/errors/email')`.

---

## Notes

Values are read from `$this->request()->data()`, which rules out GET forms. File inputs are skipped too, and [File upload](../Files/Upload.md) takes care of those.

A `/` in a field name turns it into a nested [ArrayStorage](../ArrayStorage.md) key. Stick to plain names.

Before the form is sent, `isValid()` returns `false`.
