<?php

namespace kkse\quick\sql;

/**
 * sql编译器
 * Class Build
 * @package kkse\quick\sql
 */
class Build
{
    // 数据库表达式
    const WHERE_EXP = [
        'EQ' => '=',
        'NEQ' => '<>',
        'GT' => '>',
        'EGT' => '>=',
        'LT' => '<',
        'ELT' => '<=',
    ];
    const WHERE_SECTION_EXP = [
        '('=>'>',
        ')'=>'<',
        '['=>'>=',
        ']'=>'<=',
        '='=>'=',
        '!'=>'<>',
    ];


    /**
     * 快捷插入一条数据
     * @param $table
     * @param $data
     * @param array $options
     * @return Prepare
     */
    public static function quickInsert($table, array $data, array $options = [])
    {
        $options = array_change_key_case($options, CASE_UPPER);
        $mode = 'insert';
        $insertSql = '%INSERT% INTO %TABLE% (%FIELD%) VALUES (%DATA%) %COMMENT%';
        $fields = array_keys($data);
        $values = array_values($data);
        $prepares = array_fill(0, count($values), '?');
        $insert = 'INSERT';
        if (!empty($options['REPLACE'])) {
            $insert = 'REPLACE';
        } elseif (!empty($options['IGNORE'])) {
            $insert = 'INSERT IGNORE';
        }

        $sql = str_replace(
            ['%INSERT%', '%TABLE%', '%FIELD%', '%DATA%', '%COMMENT%'],
            [
                $insert,
                $table,
                implode(' , ', $fields),
                implode(' , ', $prepares),
                '',
            ], $insertSql);

        return new Prepare($mode, $sql, $values);
    }

    /**
     * @param array $where
     * @param string $glue
     * @return array
     */
    public static function quickWhere(array $where, $glue = 'AND')
    {
        $where_values = [];
        $where_sqls = [];
        foreach ($where as $field => $value) {

            if (is_scalar($value)) {
                $where_sqls[] = "`{$field}` = ?";
                $where_values[] = $value;
                continue;
            }
            if (is_array($value) && isset($value[0], $value[1])) {
                $exp = strtoupper($value[0]);
                isset(self::WHERE_EXP[$exp]) and $exp = self::WHERE_EXP[$exp];

                if (in_array($exp, ['LIKE', '>=', '>', '<=', '<', '<>', '=']) && is_scalar($value[1])) {
                    $where_sqls[] = "`{$field}` {$exp} ?";
                    $where_values[] = $value[1];
                    continue;
                }

                if ($exp == 'IN' && is_array($value[1]) && ($num = count($value[1])) > 0) {
                    if ($num > 1) {
                        $set_in_sql = implode(' , ', array_fill(0 , $num , '?'));
                        $where_sqls[] = "`{$field}` IN ({$set_in_sql})";
                        $where_values = array_merge($where_values, array_values($value[1]));
                    } else {
                        $where_sqls[] = "`{$field}` = ?";
                        $where_values[] = reset($value[1]);
                    }
                    continue;
                }

                if (is_array($value[1])) {
                    $mult_exp = str_split($exp);//多元判断
                    $val_num = count($value[1]);
                    if (count($mult_exp) == $val_num) {
                        $mult_sqls = [];
                        foreach ($mult_exp as $sub_exp) {
                            if (!isset(self::WHERE_SECTION_EXP[$sub_exp])) break;
                            $mult_sqls[] = sprintf('`%s` %s ?', $field, self::WHERE_SECTION_EXP[$sub_exp]);
                        }

                        if (count($mult_sqls) == $val_num) {
                            $sub_glue = 'AND';
                            if (isset($value[2]) && strtoupper(strval($value[2])) == 'OR') {
                                $sub_glue = 'OR';
                            }
                            $where_sqls[] = '('.implode(" {$sub_glue} ", $mult_sqls).')';
                            $where_values = array_merge($where_values, array_values($value[1]));
                            continue;
                        }
                    }
                }
            }

            return [false, false];
        }

        if (!$where_sqls) {
            return ['', []];
        }
        $where_sql = ' WHERE ' . implode(" {$glue} ", $where_sqls);
        return [$where_sql, $where_values];
    }

    /**
     * @param array $set_data
     * @return array
     */
    public static function quickSet(array $set_data)
    {
        $set_sqls = [];
        $set_values = [];
        foreach ($set_data as $field => $value) {

            if (is_scalar($value)) {
                $set_sqls[] = "`{$field}` = ?";
                $set_values[] = $value;
                continue;
            }

            if (is_array($value) && isset($value[0], $value[1])
                && in_array($value[0], ['+', '-']) && is_numeric($value[1])) {
                $set_sqls[] = "`{$field}` {$value[0]}= ?";
                $set_values[] = $value[1];
            }

            return [false, false];
        }

        if (!$set_sqls) {
            return [false, false];
        }

        $set_sql = ' SET ' . implode(' , ', $set_sqls);

        return [$set_sql, $set_values];
    }


}