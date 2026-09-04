<?php

namespace App\Libraries\Ci3\Database;

use CodeIgniter\Database\ResultInterface;

/**
 * CodeIgniter 3's query result object.
 *
 * CI3's row()/result() default to objects and row_array()/result_array() to
 * arrays; CI4 renames these to getRow()/getResultArray() and friends. The CI3
 * names and their return contracts are kept, including row() returning NULL
 * past the end of the set rather than raising.
 */
class Result
{
    public function __construct(private ResultInterface $result)
    {
    }

    public function ci4(): ResultInterface
    {
        return $this->result;
    }

    public function num_rows(): int
    {
        return $this->result->getNumRows();
    }

    public function num_fields(): int
    {
        return $this->result->getFieldCount();
    }

    /**
     * @return list<object>
     */
    public function result(string $type = 'object'): array
    {
        return $this->result->getResult($type);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function result_array(): array
    {
        return $this->result->getResultArray();
    }

    /**
     * @return list<object>
     */
    public function result_object(): array
    {
        return $this->result->getResultObject();
    }

    /**
     * @param int|string $n Row index, or a class name when called CI3-style.
     */
    public function row($n = 0, string $type = 'object')
    {
        if (is_string($n)) {
            // CI3 allowed row('ClassName') to hydrate a custom object.
            return $this->result->getRow(0, $n);
        }

        return $this->result->getRow($n, $type);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function row_array(int $n = 0): ?array
    {
        return $this->result->getRowArray($n);
    }

    public function row_object(int $n = 0)
    {
        return $this->result->getRow($n, 'object');
    }

    public function first_row(string $type = 'object')
    {
        return $this->result->getFirstRow($type);
    }

    public function last_row(string $type = 'object')
    {
        return $this->result->getLastRow($type);
    }

    public function next_row(string $type = 'object')
    {
        return $this->result->getNextRow(0, $type);
    }

    public function previous_row(string $type = 'object')
    {
        return $this->result->getPreviousRow(0, $type);
    }

    public function unbuffered_row(string $type = 'object')
    {
        return $this->result->getUnbufferedRow($type);
    }

    /**
     * @return list<string>
     */
    public function list_fields(): array
    {
        return $this->result->getFieldNames();
    }

    public function field_data(): array
    {
        return $this->result->getFieldData();
    }

    public function free_result(): void
    {
        $this->result->freeResult();
    }

    public function __call(string $name, array $arguments)
    {
        return $this->result->{$name}(...$arguments);
    }
}
