<?php
/**
 * Created by PhpStorm.
 * User: zhj
 * Date: 2020/5/25
 * Time: 17:46
 */

namespace SCLZZHJ\Schema;



class Schema
{
    /**
     * 数据库名称
     * User: zhj
     * @var
     */
    private $dbName;

    /**
     * 执行SQL回调函数
     * User: zhj
     * @var SchemaInterFace
     */
    private $sqlExecCallBack;

    /**
     * Schema constructor.
     * @param SchemaInterFace $sqlExecCallBack
     * @param $dbName
     */
    public function __construct(SchemaInterFace $sqlExecCallBack, $dbName)
    {
        $this->sqlExecCallBack = $sqlExecCallBack;
        $this->dbName = $dbName;
    }

    /**
     * 生成数据
     * User: zhj
     * @throws SchemaException
     */
    public function make()
    {
        $tables = $this->sqlExecCallBack->SqlExec("SELECT * FROM `TABLES` WHERE `TABLE_SCHEMA` = '{$this->dbName}'");

        if (!is_array($tables)) {
            throw new SchemaException("返回结果必须是数组");
        }
        $html = '<table class="table_schema">';
        foreach ($tables as $key => $rowTable) {
            if ($key > 1) {
                $html .= '<tr>';
                $html .= '<td colspan="4">&nbsp;</td>';
                $html .= '</tr>';
            }
            $html .= '<tr class="table_name" style="font-weight: bold">';
            $html .= '<td>' . $rowTable['TABLE_NAME'] . '</td>';
            $html .= '<td colspan="3">' . $rowTable['TABLE_COMMENT'] . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>字段名称</td>';
            $html .= '<td>字段类型</td>';
            $html .= '<td>字段描述</td>';
            $html .= '<td>备注</td>';
            $html .= '</tr>';
            $sqlColumn = "SELECT * FROM `COLUMNS` WHERE `TABLE_SCHEMA` = '{$this->dbName}' AND `TABLE_NAME` = '{$rowTable['TABLE_NAME']}'"; //构建查询语句
            $queryColumns = $this->sqlExecCallBack->SqlExec($sqlColumn);
            if (!is_array($queryColumns)) {

            }
            foreach ($queryColumns as $rowColumn) {
                $html .= '<tr>';
                $html .= '<td>' . $rowColumn['COLUMN_NAME'] . '</td>';
                $html .= '<td>' . $rowColumn['COLUMN_TYPE'] . '</td>';
                $html .= '<td>' . $rowColumn['COLUMN_COMMENT'] . '</td>';
                $html .= '<td>&nbsp;</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</table>';
        return $html;
    }
}