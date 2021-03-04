<?php
try {
    function disableDarkReader()
    {
        // Implement when this actually works: https://github.com/darkreader/darkreader/pull/4334
    }

    function sanitize_output($buffer)
    {

        $search = array(
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        );

        $replace = array(
            '>',
            '<',
            '\\1',
            ''
        );

        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }

    ob_start("sanitize_output");


    /**
     * @param $id
     * @param $avatar
     * @param int $size
     * @return string
     */
    function generateAvatarURL($id, $avatar, $size = 128)
    {
        if ($avatar == null) {
            $array = [
                "https://discordapp.com/assets/322c936a8c8be1b803cd94861bdfa868.png",
                "https://discordapp.com/assets/dd4dbc0016779df1378e7812eabaa04d.png",
                "https://discordapp.com/assets/6debd47ed13483642cf09e832ed0bc1b.png",
                "https://discordapp.com/assets/1cbd08c76f8af6dddce02c5138971129.png",
                "https://discordapp.com/assets/0e291f67c9274a1abdddeb3fd919cbaa.png",
            ];
            return $array[array_rand($array, 1)];
        }
        $ext = "png";
        if (substr($avatar, 0, 2) == "a_") {
            $ext = "gif";
        }
        return sprintf("https://cdn.discordapp.com/avatars/%s/%s.%s?size=%d", $id, $avatar, $ext, $size);
    }

    function generateDiscordIDprefill($id)
    {
        return "https://discord.id/?prefill=" . $id;
    }

    function generateGoogleLookup($tag)
    {
        return sprintf("https://www.google.com/search?q=%s", urlencode("\"" . $tag . "\""));
    }

    function he($d)
    {
        return htmlentities($d);
    }

    function relation($type)
    {
        if ($type === 1) {
            $r = "Friend";
        } else if ($type === 2) {
            $r = "Blocked";
        } else if ($type === 3) {
            $r = "Incoming friend request";
        } else if ($type === 4) {
            $r = "Sent friend request";
        } else $r = "unknown";
        return $r;
    }

    if (isset($_FILES["f"])) {
        $filename = $_FILES["f"]["tmp_name"];
        if (file_exists($filename)) {
            $raw_contents = file_get_contents($filename);
            $parsed_contents = json_decode($raw_contents);
//        print_r($parsed_contents);
            echo "<!doctype html>
<html lang=\"en\">
<head>";
            disableDarkReader();
            echo "
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\"
          content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">\n";
            echo "<style>" . file_get_contents("style.css") . "</style>";
            if ($_POST["type"] === "2col") {
                $c = 2;
                echo "<style>body {
    display: grid;
    grid: \"" . trim(str_repeat(". ", $c)) . "\";
}</style>";
            } else if ($_POST["type"] === "3col") {
                $c = 3;
                echo "<style>body {
    display: grid;
    grid: \"" . trim(str_repeat(". ", $c)) . "\";
}</style>";
            }
            echo "<script>" . file_get_contents("intersection-observer.min.js") . "\n\n" . file_get_contents("lazyload.min.js") . "\n\n" . file_get_contents("other.js") . "</script>";
            echo "
</head>
<body><button class='save' style='grid-column: 1 / -1; font-size: 24px; display: none;'>save</button>";
            usort($parsed_contents, function ($a, $b) {
                return strcmp($a->user->username, $b->user->username);
            });
            foreach ($parsed_contents as $content) {
                $user = $content->user;
                $userTag = $user->username . "#" . $user->discriminator;
                $avatarSize = 128;
                echo "\n" . "<div class='g'>";
                echo sprintf("\n<div class='gAvatar' id='%s'><a href='#%s'>", $user->id, $user->id);
                echo sprintf("<img src=\"%s\" data-src=\"%s\" class='lazy u-img' width='%d' height='%d' alt='avatar'>", generateAvatarURL(null, null, $avatarSize), generateAvatarURL($user->id, $user->avatar, $avatarSize), $avatarSize, $avatarSize);
                echo "\n" . "</a></div>";
                echo "\n" . "<div class='gData'>";
                echo sprintf("\n<h1 class='u-tag'>UserTag: <span>%s</span> <a target='_blank' href=\"%s\">🔍</a></h1>", he($userTag), generateGoogleLookup($userTag));
                echo sprintf("\n<h1 class='u-id'>ID: <span>%s</span> <a target='_blank' href=\"%s\">🔍</a></h1>", he($user->id), generateDiscordIDprefill($user->id));
                echo sprintf("\n<h1 class='u-relation' data-type='%s'>Relation: <span>%s</span></h1>", $content->type, relation($content->type));
                echo "\n" . "</div>";
                echo "\n" . "</div>";
            }
            echo "<script>lazyLoadInstance.update();</script>";
            echo "<script>var savebtn = document.querySelector('.save');if(location.host!=='') {" . file_get_contents("download.min.js") . ";savebtn.onclick=function(){download(document.documentElement.outerHTML, 'dlFriendsList.html', 'text/html');};savebtn.style.display='';}else {savebtn.remove();}</script>";
            echo "
</body>
</html>";
            unlink($filename);
        } else {
            die("Temporary upload file doesn't exist?! | " . $filename);
        }
    } else {
        ?>
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport"
                  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Discord Friends List Visualizer</title>
            <?php
            disableDarkReader();
            echo "<style> body { background-color: black!important; color: white!important; } * {color:white!important;} button,select,option {color: black!important;} h5 {margin-block-start: 0px;margin-block-end: 0px;margin-inline-start: 0px;margin-inline-end: 0px;}</style>";
            ?>
        </head>
        <body>
        <?php
        echo '<h1>Upload a file containing the results of https://discord.com/api/v8/users/@me/relationships <a href="https://luna.gitlab.io/discord-unofficial-docs/relationships.html"><small>?</small></a></h1>';
        echo '<h3>If you desire to use the source instead of this live version:</h3>';
        echo '<ul>';
        echo "<li><a href='source.php'>index.php</a></li>";
        $filesNeeded = [
            "style.css",
            "placeholder-avatar.png",
            "intersection-observer.min.js",
            "lazyload.min.js",
            "other.js",
        ];
        foreach ($filesNeeded as $item) {
            echo sprintf("<li><a href=\"./%s\">%s</a></li>", urlencode($item), he($item));
        }
        echo '</ul>';
        echo '<form method="post" enctype="multipart/form-data"><input type="file" name="f" required><select name="type"><option selected disabled>Choose a style</option><option value="default">Default</option><option value="2col">2 Col</option><option value="3col">3 Col</option></select><button type="submit">Go</button></form>';
        echo "<h5>Here's a <a href='./example.json'>example.json</a> if you're just curious!</h5>";
        echo "<h5>Privacy Notice: We do not keep your files. In fact they're deleted immediately via <a href='https://www.php.net/manual/en/function.unlink.php'>unlink</a> as a double measure <a href='https://www.php.net/manual/en/features.file-upload.post-method.php'>regardless of what documentation says</a>, we wanted to make sure any and all individuals would have little to no reason to worry.</h5>";
        echo "</body></html>";
    }
} catch (Exception $e) {
    die("Error occurred!");
}