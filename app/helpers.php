<?php
/**
 * 验证字符串是否为合法base64数据
 *
 * @param string $str 字符串数据
 * @return bool
 */
function is_base64($str){
    if (!is_string($str) || @preg_match('/^[0-9]*$/', $str) || @preg_match('/^[a-zA-Z]*$/', $str)) {
        return false;
    } else {
        return $str == base64_encode(base64_decode($str));
    }
}