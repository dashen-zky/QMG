<?php

namespace backend\models\system_overwrite;
class Schema extends \yii\db\mysql\Schema
{
    public function createQueryBuilder()
    {
        return new QueryBuilder($this->db);
    }
}