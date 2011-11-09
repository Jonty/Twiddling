<?php

    class Twiddler {
        private $dbFile = '../twiddler.db';
        private $db;

        function db($reinitialise = false) {
            if (!$this->db || $reinitialise) {
                $this->db = new PDO("sqlite:{$this->dbFile}");
            }
            return $this->db;
        }

        function createTable() {
            // Reinit the db to nuke the error that caused the create to occur
            $db = $this->db(true);

            $db->query(
                'CREATE TABLE users (
                    id int,
                    name string,
                    screen_name string,
                    image string,
                    url string,
                    timestamp int,
                    deleted boolean,
                    PRIMARY KEY (id)
                );'
            );

            $errorInfo = $db->errorInfo();
            if ($errorInfo && $errorInfo[0] != '0000') {
                trigger_error("Error creating DB tables: {$errorInfo[2]}", E_USER_ERROR);
            }
        }

        function getXml($url) {
            $oCurl = curl_init("http://api.twitter.com/1/{$url}");

            curl_setopt_array(
                $oCurl,
                array(
                    CURLOPT_HEADER          => false,
                    CURLOPT_RETURNTRANSFER  => true,
                    CURLOPT_FOLLOWLOCATION  => true,
                )
            );

            $xml = curl_exec($oCurl);
            curl_close($oCurl); 

            return (array) simplexml_load_string($xml);
        }

        function getUserInfoFromCache($userId) {
            $db = $this->db();

            $oUserFetch = $db->prepare(
                'SELECT * FROM users WHERE id = ?;'
            );
            $errInfo = $db->errorInfo();

            // Create the table if it doesn't exist
            $aUserData = null;
            if (isset($errInfo[2]) && (strpos($errInfo[2], 'no such table') !== false)) {
                $this->createTable();
            } else {
                $oUserFetch->execute(
                    array($userId)
                );

                if ($aRows = $oUserFetch->fetchAll()) {
                    $aUserData = $aRows[0];
                }
            }

            return $aUserData;
        }

        function getUserInfoFromTwitter($userId) {
            $db = $this->db();

            $aUserData = $this->getXml("users/show.xml?user_id={$userId}");

            $oUserInsert = $db->prepare('
                INSERT INTO users
                    (id, name, screen_name, image, url, timestamp, deleted)
                VALUES
                    (?,?,?,?,?,?,?);'
            );

            if ($aUserData['screen_name']) {
                $aUserData = array(
                    'id'            => $userId, 
                    'name'          => $aUserData['name'],
                    'screen_name'   => $aUserData['screen_name'],
                    'image'         => $aUserData['profile_image_url'],
                    'url'           => $aUserData['url'],
                    'timestamp'     => time(),
                    'deleted'       => false,
                );

            } else {
                // Deleted user, flag as such
                $aUserData = array(
                    $userId, '', '', '', '', time(), true,
                );
            }

            $oUserInsert->execute(array_values($aUserData));

            return $aUserData;
        }

        function getUserInfo($userId) {
            $db = $this->db();

            $aUserData = $this->getUserInfoFromCache($userId);

            // If data is older than a week, invalidate
            if ($aUserData && $aUserData['timestamp'] < (time() - 604800)) {
                $aUserData = null;
            }

            if (!$aUserData) {
                $aUserData = $this->getUserInfoFromTwitter($userId);
            }

            if ($aUserData['deleted']) {
                $aUserData = null;
            }

            return $aUserData;
        }

    }
