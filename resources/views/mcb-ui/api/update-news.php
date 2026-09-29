<?php

date_default_timezone_set('Asia/Jakarta');

$rssFeeds = [
    [
        "url" => "https://www.antaranews.com/rss/terkini.xml",
        "source" => "ANTARA"
    ]
];

$keywords = [
    "infrastruktur",
    "telekomunikasi",
    "fiber",
    "internet",
    "jalan",
    "tol",
    "jembatan",
    "pln",
    "telkom",
    "tower",
    "bts",
    "smart city",
    "digital",
    "konstruksi",
    "bandwidth",
    "jaringan"
];

$defaultImage = "/img/blog-default.jpg";

$news = [];

function getImage(SimpleXMLElement $item, string $defaultImage): string
{
    if (isset($item->enclosure)) {

        $url = (string) $item->enclosure['url'];

        if (!empty($url)) {
            return $url;
        }
    }

    $media = $item->children('media', true);

    if ($media) {

        if (isset($media->content)) {

            foreach ($media->content as $content) {

                $attr = $content->attributes();

                if (!empty($attr['url'])) {
                    return (string) $attr['url'];
                }
            }
        }

        if (isset($media->thumbnail)) {

            foreach ($media->thumbnail as $thumb) {

                $attr = $thumb->attributes();

                if (!empty($attr['url'])) {
                    return (string) $attr['url'];
                }
            }
        }
    }

    return $defaultImage;
}

function getCategory(string $title): string
{
    $title = strtolower($title);

    if (str_contains($title, "pln")) {
        return "Electrical";
    }

    if (
        str_contains($title, "fiber") ||
        str_contains($title, "internet") ||
        str_contains($title, "bts") ||
        str_contains($title, "telkom") ||
        str_contains($title, "telekomunikasi")
    ) {
        return "Telecommunication";
    }

    if (
        str_contains($title, "jalan") ||
        str_contains($title, "tol") ||
        str_contains($title, "jembatan") ||
        str_contains($title, "konstruksi")
    ) {
        return "Infrastructure";
    }

    if (str_contains($title, "smart city")) {
        return "Smart City";
    }

    return "News";
}

foreach ($rssFeeds as $feed) {

    $context = stream_context_create([
        'http' => [
            'timeout' => 15,
            'user_agent' => 'Mozilla/5.0'
        ]
    ]);

    $xmlContent = @file_get_contents(
        $feed["url"],
        false,
        $context
    );

    if ($xmlContent === false) {
        continue;
    }

    $xml = @simplexml_load_string($xmlContent);

    if (!$xml) {
        continue;
    }

    if (!isset($xml->channel->item)) {
        continue;
    }

    foreach ($xml->channel->item as $item) {

        $title = trim((string) $item->title);

        $description = trim(
            strip_tags((string) $item->description)
        );

        $text = strtolower(
            $title . " " . $description
        );

        $found = false;

        foreach ($keywords as $keyword) {

            if (
                strpos(
                    $text,
                    strtolower($keyword)
                ) !== false
            ) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            continue;
        }

        $pubDate = strtotime(
            (string) $item->pubDate
        );

        $news[] = [

            "title" => $title,

            "description" => mb_substr(
                $description,
                0,
                180
            ) . "...",

            "image" => getImage(
                $item,
                $defaultImage
            ),

            "date" => $pubDate
                ? date("Y-m-d H:i:s", $pubDate)
                : date("Y-m-d H:i:s"),

            "category" => getCategory($title),

            "url" => (string) $item->link,

            "source" => $feed["source"]
        ];
    }
}

$temp = [];
$result = [];

foreach ($news as $item) {

    if (empty($item["url"])) {
        continue;
    }

    if (!isset($temp[$item["url"]])) {

        $temp[$item["url"]] = true;

        $result[] = $item;
    }
}

usort($result, function ($a, $b) {

    return strtotime($b["date"])
        <=> strtotime($a["date"]);

});

$result = array_slice($result, 0, 20);

$cacheDirectory = __DIR__ . "/cache";

if (!is_dir($cacheDirectory)) {

    mkdir(
        $cacheDirectory,
        0755,
        true
    );
}

file_put_contents(
    $cacheDirectory . "/news.json",
    json_encode(
        $result,
        JSON_PRETTY_PRINT |
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    )
);

echo "Berhasil update " .
    count($result) .
    " berita.";