<?php
    header('HTTP/1.1 503 Service Unavailable');
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
?>

<!doctype html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">

    <title>Update in progress...</title>
</head>


<body style="display:flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh; width: 100%; margin: 0;">
    <div style="display: flex;">
        <img src="https://www.google.pl/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png" style="max-width: 50vw; align-self: center;">
    </div>
    <p style="font-family: 'Open Sans', sans-serif; font-size: 1.25rem; padding: 1.5rem 2rem; text-align: center">We are updating our website... Site will automatically refresh in <span id="sec"></span> seconds.</p>

    <?php
        if( !empty($_POST) ) {
            echo '<form action="POST" id="form">';
            foreach($_POST as $key => $value) {
                if(is_array($value)) {
                    foreach($value as $arrayKey => $arrayValue) echo "<input type=\"hidden\" name=\"{$key[$arrayKey]}\" value=\"$arrayValue\">";
                } else {
                    echo "<input type=\"hidden\" name=\"$key\" value=\"$value\">";
                }
            }
            echo '</form>';
        }
    ?>

    <script>
        window.seconds = 10;

        const handler = () => {
            document.getElementById('sec').innerText = window.seconds;
            window.seconds--;

            if(window.seconds <= 0) {
                const form = document.getElementById('form');
                (form)
                    ? form.submit()
                    : document.location.reload();

                clearInterval(window.interval);
            }
        };
        handler();
        window.interval = setInterval(handler, 1000);
    </script>
</body>
</html>

