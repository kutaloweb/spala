<?php

/**
 * Chmod a file or a directory with given file permissions
 *
 * @param string $path
 * @param int $filePerm
 * @param int $dirPerm
 *
 * @return bool
 */
function recursiveChmod($path, $filePerm = 0644, $dirPerm = 0755)
{
    if (!file_exists($path)) {
        return false;
    }
    if (is_file($path)) {
        chmod($path, $filePerm);
    } elseif (is_dir($path)) {
        $foldersAndFiles = scandir($path);
        $entries = array_slice($foldersAndFiles, 2);
        foreach ($entries as $entry) {
            recursiveChmod($path . "/" . $entry, $filePerm, $dirPerm);
        }
        chmod($path, $dirPerm);
    }

    return true;
}

/**
 * Generate select options for default select box where value is same as text
 *
 * @param array $data
 *
 * @return array
 */
function generateSelectValueOnly($data)
{
    $options = [];
    foreach ($data as $value) {
        $options[] = ['text' => $value, 'value' => $value];
    }

    return $options;
}

/**
 * Generate select options for default select box
 *
 * @param array $data
 *
 * @return array
 */
function generateSelect($data)
{
    $options = [];
    foreach ($data as $key => $value) {
        $options[] = ['text' => $value, 'value' => $key];
    }

    return $options;
}

/**
 * Generate select options for Vue multiselect
 *
 * @param array $data
 *
 * @return array
 */
function generateSelectVue($data)
{
    $options = [];
    foreach ($data as $key => $value) {
        $options[] = ['name' => $value, 'id' => $key];
    }

    return $options;
}

/**
 * Generate translated select options for Vue multiselect
 *
 * @param $data
 * @param string $type
 *
 * @return array
 */
function generateSelectVueTranslated($data, $type = "radio")
{
    $options = [];

    if ($type === 'radio') {
        foreach ($data as $key => $value) {
            $options[] = ['name' => trans('list.' . $value), 'id' => $value];
        }
    } elseif ($type === 'select') {
        foreach ($data as $key => $value) {
            $options[] = ['text' => trans('list.' . $value), 'value' => $value];
        }
    }

    return $options;
}

/**
 * Get server maximum post size
 *
 * @return float|int|string
 */
function getPostMaxSize()
{
    if (is_numeric($postMaxSize = ini_get('post_max_size'))) {
        return (int)$postMaxSize;
    }

    $metric = strtoupper(substr($postMaxSize, -1));
    $postMaxSize = (int)$postMaxSize;

    switch ($metric) {
        case 'K':
            return $postMaxSize * 1024;
        case 'M':
            return $postMaxSize * 1048576;
        case 'G':
            return $postMaxSize * 1073741824;
        default:
            return $postMaxSize;
    }
}

/**
 * Get date with 00:00 time
 *
 * @param $date
 *
 * @return string
 */
function getStartOfDate($date)
{
    return date('Y-m-d', strtotime($date)) . ' 00:00';
}

/**
 * Get date with 23:59 time
 *
 * @param $date
 *
 * @return string
 */
function getEndOfDate($date)
{
    return date('Y-m-d', strtotime($date)) . ' 23:59';
}

/**
 * Get client IP address
 *
 * @return mixed
 */
function getClientIp()
{
    $ips = getRemoteIPAddress();
    $ips = explode(',', $ips);

    return !empty($ips[0]) ? $ips[0] : \Request::getClientIp();
}

/**
 * Get IP address of visitor
 *
 * @return string
 */
function getRemoteIPAddress()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'];
}
