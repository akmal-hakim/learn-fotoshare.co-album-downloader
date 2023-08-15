<?php
require 'vendor/autoload.php';
use Goutte\Client;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;

if (isset($_GET['url']) || !empty($argv[1])) {
    $url = isset($_GET['url']) ? $_GET['url'] : trim($argv[1]);
    echo $url; 
    // $_GET['url'] : trim($argv[1]) basically takes the value of url 
    //echo $url will echo the link given if https://fotoshare.co then will echo https://fotoshare.co 
   
   $folder = __DIR__ . DIRECTORY_SEPARATOR . basename($url); // !Important if URL is so weird which contains character that is restricted for folder name, then it will return errors.
   echo "<br>" .  $folder;
   echo "<br>";
   // $folder --> C:\Users\Akmal Hakim\Desktop\New folder\fotoshare.co (The respective link)    
   // The __DIR__ can be used to obtain the current code working directory.   
   /* 
      Directory seperator basically: if not use will result to this 
      C:\Users\Akmal Hakim\Desktop\New folderfotoshare.co

   */
   /*
      Basename will Returns the base name of the given path.

      echo "1) ".basename("/etc/sudoers.d").PHP_EOL;
      echo "2) ".basename("/etc/passwd").PHP_EOL;
      1) sudoers.d
      2) passwd

   */

    if (!is_dir($folder)) {
        $dir = mkdir($folder, 0755); //This will create a folder and at the same time pass value 1 to $dir
        if ($dir === false) {
            exit('Error: You need to set the permission correctly!');
        }   
    }

    // The is_dir() function checks whether the specified filename is a directory. 
    // mkdir() sets the access, change, modification, and creation times for the new directory.
    /*
        Source : https://www.edureka.co/community/33909/what-does-file-permission-755-mean

        755 means read and execute access for everyone and also write access for the owner of the file.
        File permission 755 means that the directory has the default permissions -rwxr-xr-x (represented in octal notation as 0755).

        7=rwx 5=r-x 5=r-x

    */
    /*
        mkdir(), make directory sets the access, change, modification, and creation times for the new directory.
        Even if your write echo mkdir($folder) it will create a folder. and also output 1 because if success create folder

    */

    $data = [];
    $client = new Client();
    $crawler = $client->request('GET', $url);
    $crawler->filter('[data-img][data-url]')->each(function ($node) {
        global $data;
        $data[] = $node->extract(['data-img', 'data-url', 'data-thumb', 'data-width', 'data-height', 'data-type'])[0];
    });
    // This one use Goutte\Client;
    // data-thumb is data thumbnail


 

    $files_count = count($data);
    if (php_sapi_name() == 'cli') { echo "$files_count files found." . PHP_EOL; }

    try {
        $writer = Writer::createFromPath($folder . DIRECTORY_SEPARATOR . 'images.csv', 'w+');
        
        $writer->insertOne(['Image URL', 'GIF/Thumbnail', 'Fotoshare.co Path', 'Width', 'Height', 'Type','test']);
        //Writing 'test' or anything will just create another column 
        
        $writer->insertAll($data);

    } catch (CannotInsertRecord $e) {
        echo $e->getMessage();
    }
  
    // This is to create csv file
    // And also insert data to csv
    // It insert data very fast
    //---------------------------------------------------------
    //---------------------------------------------------------

    
   foreach ($data as $key => $column) {
        $index                 = ++$key;
        $download              = [];
        $download['file'] = $column[0];
        // If written $column[1] then it will download the thumbnail name only because we did not set the thumbnail url.
        
        $download['gif']  = ($column[5] == 'mp4') ? $column[2] : false;
        //

        foreach ($download as $link) {
            $path = $folder . DIRECTORY_SEPARATOR . basename($link);
            if ($link === false) { continue; }
            if (!file_exists($path)) {
                file_put_contents($path, file_get_contents($link));
                if (php_sapi_name() == 'cli') { echo "($index/$files_count) Downloaded: " . basename($link) . PHP_EOL; }
            } else {
                if (php_sapi_name() == 'cli') { echo "($index/$files_count) File skipped: " . basename($link) . PHP_EOL; }
            }
        }
        // This code will download the file

    }
    
    /*
    -----------------------------------------------------------------
        
        foreach ($data as $key => $row) {
            .
            .
            .
            .
            .
            .
        }

        // If the code is REMOVED, then it will not download the picture or video 

    -----------------------------------------------------------------
    */

}

if (php_sapi_name() !== 'cli') {
    require __DIR__ . DIRECTORY_SEPARATOR . 'view.php';
}



/*

Original Code : 

if (isset($_GET['url']) || !empty($argv[1])) {
    $url = isset($_GET['url']) ? $_GET['url'] : trim($argv[1]);

    $folder = __DIR__ . DIRECTORY_SEPARATOR . basename($url);

    if (!is_dir($folder)) {
        $dir = mkdir($folder, 0755);
        if ($dir === false) {
            exit('Error: You need to set the permission correctly!');
        }
    }
    $data = [];
    $client = new Client();
    $crawler = $client->request('GET', $url);
    $crawler->filter('[data-img][data-url]')->each(function ($node) {
        global $data;
        $data[] = $node->extract(['data-img', 'data-url', 'data-thumb', 'data-width', 'data-height', 'data-type'])[0];
    });
    
    $files_count = count($data);
    if (php_sapi_name() == 'cli') { echo "$files_count files found." . PHP_EOL; }

    try {
        $writer = Writer::createFromPath($folder . DIRECTORY_SEPARATOR . 'images.csv', 'w+');
        $writer->insertOne(['Image URL', 'GIF/Thumbnail', 'Fotoshare.co Path', 'Width', 'Height', 'Type']);
        $writer->insertAll($data);
    } catch (CannotInsertRecord $e) {
        echo $e->getMessage();
    }
    foreach ($data as $key => $row) {
        $index                 = ++$key;
        $download              = [];
        $download['file'] = $row[0];
        $download['gif']  = ($row[5] == 'mp4') ? $row[2] : false;
        foreach ($download as $link) {
            $path = $folder . DIRECTORY_SEPARATOR . basename($link);
            if ($link === false) { continue; }
            if (!file_exists($path)) {
                file_put_contents($path, file_get_contents($link));
                if (php_sapi_name() == 'cli') { echo "($index/$files_count) Downloaded: " . basename($link) . PHP_EOL; }
            } else {
                if (php_sapi_name() == 'cli') { echo "($index/$files_count) File skipped: " . basename($link) . PHP_EOL; }
            }
        }
    }
}

if (php_sapi_name() !== 'cli') {
    require __DIR__ . DIRECTORY_SEPARATOR . 'view.php';
}
*/
