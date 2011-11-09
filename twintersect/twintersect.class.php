<?php

    require_once('../twiddler.class.php');

    class Twintersect extends Twiddler {

        private function getIdsForUser($type, $user) {
            $ids = $this->getXml("{$type}/ids.xml?screen_name={$user}");
            $ids = (array) $ids['ids'];
            return isset($ids['id']) ? $ids['id'] : array();
        }

        public function getIntersectForUsers($userA, $userB) {
            $friendsA = $this->getIdsForUser('friends', $userA);
            $friendsB = $this->getIdsForUser('friends', $userB);
            $followersA = $this->getIdsForUser('followers', $userA);
            $followersB = $this->getIdsForUser('followers', $userB);

            // Calculate people they're fans of
            $friendsIntersection = array_intersect($friendsA, $friendsB);
            $fansOf = array_diff($friendsIntersection, $followersA, $followersB);

            return array(
                'friends'               => $friendsIntersection,
                'followers'             => array_intersect($followersA, $followersB),
                'friendsAfollowersB'    => array_intersect($friendsA, $followersB),
                'friendsBfollowersA'    => array_intersect($friendsB, $followersA),
                'fansOf'                => $fansOf,
            );
        }

    }
