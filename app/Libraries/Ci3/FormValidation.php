<?php

namespace App\Libraries\Ci3;

/**
 * CodeIgniter 3's form_validation library.
 *
 * Implemented directly rather than adapted onto CI4's Validation service,
 * because two CI3 behaviours the application depends on do not map cleanly:
 *
 *  - 'trim' is a *prep* rule. CI3 wrote the trimmed value back into $_POST, so
 *    everything downstream (including $this->input->post()) saw the cleaned
 *    value. CI4 treats trim as a filter on a separate data set.
 *  - 'callback_foo' calls foo() on the controller and uses its return value,
 *    with any message the controller set via set_message().
 *
 * The rule set in use here is small -- trim, required, is_natural_no_zero,
 * matches, plus four callbacks -- so this stays short.
 */
class FormValidation
{
    /** @var list<array{field:string,label:string,rules:string}> */
    private array $rules = [];

    /** @var array<string, string> */
    private array $errors = [];

    /** @var array<string, string> */
    private array $messages = [];

    private string $prefix = '<p class="text-danger">';
    private string $suffix = '</p>';

    /** @var array<string, string> */
    private array $defaultMessages = [
        'required'           => 'The {field} field is required.',
        'is_natural_no_zero' => 'The {field} field must only contain digits and must be greater than zero.',
        'matches'            => 'The {field} field does not match the {param} field.',
        'valid_email'        => 'The {field} field must contain a valid email address.',
        'integer'            => 'The {field} field must contain an integer.',
        'numeric'            => 'The {field} field must contain only numbers.',
        'min_length'         => 'The {field} field must be at least {param} characters in length.',
        'max_length'         => 'The {field} field cannot exceed {param} characters in length.',
    ];

    public function __construct(private object $controller)
    {
    }

    /**
     * @param string|array $field
     */
    public function set_rules($field, string $label = '', $rules = '', array $errors = []): static
    {
        if (is_array($field)) {
            foreach ($field as $row) {
                if (isset($row['field'])) {
                    $this->set_rules($row['field'], $row['label'] ?? $row['field'], $row['rules'] ?? '');
                }
            }

            return $this;
        }

        if (is_array($rules)) {
            $rules = implode('|', $rules);
        }

        $this->rules[] = [
            'field' => $field,
            'label' => $label !== '' ? $label : $field,
            'rules' => (string) $rules,
        ];

        foreach ($errors as $rule => $message) {
            $this->messages[$field . '.' . $rule] = $message;
        }

        return $this;
    }

    public function set_message(string $rule, string $message = ''): static
    {
        $this->messages[$rule] = $message;

        return $this;
    }

    public function set_error_delimiters(string $prefix = '<p>', string $suffix = '</p>'): static
    {
        $this->prefix = $prefix;
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * CI3 returned FALSE when there was nothing to validate against, which is
     * what kept "if (run() == TRUE)" from firing on the initial GET.
     */
    public function run($module = '', $group = ''): bool
    {
        if ($_POST === [] || $this->rules === []) {
            $this->publish();

            return false;
        }

        $this->errors = [];

        foreach ($this->rules as $rule) {
            $this->applyRules($rule['field'], $rule['label'], $rule['rules']);
        }

        $this->publish();

        return $this->errors === [];
    }

    private function applyRules(string $field, string $label, string $ruleString): void
    {
        $value = $this->fieldValue($field);

        foreach (array_filter(explode('|', $ruleString)) as $rule) {
            $param = '';
            if (preg_match('/^(.+?)\[(.*)\]$/', $rule, $m)) {
                $rule  = $m[1];
                $param = $m[2];
            }

            // Prep rules rewrite the submitted value in place, as CI3 did.
            if ($rule === 'trim') {
                $value = is_string($value) ? trim($value) : $value;
                $this->setFieldValue($field, $value);

                continue;
            }

            if ($rule === 'htmlspecialchars') {
                $value = is_string($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
                $this->setFieldValue($field, $value);

                continue;
            }

            // Only 'required' cares about an empty value; every other rule is
            // skipped when the field is blank, matching CI3.
            if ($rule !== 'required' && ($value === null || $value === '')) {
                continue;
            }

            if (str_starts_with($rule, 'callback_')) {
                $method = substr($rule, 9);

                if (! method_exists($this->controller, $method)) {
                    $this->addError($field, $label, $rule, $param, sprintf('Unable to access the callback %s().', $method));

                    continue;
                }

                if ($this->controller->{$method}($value, $param) === false) {
                    $this->addError($field, $label, $method, $param);
                }

                continue;
            }

            if (! $this->check($rule, $value, $param)) {
                $this->addError($field, $label, $rule, $param);
            }
        }
    }

    private function check(string $rule, $value, string $param): bool
    {
        return match ($rule) {
            'required'           => ! ($value === null || $value === '' || (is_array($value) && $value === [])),
            'is_natural'         => ctype_digit((string) $value),
            'is_natural_no_zero' => ctype_digit((string) $value) && (int) $value > 0,
            'integer'            => (bool) preg_match('/^[\-+]?\d+$/', (string) $value),
            'numeric'            => is_numeric($value),
            'decimal'            => (bool) preg_match('/^[\-+]?\d*\.?\d+$/', (string) $value),
            'valid_email'        => (bool) filter_var($value, FILTER_VALIDATE_EMAIL),
            'valid_url'          => (bool) filter_var($value, FILTER_VALIDATE_URL),
            'alpha'              => ctype_alpha((string) $value),
            'alpha_numeric'      => ctype_alnum((string) $value),
            'min_length'         => mb_strlen((string) $value) >= (int) $param,
            'max_length'         => mb_strlen((string) $value) <= (int) $param,
            'exact_length'       => mb_strlen((string) $value) === (int) $param,
            'matches'            => $value === $this->fieldValue($param),
            'differs'            => $value !== $this->fieldValue($param),
            'greater_than'       => is_numeric($value) && $value > $param,
            'less_than'          => is_numeric($value) && $value < $param,
            'in_list'            => in_array((string) $value, explode(',', $param), true),
            default              => true,
        };
    }

    private function addError(string $field, string $label, string $rule, string $param, ?string $override = null): void
    {
        if (isset($this->errors[$field])) {
            return; // CI3 reported the first failure per field.
        }

        $message = $override
            ?? $this->messages[$field . '.' . $rule]
            ?? $this->messages[$rule]
            ?? $this->defaultMessages[$rule]
            ?? 'The {field} field is invalid.';

        $this->errors[$field] = str_replace(['{field}', '{param}'], [$label, $param], $message);
    }

    private function fieldValue(string $field)
    {
        if (str_contains($field, '[')) {
            preg_match_all('/([^\[\]]+)/', $field, $m);
            $ref = $_POST;
            foreach ($m[1] as $key) {
                if (! is_array($ref) || ! array_key_exists($key, $ref)) {
                    return null;
                }
                $ref = $ref[$key];
            }

            return $ref;
        }

        return $_POST[$field] ?? null;
    }

    private function setFieldValue(string $field, $value): void
    {
        if (! str_contains($field, '[')) {
            $_POST[$field] = $value;

            return;
        }

        preg_match_all('/([^\[\]]+)/', $field, $m);
        $ref = &$_POST;
        foreach ($m[1] as $key) {
            if (! isset($ref[$key]) || ! is_array($ref[$key])) {
                if (! array_key_exists($key, (array) $ref)) {
                    return;
                }
            }
            $ref = &$ref[$key];
        }
        $ref = $value;
    }

    /**
     * Publish errors where form_error() and validation_errors() can see them.
     */
    private function publish(): void
    {
        Registry::setValidationErrors($this->errors);
    }

    public function error(string $field, string $prefix = '', string $suffix = ''): string
    {
        if (! isset($this->errors[$field])) {
            return '';
        }

        return ($prefix !== '' ? $prefix : $this->prefix)
            . $this->errors[$field]
            . ($suffix !== '' ? $suffix : $this->suffix);
    }

    /**
     * @return array<string, string>
     */
    public function error_array(): array
    {
        return $this->errors;
    }

    public function error_string(string $prefix = '', string $suffix = ''): string
    {
        $out = '';
        foreach ($this->errors as $message) {
            $out .= ($prefix !== '' ? $prefix : $this->prefix) . $message . ($suffix !== '' ? $suffix : $this->suffix) . "\n";
        }

        return $out;
    }

    public function reset_validation(): static
    {
        $this->rules  = [];
        $this->errors = [];
        $this->publish();

        return $this;
    }
}
