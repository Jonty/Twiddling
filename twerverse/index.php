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

    $aSections = array(
        array(
            'title'     => count($notFollowingYou).' people are not following you',
            'subtitle'  => '(who you are following)',
            'data'      => $notFollowingYou,
        ),
        array(
            'title'     => 'You are not following '.count($notFollowingThem).' people',
            'subtitle'  => '(who are following you)',
            'data'      => $notFollowingThem,
        ),
    );

    ?>
        <br>
        <div>
            <small>
                <strong>Summary:</strong> <?=count($notFollowingYou)?> people are not following you, you are not following <?=count($notFollowingThem)?> people.
            </small>
        </div>
    <?

    foreach ($aSections as $aSection) {
        ?>
        <div class='sectionheader'>
            <?=$aSection['title']?>
        </div>
        <div class='sectionsubheader'>
            <?=$aSection['subtitle']?>
        </div>
        <ul>
        <?
        flush();ob_flush();

        foreach ($aSection['data'] as $id) {
            $userData = $tw->getUserInfo($id);
            if (!$userData) continue; // Deleted users
            
            ?>
                <div class='user'>
                    <img src='<?=$userData['image']?>' height=50 width=50>&nbsp;
                    <a href='http://twitter.com/<?=htmlentities($userData['screen_name'])?>'>
                        <b><?=htmlentities($userData['screen_name'])?></b>
                    </a>
                </div>
            <?
            flush();ob_flush(); // Generation can take a while, so get the data out
        }
        
        ?>
        </ul>
        <br>
        <?
    }

    ?>
    </body>
    </html>
