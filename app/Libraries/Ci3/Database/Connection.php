<?php

namespace App\Libraries\Ci3\Database;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseConnection;

/**
 * CodeIgniter 3's $this->db, over a CI4 connection.
 *
 * Two structural differences are bridged here:
 *
 *  1. In CI3 the query-builder methods hang off the connection itself, and the
 *     table is only named at the end (`->where(...)->get('news')`). In CI4 the
 *     builder is obtained from `->table()` first. So builder calls are buffered
 *     and replayed onto a CI4 builder once the table is known.
 *  2. CI3 reset the builder state after every terminal call. That is preserved,
 *     because the models rely on it -- they build a fresh query per method
 *     without clearing anything.
 *
 * Method names keep CI3's snake_case; the CI4 equivalents are camelCase.
 */
class Connection
{
    /** @var list<array{0:string,1:array}> Buffered builder calls. */
    private array $pending = [];

    private ?string $table = null;

    public function __construct(private BaseConnection $db)
    {
    }

    /**
     * The underlying CI4 connection, for code that wants the modern API.
     */
    public function ci4(): BaseConnection
    {
        return $this->db;
    }

    // ---------------------------------------------------------------------
    // Buffered builder calls
    // ---------------------------------------------------------------------

    private function buffer(string $method, array $args): static
    {
        $this->pending[] = [$method, $args];

        return $this;
    }

    public function select($select = '*', ?bool $escape = null): static
    {
        return $this->buffer('select', [$select, $escape]);
    }

    public function select_max(string $select = '', string $alias = ''): static
    {
        return $this->buffer('selectMax', [$select, $alias]);
    }

    public function select_min(string $select = '', string $alias = ''): static
    {
        return $this->buffer('selectMin', [$select, $alias]);
    }

    public function select_sum(string $select = '', string $alias = ''): static
    {
        return $this->buffer('selectSum', [$select, $alias]);
    }

    public function select_count(string $select = '', string $alias = ''): static
    {
        return $this->buffer('selectCount', [$select, $alias]);
    }

    public function distinct(bool $val = true): static
    {
        return $this->buffer('distinct', [$val]);
    }

    public function from($from, bool $overwrite = false): static
    {
        if ($this->table === null) {
            $this->table = is_array($from) ? (string) reset($from) : (string) $from;
        } else {
            $this->buffer('from', [$from]);
        }

        return $this;
    }

    public function join(string $table, string $cond, string $type = '', ?bool $escape = null): static
    {
        return $this->buffer('join', [$table, $cond, $type, $escape]);
    }

    public function where($key, $value = null, ?bool $escape = null): static
    {
        return $this->buffer('where', [$key, $value, $this->escapeFor($key, $value, $escape)]);
    }

    public function or_where($key, $value = null, ?bool $escape = null): static
    {
        return $this->buffer('orWhere', [$key, $value, $this->escapeFor($key, $value, $escape)]);
    }

    public function where_in(?string $key = null, $values = null, ?bool $escape = null): static
    {
        return $this->buffer('whereIn', [$key, $values, $escape]);
    }

    public function or_where_in(?string $key = null, $values = null, ?bool $escape = null): static
    {
        return $this->buffer('orWhereIn', [$key, $values, $escape]);
    }

    public function where_not_in(?string $key = null, $values = null, ?bool $escape = null): static
    {
        return $this->buffer('whereNotIn', [$key, $values, $escape]);
    }

    public function or_where_not_in(?string $key = null, $values = null, ?bool $escape = null): static
    {
        return $this->buffer('orWhereNotIn', [$key, $values, $escape]);
    }

    public function like($field, string $match = '', string $side = 'both', ?bool $escape = null): static
    {
        return $this->buffer('like', [$field, $match, $side, $escape]);
    }

    public function or_like($field, string $match = '', string $side = 'both', ?bool $escape = null): static
    {
        return $this->buffer('orLike', [$field, $match, $side, $escape]);
    }

    public function not_like($field, string $match = '', string $side = 'both', ?bool $escape = null): static
    {
        return $this->buffer('notLike', [$field, $match, $side, $escape]);
    }

    public function or_not_like($field, string $match = '', string $side = 'both', ?bool $escape = null): static
    {
        return $this->buffer('orNotLike', [$field, $match, $side, $escape]);
    }

    public function group_by($by, ?bool $escape = null): static
    {
        return $this->buffer('groupBy', [$by, $escape]);
    }

    public function having($key, $value = null, ?bool $escape = null): static
    {
        return $this->buffer('having', [$key, $value, $escape]);
    }

    public function or_having($key, $value = null, ?bool $escape = null): static
    {
        return $this->buffer('orHaving', [$key, $value, $escape]);
    }

    public function order_by(string $orderBy, string $direction = '', ?bool $escape = null): static
    {
        return $this->buffer('orderBy', [$orderBy, $direction, $escape]);
    }

    public function limit(?int $value = null, ?int $offset = 0): static
    {
        return $this->buffer('limit', [$value, $offset]);
    }

    public function offset(int $offset): static
    {
        return $this->buffer('offset', [$offset]);
    }

    public function group_start(): static
    {
        return $this->buffer('groupStart', []);
    }

    public function or_group_start(): static
    {
        return $this->buffer('orGroupStart', []);
    }

    public function not_group_start(): static
    {
        return $this->buffer('notGroupStart', []);
    }

    public function group_end(): static
    {
        return $this->buffer('groupEnd', []);
    }

    public function set($key, $value = '', ?bool $escape = null): static
    {
        return $this->buffer('set', [$key, $value, $escape]);
    }

    /**
     * CI3 accepted a raw string condition as the only argument, e.g.
     * where("( a LIKE '%x%' OR b LIKE '%x%' )"). CI4 would try to escape that
     * into an identifier, so raw conditions are passed through unescaped.
     */
    private function escapeFor($key, $value, ?bool $escape): ?bool
    {
        if ($escape !== null) {
            return $escape;
        }

        if (is_string($key) && $value === null && preg_match('/[()]|\s(LIKE|IN|BETWEEN|IS|OR|AND)\s/i', $key)) {
            return false;
        }

        return null;
    }

    // ---------------------------------------------------------------------
    // Terminal calls
    // ---------------------------------------------------------------------

    private function builder(?string $table = null): BaseBuilder
    {
        $table ??= $this->table;

        if ($table === null) {
            throw new \RuntimeException('No table has been set for this query.');
        }

        $builder = $this->db->table($table);

        foreach ($this->pending as [$method, $args]) {
            $builder->{$method}(...$args);
        }

        return $builder;
    }

    private function reset(): void
    {
        $this->pending = [];
        $this->table   = null;
    }

    public function get($table = '', ?int $limit = null, ?int $offset = null): Result
    {
        $builder = $this->builder($table !== '' ? (string) $table : null);

        if ($limit !== null) {
            $builder->limit($limit, (int) $offset);
        }

        $result = $builder->get();
        $this->reset();

        return new Result($result);
    }

    public function get_where($table = '', $where = null, ?int $limit = null, ?int $offset = null): Result
    {
        if ($where !== null) {
            $this->where($where);
        }

        return $this->get($table, $limit, $offset);
    }

    public function insert(string $table = '', $set = null, ?bool $escape = null): bool
    {
        $builder = $this->builder($table !== '' ? $table : null);
        $result  = $builder->insert($set, $escape);
        $this->reset();

        return (bool) $result;
    }

    public function insert_batch(string $table = '', $set = null, ?bool $escape = null, int $batchSize = 100)
    {
        $builder = $this->builder($table !== '' ? $table : null);
        $result  = $builder->insertBatch($set, $escape, $batchSize);
        $this->reset();

        return $result;
    }

    public function replace(string $table = '', $set = null)
    {
        $builder = $this->builder($table !== '' ? $table : null);
        $result  = $builder->replace($set);
        $this->reset();

        return $result;
    }

    public function update(string $table = '', $set = null, $where = null, ?int $limit = null): bool
    {
        $builder = $this->builder($table !== '' ? $table : null);

        if ($where !== null) {
            $builder->where($where);
        }
        if ($limit !== null) {
            $builder->limit($limit);
        }

        $result = $builder->update($set);
        $this->reset();

        return (bool) $result;
    }

    public function update_batch(string $table = '', $set = null, $index = null, int $batchSize = 100)
    {
        $builder = $this->builder($table !== '' ? $table : null);
        $result  = $builder->updateBatch($set, $index, $batchSize);
        $this->reset();

        return $result;
    }

    public function delete(string $table = '', $where = '', ?int $limit = null): bool
    {
        $builder = $this->builder($table !== '' ? $table : null);

        if ($where !== '' && $where !== null) {
            $builder->where($where);
        }
        if ($limit !== null) {
            $builder->limit($limit);
        }

        $result = $builder->delete();
        $this->reset();

        return (bool) $result;
    }

    public function truncate(string $table = '')
    {
        $builder = $this->builder($table !== '' ? $table : null);
        $result  = $builder->truncate();
        $this->reset();

        return $result;
    }

    public function empty_table(string $table = '')
    {
        $builder = $this->builder($table !== '' ? $table : null);
        $result  = $builder->emptyTable();
        $this->reset();

        return $result;
    }

    public function count_all_results(string $table = '', bool $reset = true): int
    {
        $builder = $this->builder($table !== '' ? $table : null);
        $count   = $builder->countAllResults($reset);

        if ($reset) {
            $this->reset();
        }

        return $count;
    }

    public function count_all(string $table = ''): int
    {
        $count = $this->db->table($table !== '' ? $table : (string) $this->table)->countAll();
        $this->reset();

        return $count;
    }

    public function get_compiled_select($table = '', bool $reset = true): string
    {
        $builder = $this->builder($table !== '' ? (string) $table : null);
        $sql     = $builder->getCompiledSelect($reset);

        if ($reset) {
            $this->reset();
        }

        return $sql;
    }

    public function get_compiled_insert($table = '', bool $reset = true): string
    {
        $builder = $this->builder($table !== '' ? (string) $table : null);
        $sql     = $builder->getCompiledInsert($reset);

        if ($reset) {
            $this->reset();
        }

        return $sql;
    }

    /**
     * Raw query. Returns a Result for SELECTs, the driver's value otherwise.
     */
    public function query(string $sql, $binds = false, bool $setEscapeFlags = true)
    {
        $result = $binds === false
            ? $this->db->query($sql)
            : $this->db->query($sql, $binds);

        $this->reset();

        if ($result instanceof \CodeIgniter\Database\ResultInterface) {
            return new Result($result);
        }

        return $result;
    }

    public function simple_query(string $sql)
    {
        return $this->db->simpleQuery($sql);
    }

    // ---------------------------------------------------------------------
    // Connection-level helpers
    // ---------------------------------------------------------------------

    public function insert_id()
    {
        return $this->db->insertID();
    }

    public function affected_rows(): int
    {
        return $this->db->affectedRows();
    }

    public function last_query(): string
    {
        $query = $this->db->getLastQuery();

        return $query === null ? '' : (string) $query;
    }

    public function escape($str)
    {
        return $this->db->escape($str);
    }

    public function escape_str($str, bool $like = false)
    {
        return $this->db->escapeString($str, $like);
    }

    public function escape_like_str($str)
    {
        return $this->db->escapeLikeString($str);
    }

    /**
     * CI3 returned ['code' => ..., 'message' => ...].
     *
     * @return array{code: int|string|null, message: string|null}
     */
    public function error(): array
    {
        return $this->db->error();
    }

    public function table_exists(string $table): bool
    {
        return $this->db->tableExists($table);
    }

    public function list_tables(bool $constrainByPrefix = false)
    {
        return $this->db->listTables($constrainByPrefix);
    }

    public function field_exists(string $field, string $table): bool
    {
        return $this->db->fieldExists($field, $table);
    }

    public function list_fields(string $table)
    {
        return $this->db->getFieldNames($table);
    }

    public function trans_start(bool $testMode = false): bool
    {
        return $this->db->transStart($testMode);
    }

    public function trans_complete(): bool
    {
        return $this->db->transComplete();
    }

    public function trans_begin(bool $testMode = false): bool
    {
        return $this->db->transBegin($testMode);
    }

    public function trans_commit(): bool
    {
        return $this->db->transCommit();
    }

    public function trans_rollback(): bool
    {
        return $this->db->transRollback();
    }

    public function trans_status(): bool
    {
        return $this->db->transStatus();
    }

    public function close(): void
    {
        $this->db->close();
    }

    public function database(): string
    {
        return $this->db->getDatabase();
    }

    /**
     * Anything not wrapped falls through to the CI4 connection, so the modern
     * API stays available on the same object.
     */
    public function __call(string $name, array $arguments)
    {
        return $this->db->{$name}(...$arguments);
    }

    public function __get(string $name)
    {
        return $this->db->{$name} ?? null;
    }
}
