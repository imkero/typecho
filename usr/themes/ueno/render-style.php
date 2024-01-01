<?php
header('Content-Type: text/css; charset=utf-8');
/*
css file list
@import "./vars.css";
@import "./layout.css";
@import "./list-item.css";
@import "./comments.css";
@import "./entry/layout.css";
@import "./entry/tags.css";
@import "./entry/license.css";
@import "./entry/page-nav.css";
*/
$styles = [
  'typlog.css',
  'yue.css',
  'vars.css',
  'layout.css',
  'list-item.css',
  'comments.css',
  'entry/layout.css',
  'entry/tags.css',
  'entry/license.css',
  'entry/page-nav.css',
];

foreach ($styles as $style) {
  echo file_get_contents(__DIR__ . '/css/' . $style);
}
