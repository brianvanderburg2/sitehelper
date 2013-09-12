<?php

// File:        Sqlite.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Grammar for Sqlite

namespace mrbavii\sitehelper\database\grammars;
use mrbavii\sitehelper\database;

class Sqlite extends Grammar
{
    public function quoteColumn($col)
    {
        $p = explode('.', $col);
        if(count($p) == 2)
        {
            return $this->quoteTable($p[0]) . ".`{$p[1]}`";
        }
        else
        {
            return "`{$p[0]}`";
        }
    }

    public function quoteTable($table)
    {
        return "`$table`";
    }

    public function quoteValue($value)
    {
        return $this->connector->quote($value);
    }

    public function formatNull($col)
    {
        return $this->quoteColumn($col) . ' ISNULL';
    }

    public function formatNotNull($col)
    {
        return $this->quoteColumn($col) . ' NOTNULL';
    }

    protected function prepareLike($like)
    {
        $like = str_replace('\\', '\\\\', $like);
        $like = str_replace('%', '\\%', $like);
        $like = str_replace('_', '\\_', $like);
        $like = str_replace('*', '%', $like);

        return $this->quoteValue($like);
    }

    public function formatLike($col, $like)
    {
        return $this->quoteColumn($col) . ' LIKE ' . $this->prepareLike($like) . " ESCAPE '\\'";
    }

    public function formatNotLike($col, $like)
    {
        return 'NOT ' . $this->formatLike($col, $like);
    }

    public function formatLimit($limit, $offset)
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

    public function formatCreateTable($table, $columns, $constraints, $ifnotexists)
    {
        // Base create table statement
        $colsql = array();
        foreach($columns as $name => $column)
        {
            $colsql[] = $this->coldef($name, $column, $constraints);
        }

        $ifnotexists = ($ifnotexists ? ' IF NOT EXISTS ' : '');
        $sql = 'CREATE TABLE ' . $ifnotexists . $this->quoteTable($table) . ' (';
        $sql .= implode(', ', $colsql);

        // Uniques
        foreach($constraints['unique'] as $unique)
        {
            $colsql = array();
            foreach($unique as $col)
            {
                $colsql[] = $this->quoteColumn($col);
            }
            $sql .= ', UNIQUE (' . implode(', ', $colsql) . ')';
        }

        // Foreign keys
        foreach($constraints['fkey'] as $col => $ref)
        {
            $sql .= ', FOREIGN KEY (' . $this->quoteColumn($col) . ')';
            $sql .= ' REFERENCES ' . $this->quoteTable($ref[0]) . ' (' . $this->quoteColumn($ref[1]) . ')';
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
                $typestr = 'INTEGER PRIMARY KEY AUTOINCREMENT';
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
            $typestr .= ' DEFAULT ' . $this->quoteValue($constraints['default'][$name]);
        }

        return $this->quoteColumn($name) . ' ' . $typestr;
    }
}

