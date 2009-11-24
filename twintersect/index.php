<?php
 
    include('twintersect.class.php');

    ?>
        <html>
        <head>
            <title>Twintersect</title>
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
        <div class='heading'>Twintersect</div>
        <div class='description'>Twintersect shows you the intersections between your friends/followers lists and those of another user, so you can work out how you know them.</div>
    <?php

    $userA = trim($_GET['userA']);
    $userB = trim($_GET['userB']);

    $tw = new Twintersect();

    if ($userA && $userB) {
        $userAInfo = $tw->getXml("users/show.xml?screen_name={$userA}");
        $userBInfo = $tw->getXml("users/show.xml?screen_name={$userB}");
        if (!$userAInfo['screen_name'] || !$userBInfo['screen_name']) {
            $userA = $userB = null;
            ?>
                <h3>Hm, are those twitter usernames correct?</h3>
            <?
        }
    }

    if (!$userA && !$userB) {
        ?>
            <div class='form'>
                <form>
                    <b>Your Twitter Username:</b>
                    <input type="textbox" name="userA">
                    &nbsp;
                    <b>Their Twitter Username:</b>
                    <input type="textbox" name="userB">
                    <input type="submit" value="calculate twintersections!">
                </form>
            </div>
        <?php
        exit;
    }

    $int = $tw->getIntersectForUsers($userA, $userB);

    $aSections = array(
        array(
            'title'     => 'You are both following '.count($int['friends']).' people',
            'subtitle'  => '',
            'data'      => $int['friends'],
        ),
        array(
            'title'     => 'You are both being followed by '.count($int['followers']).' people',
            'subtitle'  => '',
            'data'      => $int['followers'],
        ),
        array(
            'title'     => 'You are following '.count($int['friendsAfollowersB']).' people who are following '.htmlentities($userB),
            'subtitle'  => '',
            'data'      => $int['friendsAfollowersB'],
        ),
        array(
            'title'     => htmlentities($userB).' is following '.count($int['friendsBfollowersA']).' people who are following you',
            'subtitle'  => '',
            'data'      => $int['friendsBfollowersA'],
        ),
        array(
            'title'     => 'You are both fans of '.count($int['fansOf']).' people',
            'subtitle'  => '(People you are both following that aren\'t following either of you)',
            'data'      => $int['fansOf'],
        ),
    );


    ?>
        <small>
            Pssst, here's a magic <a href="<?include('bookmarklet.php')?>">twintersect bookmarklet</a> that compares you (<?=htmlentities($userA)?>) to the twitter user page you're currently on.
        </small>

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
