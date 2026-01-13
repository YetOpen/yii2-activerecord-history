<?php

namespace nhkey\arh\managers;

use yii\db\Expression;
use const SORT_DESC;

/**
 * Class DedicatedTableManager extends DBManager
 * It handles historical records for dedicated tables.
 */
class DedicatedTableManager extends DBManager
{
    /**
     * @inheritdoc
     */
    public function saveField($data)
    {
        if (isset($data['table'])) {
            unset($data['table']);
        }
        parent::saveField($data);
    }

    /**
     * @inheritdoc
     */
    protected function prepareQuery(array $filter, array $order)
    {
        if (isset($filter['table'])) {
            unset($filter['table']);
        }
        return parent::prepareQuery($filter, $order);
    }

    /**
     * @inheritdoc
     */
    public function getRecordValueAt($object, $attribute, $date)
    {
        // We assume that for future dates the value will stay the same since we have no way to know it
        if ($date >= date('Y-m-d H:i:s')) {
            return $object->getAttribute($attribute);
        }
        $query = parent::prepareBaseQuery();
        // Getting the date-time in which the record was created
        $insertDate = (clone $query)
            ->select('date')
            ->andWhere([
                'field_id' => new Expression("'{$object->getPrimaryKey()}'"),
                'type' => static::AR_INSERT,
            ])
            ->scalar();
        // The record was inserted after the specified date, we return `null`
        if ($insertDate > $date) {
            return null;
        }
        // Searching for the first history record for the attribute and active record with date prior to the given one.
        // We order by descendent ID so that it will be returned the most recent.
        $query->select('new_value')
            ->andWhere([
                'field_id' => new Expression("'{$object->getPrimaryKey()}'"),
                'field_name' => $attribute,
            ])
            ->andWhere(['<=', 'date', $date])
            ->orderBy(['id' => SORT_DESC]);
        // If no record was found, it means that the value was never changed, in that case we return the current one.
        return $query->scalar() ?: $object->getAttribute($attribute);
    }
}
