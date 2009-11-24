<?php

    require_once('../twiddler.class.php');

    class Twerverse extends Twiddler {

        function getDiffsForUser($user) {
            $followingXml = $this->getXml("friends/ids.xml?screen_name={$user}");
            $followersXml = $this->getXml("followers/ids.xml?screen_name={$user}");

            $notFollowingYou = $notFollowingThem = false;
            if (isset($followingXml['id']) || isset($followersXml['id'])) {

                // Because they may have no followers, or no friends
                $following = isset($followingXml['id']) ? $followingXml['id'] : array();
                $followers = isset($followersXml['id']) ? $followersXml['id'] : array();

                $notFollowingYou = array_diff($following, $followers);
                $notFollowingThem = array_diff($followers, $following);
            }

            return array($notFollowingYou, $notFollowingThem);
        }
    }
