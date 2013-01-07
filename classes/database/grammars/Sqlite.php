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

    public function format_create_table($table, $columns, $constraints)
    {
        // Base create table statement
        $colsql = array();
        foreach($columns as $name => $column)
        {
            $colsql[] = $this->coldef($name, $column, $constraints);
        }

        $sql = 'CREATE TABLE ' . $this->quote_table($table) . ' (';
        $sql .= implode(', ', $colsql);

        // Uniques
        foreach($constraints['unique'] as $unique)
        {
            $colsql = array();
            foreach($unique as $col)
            {
                $colsql[] = $this->quote_column($col);
            }
            $sql .= ', UNIQUE (' . implode(', ', $colsql) . ')';
        }

        // Foreign keys
        foreach($constraints['fkey'] as $col => $ref)
        {
            $sql .= ', FOREIGN KEY (' . $this->quote_column($col) . ')';
            $sql .= ' REFERENES ' . $this->quote_table($ref[0]) . ' (' . $this->quote_column($ref[1]) . ')';
        }

        // Done
        $sql .= ')';

        return $sql;
    }

    protected function coldef($name, $column, $constraints)
    {
        // Column Type
        $type = array_shift($column);
        switch($type)
        {
            case 'rowid':
                $typestr = 'INTEGER PRIMARY KEY AUTO INCREMENT';
                if($constraints['pkey'] == $name)
                {
                    $constraints['pkey'] = '';
                }
                break;

            case 'rowidref':
            case 'int8':
            case 'int16':
            case 'int32':
            case 'int64':
                $typestr = 'INTEGER';
                break;

            case 'uint8';
            case 'uint16':
            case 'uint32':
            case 'uint64':
                $typestr = 'UNSIGNED INTEGER';
                break;

            case 'char':
                $count = (int)array_shift($column);
                $typestr = "CHAR($count)";
                break;

            case 'varchar':
                $count = (int)array_shift($column);
                $typestr = "VARCHAR($count)";
                break;
                break;
        }

        // Constraints
        if($constraints['pkey'] == $name)
        {
            $typestr .= ' PRIMARY KEY';
        }

        if(in_array($name, $constraints['notnull']))
        {
            $typestr .= ' NOT NULL';
        }

        if(isset($constraints['default'][$name]))
        {
            $typestr .= ' DEFAULT ' . $this->quote_value($constraints['default'][$name]);
        }

        return $this->quote_column($name) . ' ' . $typestr;
    }
}

