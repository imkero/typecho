<?php
/**
 * 动态渲染 CSS
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

header('Content-Type: text/css; charset=utf-8');

echo renderStylesheet();
