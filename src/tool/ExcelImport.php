<?php
/**
 * Created by PhpStorm.
 * User: kkse
 * Date: 2018/7/18
 * Time: 15:42
 */

namespace kkse\quick\tool;

use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_IOFactory;

/**
 * Excel文件导入类
 * Class ExcelImport
 * @package Com\Tool
 */
class ExcelImport
{
    protected $file;
    protected $fields;
    protected $option = [
        'ignore_header'=>0,
    ];
    protected $extensionType;//导入的类型

    public function __construct($file, array $fields, array $option = [])
    {
        $this->setFile($file);
        $this->setFields($fields);
        $this->setOption($option);
    }

    /**
     * @param $file
     * @return bool
     */
    public function setFile($file)
    {
        $this->file = null;
        $this->extensionType = null;
        if (is_array($file) && isset($file['tmp_name'], $file['name']) && is_uploaded_file($file['tmp_name'])){//上传文件
            $pathinfo = pathinfo($file['name']);
            $file_path = $file['tmp_name'];
        } elseif (is_string($file) && is_file($file)) {
            $pathinfo = pathinfo($file);
            $file_path = $file;
        } else {
            //文件不正确
            return false;
        }

        $extensionType = NULL;
        switch (strtolower($pathinfo['extension'])) {
            case 'xlsx':			//	Excel (OfficeOpenXML) Spreadsheet
            case 'xlsm':			//	Excel (OfficeOpenXML) Macro Spreadsheet (macros will be discarded)
            case 'xltx':			//	Excel (OfficeOpenXML) Template
            case 'xltm':			//	Excel (OfficeOpenXML) Macro Template (macros will be discarded)
                $extensionType = 'Excel2007';
                break;
            case 'xls':				//	Excel (BIFF) Spreadsheet
            case 'xlt':				//	Excel (BIFF) Template
                $extensionType = 'Excel5';
                break;
            case 'ods':				//	Open/Libre Offic Calc
            case 'ots':				//	Open/Libre Offic Calc Template
                $extensionType = 'OOCalc';
                break;
            case 'slk':
                $extensionType = 'SYLK';
                break;
            case 'xml':				//	Excel 2003 SpreadSheetML
                $extensionType = 'Excel2003XML';
                break;
            case 'gnumeric':
                $extensionType = 'Gnumeric';
                break;
            case 'htm':
            case 'html':
                $extensionType = 'HTML';
                break;
            case 'csv':
                $extensionType = 'CSV';
                break;
            default:
                break;
        }

        if ($extensionType === NULL) {
            return false;
        }
        $this->file = $file_path;
        $this->extensionType = $extensionType;

        return true;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function setOption(array $option)
    {
        $this->option = array_merge($this->option, $option);
        return $this;
    }

    /**
     * 检查参数数据是否正常
     * @return bool
     */
    public function check()
    {
        if (empty($this->file)) {
            return false;
        }

        foreach ($this->fields as $key => $field) {
            if (empty($field['col']) || !is_int($field['col'])) {//col必要,filter、no_empty可选
               return false;
            }
        }

        return true;
    }

    /**
     * 获取迭代器对象
     * @return \Generator|null
     */
    public function getIterator()
    {
        if (!$this->check()) {
            return null;
        }

        return $this->doIterator();
    }

    /**
     *
     * @return \Generator
     */
    protected function doIterator(){
        $extensionType = $this->extensionType;
        $file_path = $this->file;
        $ignore_header = intval($this->option['ignore_header']);
        $fields = $this->fields;

        if ($extensionType == 'CSV') {
            $row = 0;
            if (($handle = fopen($file_path, "r")) !== false) {
                try {
                    while (($data = fgetcsv($handle)) !== false) {
                        $row++;
                        if ($row <= $ignore_header) {
                            continue;
                        }

                        $fail = false;
                        $fields_data = [];
                        foreach ($fields as $key => $field) {
                            $col = $field['col']-1;
                            $val = isset($data[$col])?$data[$col]:'';
                            if (empty($val) && !empty($field['no_empty'])) {
                                $fail = true;
                                break;
                            }

                            $fields_data[$key] = $val;
                        }

                        if ($fail) {
                            continue;
                        }

                        yield $fields_data;

                    }
                } finally {
                    //防止中断循环时没有释放资源
                    fclose($handle);
                }
            }
        }
        else {
            $reader = PHPExcel_IOFactory::createReader($extensionType);
            if (!isset($reader) || !$reader->canRead($file_path)) {
                return;
            }
            /** @var PHPExcel $PHPExcel */
            $PHPExcel = $reader->load($file_path);

            $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            //$highestColumm = $sheet->getHighestColumn(); // 取得总列数

            /** 循环读取每个单元格的数据 */
            for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
                if ($row <= $ignore_header) {
                    continue;
                }

                $fail = false;
                //$num = count($data);
                $fields_data = [];
                foreach ($fields as $key => $field) {
                    $col = PHPExcel_Cell::stringFromColumnIndex($field['col']-1);

                    $cell = $sheet->getCell($col.$row);
                    $val = $cell?$cell->getValue():'';

                    if (!empty($field['filter']) && is_callable($field['filter'])) {
                        $val = call_user_func($field['filter'], $val);
                    }

                    if (empty($val) && !empty($field['no_empty'])) {
                        $fail = true;
                        break;
                    }

                    $fields_data[$key] = $val;
                }

                if ($fail) {
                    continue;
                }
                yield $fields_data;
            }
        }
    }

    /**
     * 提供一个静态方法获取迭代器对象
     * @param $file
     * @param array $fields
     * @param array $option
     * @return \Generator|null
     */
    public static function quickIterator($file, array $fields, array $option = [])
    {
        $obj = new self($file, $fields, $option);
        return $obj->getIterator();
    }
}