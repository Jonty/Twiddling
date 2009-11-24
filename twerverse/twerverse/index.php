<?php

    include('twerverse.class.php');

    ?>
        <html>
        <head>
            <title>Twerverse</title>
            <style type="text/css">
                body {
                    font-family: Helvetica, Bitstream Vera Sans, sans-serif;
                    color: #000000;
                    margin-left: 10%;
                    margin-top: 5%;
                }
                .heading {
                    font-size: 3em;
                    font-weight: bold;
                }
                .description {
                    font-size: 1.1em;
                    font-weight: bold;
                }
                .form {
                    margin-top: 3%;
                }
                .sectionheader {
                    margin-top: 3%;
                    font-size: 2em;
                    font-weight: bold;
                }
                .sectionsubheader {
                    font-size: 0.9em;
                }
            </style>
        </head>
        <body>
        <div class='heading'>Twerverse</div>
        <div class='description'>Twerverse shows you who you're following that's not following you, and who's following you that you're not following.<br>
        It's both informative and vaguely upsetting.</div>
    <?php

    $tw = new Twerverse();

    $user = trim($_GET['user']);
    if ($user) {
        list($notFollowingYou, $notFollowingThem) = $tw->getDiffsForUser($user);
        if ($notFollowingYou === false && $notFollowingThem === false) {
            $user = null;
            ?>
                <h3>That twitter user doesn't appear to exist!</h3>
            <?
        }
    }

    if (!$user) {
        ?>
            <div class='form'>
                <b>Twitter Username:</b>
                <form>
                    <input type="textbox" name="user">
                    <input type="submit" value="flip twerverse it">
                </form>
            </div>
        <?php
        exit;
    }

    ?>
        <br>
        <div><small><strong>Summary:</strong> <?=count($notFollowingYou)?> people are not following you, you are not following <?=count($notFollowingThem)?> people.</small></div>
    <?

    ?>
        <div class='sectionheader'>
            <?=count($notFollowingYou)?> people are not following you
        </div>
        <div class='sectionsubheader'>
            (who you are following)
        </div>
        <ul>
    <?
    flush();ob_flush();

    foreach ($notFollowingYou as $id) {
        $userData = $tw->getUserInfo($id);
        if (!$userData) continue;
        
        ?>
            <div class='user'>
                <img src='<?=$userData['image']?>' height=50 width=50>&nbsp;
                <a href='http://twitter.com/<?=$userData['screen_name']?>'>
                    <b><?=$userData['screen_name']?></b>
                </a>
            </div>
        <?
        flush();ob_flush();
    }

    ?>
        </ul>
        <br><br>
        <div class='sectionheader'>
            You are not following <?=count($notFollowingThem)?> people
        </div>
        <div class='sectionsubheader'>
            (who are following you)
        </div>
        <ul>
    <?
    flush();ob_flush();

    foreach ($notFollowingThem as $id) {
        $userData = $tw->getUserInfo($id);
        if (!$userData) continue;
        
        ?>
            <div class='user'>
                <img src='<?=$userData['image']?>' height=50 width=50>&nbsp;
                <a href='http://twitter.com/<?=$userData['screen_name']?>'>
                    <b><?=$userData['screen_name']?></b>
                </a>
            </div>
        <?
        flush();ob_flush();
    }
    
    ?>
        </ul>
