<?php

namespace DevTools\Cli\Helper;

class Data
{
    /**
     * Converts the given string to camel case format
     */
    public function camelCase($str, $lowerFirst = true, array $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);

        if ($lowerFirst) {
            $str = lcfirst($str);
        }

        return $str;
    }

    /**
     * Converts the given string to title case format
     */
    public function titleCase($str, $flag)
    {
        $pattern = '/(.*?[a-z]{1})([A-Z]{1}.*?)/';
        $replace = '${1} ${2}';
        $str = preg_replace($pattern, $replace, $this->camelCase($str, $flag));

        return $str;
    }

    /**
     * From the given Mysql data type, return the appropriate data type for PHP docblocks
     */
    public function getParamTypeFromDataType($dataType)
    {
        $paramType = 'string';

        switch ($dataType) {
            case 'blob':
            case 'text':
            case 'mediumtext':
            case 'longtext':
            case 'smalltext':
            case 'varchar':
            case 'date':
            case 'datetime':
            case 'timestamp':
                $paramType = 'string';
                break;

            case 'boolean':
                $paramType = 'boolean';
                break;

            case 'smallint':
            case 'int':
            case 'bigint':
            case 'numeric':
                $paramType = 'integer';
                break;

            case 'decimal':
            case 'float':
                $paramType = 'float';
                break;

            default:
                $paramType = 'string';
                break;
        }

        return $paramType;
    }
}
