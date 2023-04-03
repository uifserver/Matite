<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1');

$dir = 'data_full'; // indirilen dosyaların kaydedileceği dizin
$fileUrl = 'https://matrp.my-fastdl.com/jmobile_cache/mobile/cache/full_list.json'; // dosyaların listesi

// dosyaların listesini indir
$list = json_decode(file_get_contents($fileUrl), true);

// dosyaları indir
foreach ($list['files'] as $file) {
    $url = $file['url'];
    $path = $dir . '/' . $file['path'];
    $dirPath = dirname($path);

    if (!file_exists($dirPath)) {
        mkdir($dirPath, 0777, true);
    }

    if (file_exists($path) && filesize($path) == $file['size']) {
        echo $path . " already exists.\n";
    } else {
        echo "Downloading " . $path . "\n";
        $data = file_get_contents($url);
        file_put_contents($path, $data);
    }
}

// dosyaları zip dosyası haline getir
$zip = new ZipArchive();
$zipName = 'sanandreas.zip';

if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($dir) + 1);

            $zip->addFile($filePath, $relativePath);
        }
    }

    $zip->close();

    echo "Files successfully compressed to " . $zipName . "\n";
} else {
    echo "Error creating zip file!\n";
}
?>
