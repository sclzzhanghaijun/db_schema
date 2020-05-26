#### db_schema
本项目主要是生成Mysql的表结构，返回Html，可以自定义显示table样式。支持所有php项目，只需要实现数据查询方法。

#### 安装
```bash
composer require sclzzhanghaijun/db_schema
```

### Laravel使用
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SCLZZHJ\Schema\Schema;
use SCLZZHJ\Schema\SchemaInterFace;

//实现接口
class DBSchema implements SchemaInterFace
{
    public function SqlExec($sql)
    {
        $result =  DB::select($sql);
        if($result){
            $result = $this->object_array($result);
        }
        return $result;
    }

    //需要将对象转换为数组
    public function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        } if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }
}

//使用命令方式调用
class SchemaCommand extends Command
{
    protected $signature = 'make:table_schema';
    protected $description = '生成数据表结构';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $schema = new Schema(new DBSchema(), config('database.connections.mysql.database'));
        try {
            $table = $schema->make();
            $html = <<<EOF
    <html lang="zh">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style ="text/css">
    /*table start*/  
    table{  
        /* -moz-border-radius: 5px;  
        -webkit-border-radius:5px;  
        border-radius:5px; */  
        margin: auto;
        width: 50%;  
        border:solid #333;   
        border-width:1px 0px 0px 1px;  
        font-size: #333;  
        border-collapse: collapse;  
        border-spacing: 0;  
        font-size: 13px;
    }  
    table tbody tr{  
        height: 20px;  
        line-height: 20px;  
    }  
    table tbody tr.odd{  
        background-color: #fff;  
    }  
    table tbody tr.even{  
        background-color: #F5F5F5;  
    }  
    table tbody tr:hover{  
        background-color: #eee;  
    }  
    table tbody tr th,table tbody tr td{  
        padding:3px 5px;  
        text-align: left;  
        /* border: 1px solid #ddd; */  
        border:solid #333;   
        border-width:0px 1px 1px 0px;   
    }  
    table tbody tr th{  
        font-weight: bold;  
        text-align: center;  
    }  
    table tbody tr td a:hover{  
        color:#0080c0;  
    }  
    /*table end*/ 
</style>
EOF;
            $html .= "<body>" . $table . "</body></html>";
            file_put_contents("./index.html", $html);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }
}
```

### 最终效果
![图片](/images/image1.png)