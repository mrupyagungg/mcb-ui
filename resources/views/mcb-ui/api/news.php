<?php

header("Content-Type: application/json; charset=UTF-8");

$file = __DIR__ . "/cache/news.json";

if (!file_exists($file)) {

    echo json_encode([]);

    exit;
}

$content = file_get_contents($file);

if ($content === false || empty($content)) {

    echo json_encode([]);

    exit;
}

echo $content;