<?php

// File:        PdoQuery.php
// Author:      Brian Allen Vanderburg II
// Purpose:     Query object for PDO results

namespace MrBavii\SiteHelper\Database;

class PdoQuery extends Query
{
    protected $stmt = null;

    public function __construct($stmt)
    {
        $this-stmt = $stmt;
    }

    public function __destruct()
    {
        $this->stmt->closeCursor();
        $this->stmt = null;
    }

    public function next()
    {
        try
        {
            return $this->stmt->fetch(\PDO::FETCH_ASSOC);
        }
        catch(\PDOException $e)
        {
            throw new Exception('Error during fetch.', 0, $e);
        }
    }
}


