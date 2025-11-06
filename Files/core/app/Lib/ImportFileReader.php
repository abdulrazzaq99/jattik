<?php

namespace App\Lib;

use Exception;
use \PhpOffice\PhpSpreadsheet\IOFactory;

class ImportFileReader
{
    public $dataInsertMode = true;
    public $columns = [];
    public $uniqueColumns = [];
    public $modelName;
    public $file;
    public $fileSupportedExtension = ['csv', 'xlsx', 'txt'];
    public $allData = [];
    public $allUniqueData = [];
    public $notify = [];

    public function __construct($file, $modelName = null)
    {
        $this->file      = $file;
        $this->modelName = $modelName;
    }

    public function readFile()
    {
        $fileExtension = $this->file->getClientOriginalExtension();

        if (!in_array($fileExtension, $this->fileSupportedExtension)) {
            return $this->exceptionSet("File type not supported");
        }

        $spreadsheet = IOFactory::load($this->file);
        $data        = $spreadsheet->getActiveSheet()->toArray();

        if (count($data) <= 0) {
            return   $this->exceptionSet("File can not be empty");
        }

        $this->validateFileHeader(array_filter(@$data[0]));

        unset($data[0]);

        foreach ($data as  $item) {
            array_map('trim', $item);
            $this->dataReadFromFile($item);
        };

        return $this->saveData();
    }

    public function validateFileHeader($fileHeader)
    {
        if (!is_array($fileHeader) || count($fileHeader) != count($this->columns)) {
            $this->exceptionSet("Invalid file format");
        }

        foreach ($fileHeader as $k => $header) {
            if (trim(strtolower($header)) != strtolower(@$this->columns[$k])) {
                $this->exceptionSet("Invalid file format");
            }
        }
    }

    public function dataReadFromFile($data)
    {
        if (gettype($data) != 'array') {
            return $this->exceptionSet('Invalid data formate provided inside upload file.');
        }

        if (count($data) != count($this->columns)) {
            return  $this->exceptionSet('Invalid data formate provided inside upload file.');
        }

        if ($this->dataInsertMode && (!$this->uniqueColumCheck($data))) {
            $this->allUniqueData[] = array_combine($this->columns, $data);
        }

        $this->allData[] = $data;
    }

    function uniqueColumCheck($data)
    {

        $combinedData      = array_combine($this->columns, $data);
        $uniqueColumns     = array_intersect($this->uniqueColumns, $this->columns);
        $uniqueColumnCheck = false;

        foreach ($uniqueColumns as $uniqueColumn) {
            $uniqueColumnsValue = $combinedData[$uniqueColumn];
            if ($uniqueColumnsValue && $uniqueColumn) {
                $uniqueColumnCheck = $this->modelName::where($uniqueColumn, $uniqueColumnsValue)->exists();
            }
        }

        return $uniqueColumnCheck;
    }

    public function saveData()
    {
        if (count($this->allUniqueData) > 0 && $this->dataInsertMode) {
            try {
                $this->modelName::insert($this->allUniqueData);
            } catch (Exception $e) {
                $this->exceptionSet('This file can\'t be uploaded. It may contains duplicate data.');
            }
        }

        $this->notify = [
            'success' => true,
            'message' => count($this->allUniqueData) . " data uploaded successfully total " . count($this->allData) . ' data'
        ];
    }

    public function exceptionSet($exception)
    {
        throw new Exception($exception);
    }

    public function getReadData()
    {
        return $this->allData;
    }

    public function notifyMessage()
    {
        $notify = (object) $this->notify;
        return $notify;
    }
}
