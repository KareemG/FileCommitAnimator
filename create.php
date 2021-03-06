<?php
// Version 1.0

use FileCommitAnimator\GithubRepoExtractor;
use FileCommitAnimator\ScreenshotCreator;
use GifCreator\AnimGif;

require 'vendor/autoload.php';

function readline($prompt = null){
    if($prompt){
        echo $prompt;
    }
    $fp = fopen("php://stdin","r");
    $line = rtrim(fgets($fp, 1024));
    return $line;
}

if (count($argv) != 3) {
    exit("Invalid arguments. ");
}

$credentials = base64_encode($argv[1] . ":" . $argv[2]);

echo "--File Details--\n";
$name = readline("Repository Owner Username: ");
$repo = readline("Repository Name: ");
$filePath = readline("File Path: ");
echo "\n";

echo "--Gif Configuration--\n";
$width = readline("Width (px): ");
while (!ctype_digit($width)) {
    $width = readline("\rPlease enter an integer! Width (px): ");
}
$width = intval($width);

$height = readline("Height (px): ");
while (!ctype_digit($height)) {
    $height = readline("\rPlease enter an integer! Height (px): ");
}
$height = intval($height);

$frameRate = readline("Frame rate (per second): ");
while (!ctype_digit($frameRate)) {
    $frameRate = readline("\rPlease enter an integer! Frame rate (per second): ");
}
$frameRate = intval($frameRate);

echo "\n";

$extractor = new GithubRepoExtractor($name, $repo, $credentials);
$ssCreator = new ScreenshotCreator(dirname(__FILE__) . '/bin/phantomjs.exe');

echo "Retrieving commits... ";
try {
    $commits = $extractor->getCommits();
} catch (Exception $e) {
    exit("\nError: " .  $e->getMessage() . " \n");
}
echo "done. \n";

if (!file_exists('images\\')) {
    mkdir('images');
} else {
    array_map('unlink', glob("images\*") ?: []);
}

$frames = array();
$durations = array();
$counter = 1;
$numOfCommits = count($commits);

foreach ($commits as $commit) {
    $htmlFile = fopen("images\\frame" . $counter . ".html", "w") or exit("Unable to write file: images\\frame-" . $counter . ".html");
    $content = "<!DOCTYPE html><html><body style='width:100%;height:100%;background-color:white;'>" .
                "<div style='font-family:Segoe UI;color:blue;font-size:50px;position:absolute;top:0;left:15px;'>" .
                $counter . "</div><div style='display:flex;align-items:center;justify-content:center;'><pre>";
    
    try {
        $content .= $extractor->getFileAtCommit($filePath, $commit);
    } catch (Exception $e) { }
    
    $content .= "</pre></div></body></html>";
    fwrite($htmlFile, $content);

    $htmlPath = "file:///" .  str_replace('\\', '/', dirname(__FILE__)) . "/images/frame" . $counter . ".html";
    $imgPath = "./images/frame". $counter . ".png";

    $frames[$counter-1] = $imgPath;
    $durations[$counter-1] = 100/$frameRate;

    $ssCreator->createScreenshot($htmlPath, $width, $height, $imgPath);

    echo "\rProgress: " .  $counter . "/" . $numOfCommits . " frames completed. ";

    fclose($htmlFile);
    $counter += 1;
}

echo "\n";
echo "Creating gif... ";
$anim = new GifCreator\AnimGif();
$anim->create($frames, $durations);

if (!file_exists('gifs\\')) {
    mkdir('gifs');
}

$anim->save("./gifs/" . date("Y-m-d h-i-sa") . ".gif");
echo "gifs/" . date("Y-m-d h-i-sa") . ".gif " . "created. \n";
