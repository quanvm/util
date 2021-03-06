<?php

namespace go1\util\note;

use Doctrine\DBAL\Connection;
use go1\util\DB;

class NoteHelper
{
    public static function load(Connection $db, int $id)
    {
        $sql = 'SELECT * FROM gc_note WHERE id = ?';

        return $db->executeQuery($sql, [$id])->fetch(DB::OBJ);
    }

    public static function loadByUUID(Connection $db, string $uuid)
    {
        $sql = 'SELECT * FROM gc_note WHERE uuid = ?';

        return $db->executeQuery($sql, [$uuid])->fetch(DB::OBJ);
    }
}
