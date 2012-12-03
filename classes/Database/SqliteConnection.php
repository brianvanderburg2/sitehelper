<?php

// File:        SqliteGrammar.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Connection for Sqlite

namespace MrBavii\SiteHelper\Database;

class SqliteConnection extends Connection
{
    public function quote_column($col)
    {
        $p = explode('.', $col);
        if(count($p) == 2)
        {
            return $this->quote_table($p[0]) . ".`{$p[1]}`";
        }
        else
        {
            return "`{$p[0]}`";
        }
    }

    public function quote_table($table)
    {
        return "`$table`";
    }

    public function quote_value($value)
    {
        return $this->driver->quote($value);
    }

    public function format_isnull($col)
    {
        return $this->quote_column($col) . ' ISNULL';
    }

    public function format_isnotnull($col)
    {
        return $this->quote_column($col) . ' NOTNULL';
    }

    protected function prepare_like($like)
    {
        $like = str_replace('\\', '\\\\', $like);
        $like = str_replace('%', '\\%', $like);
        $like = str_replace('_', '\\_', $like);
        $like = str_replace('*', '%', $like);

        return $this->quote_value($like);
    }

    public function format_islike($col, $like)
    {
        return $this->quote_column($col) . ' LIKE ' . $this->prepare_like($like);
    }

    public function format_isnotlike($col, $like)
    {
        return 'NOT ' . $this->format_islik($col, $like);
    }

    public function format_limit($limit, $offset)
    {
        $sql = '';

        if($limit !== null)
        {
            $limit = (int)$limit;
            $sql .= "LIMIT $limit";
        }

        if($offset !== null)
        {
            $offset = (int)$offset;
            $sql .= " OFFSET $offset";
        }

        return $sql;
    }
}

