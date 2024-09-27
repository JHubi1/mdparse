<?php
require("./vendor/autoload.php");
use Erusev\Parsedown\Parsedown;

$file = "guideline.md";

$content_raw = file_get_contents($file);
if ($content_raw == "") {
    $content_raw = "> *Source file empty or non existent*";
}

$Parsedown = new Parsedown();
$content = $Parsedown->toHtml($content_raw);
$content = str_replace("<a href=", "<a target='_blank' href=", $content);
$content = str_replace("<a target='_blank' href=\"#", "<a href=\"#", $content);
$content = str_replace('class="footnote-backref">&#8617;</a>', 'class="footnote-backref" style="font-family: sans-serif; font-weight: bold">&#8617;</a>', $content);

$content = preg_replace_callback('/<h([1-6])>(.*?)<\/h[1-6]>/s', function ($matches) {
    return "<h" . $matches[1] . " id='" . strtolower(preg_replace('/[^A-Za-z0-9\-_]+/', "-", $matches[2])) . "'>" . $matches[2] . "</h" . $matches[1] . ">";
}, $content);

$content = preg_replace_callback('/<code>#([a-zA-Z0-9]{6}|[a-zA-Z0-9]{3})<\/code>/', function ($matches) {
    return "<code>#" . $matches[1] . "<span class='color' style='background-color: #" . $matches[1] . "'></span></code>";
}, $content);

$lang = LanguageDetector\LanguageDetector::detect(preg_replace('/<.*?>/s', "", $content));

$c = "<!DOCTYPE html><html lang='$lang'><head>";
$title = "";
foreach(preg_split("/((\r?\n)|(\r\n?))/", $content_raw) as $line){
    if (str_starts_with($line, "# ")) {
        $c .= "<title>" . trim(str_replace("#", "", $line)) . "</title>";
        $title = trim(str_replace("#", "", $line));
        break;
    }
}

$description = "";
$description_list = explode(" ", trim(str_replace("\n", " ", str_replace($title, "", preg_replace('/<.*?>/s', "", $content)))));
while (strlen($description) <= 120 && $description_list != array()) {
    $description .= array_shift($description_list) . " ";
}
if($description_list != array()) {
    $description .= "...";
}
$description = trim($description);

$c .= '<link rel="stylesheet" href="https://unpkg.com/@highlightjs/cdn-assets@11.9.0/styles/github.min.css" id="hljsTheme"><script src="https://unpkg.com/@highlightjs/cdn-assets@11.9.0/highlight.min.js"></script>';

$c .= "<style>";

$c .= "html {scroll-behavior: smooth}";
$c .= "body {font-family: -apple-system,BlinkMacSystemFont,'Segoe UI','Noto Sans',Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji'; width: 100%; overflow-x: hidden; font-size: 16px; line-height: 1.5}";

// $c .= "img {width: 100%}";

$c .= "code {background-color: #818b981f; border-radius: 6px; padding: .2em .4em; font-size: 85%; white-space: break-spaces}";
$c .= "code span.color {display: inline-block; margin-left: 4px; border-radius: 50%; border-color: #d0d7deb3; border: 1px solid #d0d7deb3; height: 8px; width: 8px}";
$c .= "pre {padding: 16px; overflow: auto; font-size: 85%; line-height: 1.45; background-color: #f6f8fa; border-radius: 6px; font-family: ui-monospace, SFMono-Regular, SF Mono, Menlo, Consolas, Liberation Mono, monospace}";
$c .= "pre code {display: inline; max-width: auto; padding: 0; margin: 0; overflow: visible; line-height: inherit; word-wrap: normal; background-color: transparent; border: 0; color: #1f2328}";
$c .= "pre code.hljs {overflow-x: unset; padding: unset} .hljs {background: unset; color: unset}";

$c .= "table img {max-width: 100%; max-height: 100%}";
$c .= "table {border-spacing: 0; border-collapse: collapse; display: block; width: 100%; max-width: 100%; overflow: auto; & tr {border-top: 1px solid #d7dde3}; & td, th {padding: 6px 13px; border: 1px solid #d7dde3}; tr:nth-child(2n) {background-color: #f6f8fa}}";
$c .= "hr {height: .25em; padding: 0; margin: 1.5rem 0; background-color: #d1d9e0; border: 0}";

$c .= "h1, h2, h3, h4, h5, h6 {border-bottom: 1px solid rgb(225, 228, 232); padding-bottom: .3em; font-weight: 600}";
$c .= ".content {width: 35%; margin: auto; border: 1px solid rgb(225, 228, 232); border-radius: 5px; padding-left: 25px; padding-right: 25px; padding-top: 5px; padding-bottom: 5px; margin-top: 30px; margin-bottom: 30px; overflow-x: hidden}";
$c .= "a {color: #0969da; text-decoration: none}";
$c .= "blockquote {margin: 0; padding: 0 1em; color: #57606a; border-left: .25em solid #d0d7de}";

$c .= "@media only screen and (max-width: 1600px) {.content {width: 45%}} @media only screen and (max-width: 900px) {.content {width: calc(100% - 55px); margin: 0; border: 0}}";
$c .= "@media (prefers-color-scheme: dark) {:root {color-scheme: dark} body {background-color: rgb(13, 17, 23); color: #c9d1d9} code {background-color: #656c7633} pre{background-color: #151b23; & code {color: #f0f6fc}} .content {border-color: rgb(48, 54, 61)} h1, h2, h3, h4, h5, h6 {border-bottom-color: rgb(48, 54, 61)} table {border-spacing: 0; border-collapse: collapse; display: block; width: max-content; max-width: 100%; overflow: auto; & tr {background-color: #0d1117; border-top: 1px solid #21262d}; & td, th {padding: 6px 13px; border: 1px solid #30363d}; tr:nth-child(2n) {background-color: #161b22}} hr {background-color: #3d444d} a {color: #58a6ff} blockquote {border-color: #30363d; color: #848d97}}";

$c .= "</style>";
$c .= "<meta name='viewport' content='width=device-width'>";
$c .= "<meta name='description' content='$description'>";

$c .= "</head><body>";

$c .= "<div class='content'>";
$c .= $content;
$c .= "</div>\n<script>function c(d){e=document.getElementById('hljsTheme');if(d){e.href='https://unpkg.com/@highlightjs/cdn-assets@11.9.0/styles/github-dark.min.css'}else{e.href='https://unpkg.com/@highlightjs/cdn-assets@11.9.0/styles/github.min.css'}}if(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches){c(true)}window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {c(event.matches)})</script>";
$c .= "<script>hljs.highlightAll()</script></body></html>";

echo $c;
