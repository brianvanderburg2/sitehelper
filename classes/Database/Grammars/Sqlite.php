<?php

// File:        Sqlite.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Grammar for Sqlite

namespace MrBavii\SiteHelper\Database\Grammars;
use MrBavii\SiteHelper\Database;

class Sqlite extends Grammar
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
        return $this->connector->quote($value);
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
        return $this->quote_column($col) . ' LIKE ' . $this->prepare_like($like) . "ESCAPE '\\'";
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

