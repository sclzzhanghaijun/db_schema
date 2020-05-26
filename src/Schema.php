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
     * 指定表
     * User: zhj
     * @var array
     */
    private $tables = [];

    /**
     * Schema constructor.
     * @param SchemaInterFace $sqlExecCallBack
     * @param $dbName
     */
    public function __construct(SchemaInterFace $sqlExecCallBack, $dbName, $tables = [])
    {
        $this->sqlExecCallBack = $sqlExecCallBack;
        $this->dbName = $dbName;
        $this->tables = $tables;
    }

    /**
     * 生成数据
     * User: zhj
     * @throws SchemaException
     */
    public function make()
    {
        $tables = $this->sqlExecCallBack->SqlExec("SELECT * FROM information_schema.`TABLES` WHERE `TABLE_SCHEMA` = '{$this->dbName}'");

        if (!is_array($tables)) {
            throw new SchemaException("返回结果必须是数组");
        }
        $html = '<table class="table_schema">';
        foreach ($tables as $key => $rowTable) {
            //过滤表
            if ($this->tables && !in_array($rowTable['TABLE_NAME'], $this->tables)) {
                continue;
            }
            if ($key > 0) {
                $html .= '<tr>';
                $html .= '<td colspan="7">&nbsp;</td>';
                $html .= '</tr>';
            }
            $html .= '<tr class="table_name" style="font-weight: bold">';
            $html .= '<td>' . $rowTable['TABLE_NAME'] . '</td>';
            $html .= '<td colspan="6">' . $rowTable['TABLE_COMMENT'] . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>字段名称</td>';
            $html .= '<td>字段类型</td>';
            $html .= '<td>默认值</td>';
            $html .= '<td>是否为空</td>';
            $html .= '<td>列KEy</td>';
            $html .= '<td>字段描述</td>';
            $html .= '<td>备注</td>';
            $html .= '</tr>';
            $sqlColumn = "SELECT * FROM information_schema.`COLUMNS` WHERE `TABLE_SCHEMA` = '{$this->dbName}' AND `TABLE_NAME` = '{$rowTable['TABLE_NAME']}'"; //构建查询语句
            $queryColumns = $this->sqlExecCallBack->SqlExec($sqlColumn);
            if (!is_array($queryColumns)) {
                throw  new SchemaException("获取数据表信息不是数组");
            }
            foreach ($queryColumns as $rowColumn) {
                $html .= '<tr>';
                $html .= '<td>' . $rowColumn['COLUMN_NAME'] . '</td>';
                $html .= '<td>' . $rowColumn['COLUMN_TYPE'] . '</td>';
                $html .= '<td>' . $rowColumn['COLUMN_DEFAULT'] . '</td>';
                $html .= '<td>' . $rowColumn['IS_NULLABLE'] . '</td>';
                $html .= '<td>' . $rowColumn['COLUMN_KEY'] . '</td>';
                $html .= '<td>' . $rowColumn['COLUMN_COMMENT'] . '</td>';
                $html .= '<td>' . $rowColumn['EXTRA'] . '</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</table>';
        return $html;
    }
}