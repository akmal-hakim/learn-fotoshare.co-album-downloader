<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="https://cdn.rawgit.com/yegor256/tacit/gh-pages/tacit-css-1.3.2.min.css"/>
    <title>fotoshare.co - Album Downloader</title>
</head>
<body>
    <section>
        <header>
            <h1>fotoshare.co - Album Downloader</h1>
        </header>
        <article>
            <form action="index.php">
                <fieldset>
                    <p>You can download all images of an fotoshare.co Album. Existing files will be skipped. A directory will be created named after you album name.</p>
                    <p><strong>For larger albums, use CLI mode.</strong></p>
                    <label for="url">URL to fotoshare.co Album</label>
                    <input type="url" name="url" id="url" required title="Valid fotoshare.co link, like https://fotoshare.co/u/123456789/event">
                    <input type="submit" value="Download">
                </fieldset>
            </form>
        </article>
    </section>
</body>
    
<?php
// Creating a variable with an URL
// to be checked
$url = 'https://fotoshare.co/u/kotakphotobooth/3492386';
  
// Getting page header data
$array = @get_headers($url);
  
// Storing value at 1st position because
// that is only what we need to check
$string = $array[0];
  
// 404 for error, 200 for no error
if(strpos($string, "200")) {
    echo 'Specified URL Exists';
} 
else {
    echo 'Specified URL does not exist';
}
?>
  
</html>
