<?php

declare(strict_types=1);

/**
 * @var string $error_type
 * @var string $error_file
 * @var string $error_line
 * @var string $error_level
 * @var string $error_desc
 * @var string $error_trace_smg
 * @var string $source
 * @var array $error_array
 *
 */

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
        echo $error_array['class']; ?> | Error Control Software</title>
    <style>
        @import 'https://fonts.googleapis.com/css?family=Inconsolata';

        html {
            min-height: 100%;
        }

        body {
            margin: 0;
            box-sizing: border-box;
            height: 100%;
            background-color: #000000;
            background-image: radial-gradient(#11581E, #041607), url("https://media.giphy.com/media/oEI9uBYSzLpBK/giphy.gif");
            background-repeat: no-repeat;
            background-size: cover;
            font-family: 'Inconsolata', Helvetica, sans-serif;
            font-size: 1.5rem;
            color: rgba(128, 255, 128, 0.8);
            text-shadow: 0 0 1ex rgba(51, 255, 51, 1),
            0 0 2px rgba(255, 255, 255, 0.8);
        }

        .noise {
            pointer-events: none;
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url("https://media.giphy.com/media/oEI9uBYSzLpBK/giphy.gif");
            background-repeat: no-repeat;
            background-size: cover;
            z-index: -1;
            opacity: .02;
        }

        .overlay {
            pointer-events: none;
            position: absolute;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                    180deg,
                    rgba(0, 0, 0, 0) 0,
                    rgba(0, 0, 0, 0.3) 50%,
                    rgba(0, 0, 0, 0) 100%);
            background-size: auto 4px;
            z-index: 1;
        }

        .overlay::before {
            content: "";
            pointer-events: none;
            position: absolute;
            display: block;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(
                    0deg,
                    transparent 0%,
                    rgba(32, 128, 32, 0.2) 2%,
                    rgba(32, 128, 32, 0.8) 3%,
                    rgba(32, 128, 32, 0.2) 3%,
                    transparent 100%);
            background-repeat: no-repeat;
            animation: scan 7.5s linear 0s infinite;
        }

        @keyframes scan {
            0% {
                background-position: 0 -100vh;
            }
            35%, 100% {
                background-position: 0 100vh;
            }
        }

        .terminal {
            box-sizing: inherit;
            position: absolute;
            height: 100%;
            width: 1000px;
            max-width: 100%;
            padding: 4rem;
            /*text-transform: uppercase;*/
        }

        .output {
            color: rgba(128, 255, 128, 0.8);
            text-shadow: 0 0 1px rgba(51, 255, 51, 0.4),
            0 0 2px rgba(255, 255, 255, 0.8);
        }

        .output::before {
            content: "> ";
        }

        a {
            color: #fff;
            text-decoration: none;
        }

        a::before {
            content: "[";
        }

        a::after {
            content: "]";
        }

        .error_code {
            color: white;
        }
    </style>
</head>

<body>
<div class="noise"></div>
<div class="overlay"></div>
<div class="terminal">
    <h1><span class="error_code"><?php
            echo $error_array['class']; ?></span></h1>
    <p class="output"><b>File:</b> <?php
        echo $error_array['file'] ?></p>
    <p class="output"><b>Line:</b> <?php
        echo $error_array['line']; ?> <b>Level:</b> <?php
        echo $error_array['type']; ?></p>
    <p class="output"><b>Description:</b> <?php
        echo $error_array['description']; ?></p>
    <p class="output"><b>BackTrace Log:</b><br><?php
        echo $error_array['trace_msg']; ?></p>
    <p class="output"><b>Code:</b><br><br><?php
        echo $source; ?></p>
    <p class="output">Please try to <a href="#" id="return">go back</a>.</p>
</div>
<!-- Return Home Script -->
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        function refresh() {
            document.location.href = "/";
        }

        document.getElementById("return").addEventListener("click", function () {
            refresh();
        });
    });
</script>
</body>

</html>