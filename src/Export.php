<?php

namespace Vicoders\Tool;

use DateTime;
use NF\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Export
{
    public $_filename = '';
    public function __construct()
    {
        // do anything that you want to do
    }

    /**
     * [exportFile description]
     * @param  [type] $post_id       ID of post
     * @param  [type] $exports       an array data to insert to csv file
     * @param  [type] $fileExtension [description]
     * @return [type]                [description]
     */
    public function exportFile($name, $exports, $exports_second = [], $fileExtension)
    {
        $year       = current_time('Y');
        $month      = current_time('m');
        $day        = current_time('d');
        $h_i_s_time = current_time('his');

        $this->_filename = snake_case(str_slug($name)) . "_{$year}{$month}{$day}_{$h_i_s_time}.{$fileExtension}";

        switch ($fileExtension) {
            case 'csv':
                $path = $this->_exportCSV($exports, $exports_second);
                break;
            case 'xlsx':
                $path = $this->_exportXLSX($exports, $exports_second);
                break;
        }
        return $path;
    }

    private function _exportCSV($exports, $exports_second)
    {
        foreach ($exports as $key => $export) {
            $exports[$key] = (array) $export;
        }
        if (empty($exports)) {
            throw new BadRequestHttpException("Data empty", null, 1);
        }
        $year        = current_time('Y');
        $month       = current_time('m');
        $path_upload = wp_upload_dir($year . '/' . $month);
        $path_export = $path_upload['path'] . '/export';
        if (!is_dir($path_export)) {
            mkdir($path_export, 0755);
        }
        $link_file = site_url('wp-content/uploads/' . $year . '/' . $month . '/export/' . ($this->_filename));
        $path_file = $path_export . "/{$this->_filename}";
        $success   = Storage::write($file_path, '');

        $file = fopen($path_file, "w");
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($file, array_keys($exports[0]));
        foreach ($exports as $export) {
            fputcsv($file, array_map(function ($value) {
                return trim($value);
            }, $export), $delimiter = ',', $enclosure = '"');
        }
        // for exports second
        if (!empty($exports_second)) {
            foreach ($exports_second as $key => $export) {
                $exports_second[$key] = (array) $export;
            }
            if (empty($exports_second)) {
                throw new BadRequestHttpException("Data empty", null, 1);
            }
            fputcsv($file, array_keys($exports_second[0]), $delimiter = ',', $enclosure = '"');
            foreach ($exports_second as $export) {
                fputcsv($file, array_map(function ($value) {
                    return trim($value);
                }, $export), $delimiter = ',', $enclosure = '"');
            }
        }

        fclose($file);

        $url = $path_file;
        // $this->download($this->_filename, $link_file);
        // // var_dump($link_file);
        // // die;
        // // header('Location: ' . $link_file);
        // // wp_redirect($link_file, 200);
        // // exit;
        return $link_file;
    }

    private function _exportXLSX($exports)
    {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator($this->_creator);

        foreach ($exports as $key => $export) {
            $exports[$key] = (array) $export;
        }
        $columns = [];

        foreach (array_keys($exports[0]) as $key => $value) {
            $columns[$value] = $value;
        }
        $objPHPExcel->getActiveSheet()->fromArray($columns, null, 'A1');
        $objPHPExcel->getActiveSheet()->fromArray($exports, null, 'A2');

        $time      = new DateTime;
        $year      = $time->format('Y');
        $month     = $time->format('m');
        $path      = "exports/{$year}/{$month}/" . md5(time()) . "_{$this->_filename}";
        $success   = Storage::disk('local')->put($path, '');
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($path);

        $url = url($path);
        return $url;
    }

    public function download($file_name, $file_url) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'. $file_name .'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_url));
        readfile($file_url);
        exit;
    }
}
