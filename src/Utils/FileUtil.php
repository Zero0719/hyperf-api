<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Utils;

use Hyperf\Collection\Arr;
use Hyperf\Context\ApplicationContext;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Stringable\Str;
use Zero0719\HyperfApi\Exception\BusinessException;

class FileUtil
{
    /**
     * @param UploadedFile $file
     * @param string $path
     * @param $keepName
     * @return string[]
     */
    public static function uploadFile(UploadedFile $file, string $path = '', $keepName = false)
    {
        $extension = $file->getExtension();

        if (!$keepName) {
            $fileName = date('YmdHis') . Str::random(6).'.'.$extension;
        }

        if ($path) {
            $path = BASE_PATH . DIRECTORY_SEPARATOR . 'runtime/uploads' . DIRECTORY_SEPARATOR . $path;
        } else {
            $path = BASE_PATH . DIRECTORY_SEPARATOR . 'runtime/uploads';
        }
        
        if (!file_exists($path) && !mkdir($path)) {
            throw new BusinessException('目录自动创建失败');
        }
        
        $fullPath = $path . DIRECTORY_SEPARATOR . $fileName;

        $file->moveTo($fullPath);
        
        if (!$file->isMoved()) {
            throw new BusinessException('上传失败');
        }

        return [
            'fullPath' => $fullPath,
            'fileName' => $fileName,
        ];
    }


    /**
     * @param UploadedFile $file
     * @param string $path
     * @param $keepName
     * @return string[]
     */
    public static function uploadExcel(UploadedFile $file, string $path = '', $keepName = false)
    {
        if (!in_array($file->getExtension(), ['xls', 'xlsx'])) {
            throw new BusinessException('文件格式为xls或xlsx');
        }

        return self::uploadFile($file, $path, $keepName);
    }


    /**
     * @param UploadedFile $file
     * @param string $path
     * @param $keepName
     * @return string[]
     */
    public static function uploadImage(UploadedFile $file, string $path = '', $keepName = false)
    {
        if (!in_array($file->getExtension(), ['jpg', 'png', 'gif', 'bmp', 'jpeg', 'webp', 'svg', 'ico', 'tif', 'tiff', 'psd', 'raw', 'ai', 'eps', 'svgz', 'png', 'jpg', 'jpeg', 'webp', 'svg', 'ico', 'tif', 'tiff', 'psd', 'raw', 'ai', 'eps', 'svg'])) {
            throw new BusinessException('文件格式错误');
        }

        return self::uploadFile($file, $path, $keepName);
    }


    // 下载文件
    public static function downloadFile(string $filePath, string $fileName = '')
    {
        if (!file_exists($filePath)) {
            throw new BusinessException('文件不存在');
        }

        $response = ApplicationContext::getContainer()->get(ResponseInterface::class);

        return $response->download($filePath, $fileName);
    }

    public static function excelToArray($excelFileLocation, $isAssocMode = false)
    {
        if (!file_exists($excelFileLocation)) {
            throw new BusinessException('文件不存在');
        }

        list($path, $filename) = array_values(Arr::only(pathinfo($excelFileLocation), ['dirname', 'basename']));

        $excel = new \Vtiful\Kernel\Excel(['path' => $path]);

        $file = $excel->openFile($filename);
        $sheet = $file->openSheet();

        $data = [];
        $index = 0;
        // 映射模式第一行数据为数组的key
        while ($row = $sheet->nextRow() !== NULL) {
            if ($isAssocMode && $index === 0) {
                $keys = $row;
                continue;
            }
            $data[] = isset($keys) ? array_combine($keys, $row) : $row;
            $index++;
        }

        return $data;
    }

    /**
     * 将数组写入指定位置文件中，后续处理由上层决定
     * @param array $data
     * @param $fileName
     * @return mixed
     */
    public static function arrayToExcel(Array $data, string $excelFileLocation)
    {
        if (!$excelFileLocation) {
            throw new BusinessException('file location is empty');
        }

        list($path, $filename) = array_values(Arr::only(pathinfo($excelFileLocation), ['dirname', 'basename']));

        if (!is_dir($path)) {
            throw new BusinessException('file path is not a directory or not exists');
        }

        $excel = new \Vtiful\Kernel\Excel(['path' => $path]);
        $fileObject = $excel->constMemory($filename);
        return $fileObject->data($data)->output();
    }
}