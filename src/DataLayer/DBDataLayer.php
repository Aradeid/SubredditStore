<?php
    namespace DataLayer;

    use \Domain\Subreddit;
    use \Domain\Category;
    use \Domain\User;
    use \Domain\Comment;

    class DBDataLayer implements DataLayer {

        private $server;
        private $userName;
        private $password;
        private $database;

        public function __construct($server, $userName, $password, $database) {
            $this->server = $server;
            $this->userName = $userName;
            $this->password = $password;
            $this->database = $database;
        }

        private function getConnection() {
            $conn = new \mysqli($this->server, $this->userName, $this->password, $this->database);
            if (!$conn) {
                die('Unable to connect to database: ' . mysqli_connect_error());
            }
            return $conn;
        }

        private function executeQuery($connection, $query) {
            $result = $connection->query($query);
            if (!$result) {
                die('Error in query `$query`: ' . $connection->error);
            }
            return $result;
        }

        private function executeStatement($connection, $query, $bindFunc) {
            $statement = $connection->prepare($query);
            if (!$statement) {
                die('Error in prepared statement `$query`: ' . $connection->error);
            }
            $bindFunc($statement);
            if (!$statement->execute()) {
                die('Error executing prepared statement `$query`: ' . $connection->error);
            }
            return $statement;
        }

        public function getCategories() {
            $categories = array();
            
            $conn = $this->getConnection();
            $res = $this->executeQuery($conn, 'SELECT id, name, titular FROM categories ORDER BY name');
            while ($cat = $res->fetch_object()) {
                $categories[] = new Category($cat->id, $cat->name, $cat->titular);
            }
            $res->close();
            $conn->close();

            return $categories;
        }

        public function getSubreddits() {
            $subreddits = array();
            
            $conn = $this->getConnection();
            $res = $this->executeQuery($conn, 'SELECT s.id sid, categoryid, url, description, multiplier, u.id uid, username FROM subreddits s JOIN users u ON (s.submitter = u.id) ORDER BY creationdate DESC');
            while ($sub = $res->fetch_object()) {
                $subreddits[] = new Subreddit($sub->sid, $sub->categoryid, $sub->url, $sub->description, new User($sub->uid, $sub->username), $sub->multiplier, $this->getSubscribersForUrl($sub->url));
            }
            $res->close();
            $conn->close();

            return $subreddits;
        }

        public function getSubredditForId($subredditId) {
            $subreddit = null;

            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'SELECT s.id sid, categoryid, url, description, multiplier, u.id uid, username FROM subreddits s JOIN users u ON (s.submitter = u.id) WHERE s.id = ? ORDER BY creationdate DESC',
                function($s) use ($subredditId) {
                    $s->bind_param('i', $subredditId);
                }
            );
            $stat->bind_result($sid, $categoryId, $url, $description, $multiplier, $uid, $userName);
            if ($stat->fetch()) {
                $subreddit = new Subreddit($sid, $categoryId, $url, $description, new User($uid, $userName), $multiplier, $this->getSubscribersForUrl($url));
            }
            $stat->close();
            $conn->close();

            return $subreddit;
        }
    
        public function getSubredditsForCategory($categoryId) {
            $subreddits = array();

            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn, 
                'SELECT s.id, categoryid, url, description, multiplier, u.id, username FROM subreddits s JOIN users u ON (s.submitter = u.id) WHERE categoryid = ? ORDER BY creationdate DESC',
                function($s) use ($categoryId) {
                    $s->bind_param('i', $categoryId);
                }
            );
            $stat->bind_result($sid, $categoryId, $url, $description, $multiplier, $uid, $userName);
            while ($stat->fetch()) {
                $subreddits[] = new Subreddit($sid, $categoryId, $url, $description, new User($uid, $userName), $multiplier, $this->getSubscribersForUrl($url));
            }
            $stat->close();
            $conn->close();

            return $subreddits;
        }

        public function getSubredditForUrl($url) {
            $subreddits = null;

            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn, 
                'SELECT s.id, categoryid, url, description, multiplier, u.id, username FROM subreddits s JOIN users u ON (s.submitter = u.id) WHERE url = ?',
                function($s) use ($url) {
                    $s->bind_param('s', $url);
                }
            );
            $stat->bind_result($sid, $categoryId, $url, $description, $multiplier, $uid, $userName);
            if ($stat->fetch()) {
                $subreddits = new Subreddit($sid, $categoryId, $url, $description, new User($uid, $userName), $multiplier, $this->getSubscribersForUrl($url));
            }
            $stat->close();
            $conn->close();

            return $subreddits;
        }
    
        public function getSubredditsForSearchCriteria($url) {
            $url = "%$url%";
            $subreddits = array();

            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'SELECT s.id, categoryid, url, description, multiplier, u.id, username FROM subreddits s JOIN users u ON (s.submitter = u.id) WHERE url LIKE ?',
                function($s) use ($url) {
                    $s->bind_param('s', $url);
                }
            );
            $stat->bind_result($sid, $categoryId, $url, $description, $multiplier, $uid, $userName);
            while ($stat->fetch()) {
                $subreddits[] = new Subreddit($sid, $categoryId, $url, $description, new User($uid, $userName), $multiplier, $this->getSubscribersForUrl($url));
            }

            $stat->close();
            $conn->close();

            return $subreddits;
        }

        public function getSubscribersForUrl($url) {
            $json = json_decode(file_get_contents("https://www.reddit.com/r/$url/about.json"), true);
            return $json['data']['subscribers'];
        }

        public function postSubreddit($url, $submitterId, $categoryId, $multiplier, $description) {
            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'INSERT INTO subreddits (url, submitter, categoryid, multiplier, description) VALUES (?,?,?,?,?)',
                function($s) use ($url, $submitterId, $categoryId, $multiplier, $description) {
                    $s->bind_param('siiis', $url, $submitterId, $categoryId, $multiplier, $description);
                }
            );

            $stat->close();
            $conn->close();

            return $this->getSubredditForUrl($url);
        }

        public function updateSubreddit($url, $submitterId, $categoryId, $multiplier, $description) {
            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'UPDATE subreddits SET categoryid = ?, multiplier = ?, description = ?, lastmodified = NOW() WHERE url = ? AND submitter = ?',
                function($s) use ($url, $submitterId, $categoryId, $multiplier, $description) {
                    $s->bind_param('iissi', $categoryId, $multiplier, $description, $url, $submitterId);
                }
            );

            $stat->close();
            $conn->close();

            return $this->getSubredditForUrl($url);
        }
    
        public function createOrder($userId, $subredditIds, $nameOnCard, $cardNumber) {
            $conn = $this->getConnection();
            $conn->autocommit(false);

            $stat = $this->executeStatement(
                $conn,
                'INSERT INTO orders (userId, creditCardHolder, creditCardNumber) VALUES (?, ?, ?)',
                function($s) use ($userId, $nameOnCard, $cardNumber) {
                    $s->bind_param('iss', $userId, $nameOnCard, $cardNumber);
                }
            );
            $orderId = $stat->insert_id;
            $stat->close();

            foreach($subredditIds as $subredditId) {
                $this->executeStatement($conn,
                    'INSERT INTO orderedSubreddits (orderId, subredditId) VALUES(?, ?)',
                    function($s) use ($orderId, $subredditId) {
                        $s->bind_param('ii', $orderId, $subredditId);
                    }
                )->close();
            }

            $conn->commit();
            $conn->close();

            return $orderId;
        }
    
        public function getUser($id) {
            $user = null;
            
            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'SELECT id, userName FROM users WHERE id = ?',
                function($s) use ($id) {
                    $s->bind_param('i', $id);
                }
            );
            $stat->bind_result($id, $userName);

            if ($stat->fetch()) {
                $user = new User($id, $userName);
            }

            $stat->close();
            $conn->close();

            return $user;
        }

        public function getUserForUserName($userName) {
            $user = null;

            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'SELECT id FROM users WHERE userName = ?',
                function($s) use ($userName) {
                    $s->bind_param('s', $userName);
                }
            );
            $stat->bind_result($id);
            
            if ($stat->fetch()) {
                $user = new User($id, $userName);
            }

            $stat->close();
            $conn->close();

            return $user;
        }
    
        public function getUserForUserNameAndPassword($userName, $password) {
            $user = null;

            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'SELECT id, passwordHash FROM users WHERE userName = ?',
                function($s) use ($userName) {
                    $s->bind_param('s', $userName);
                }
            );
            $stat->bind_result($id, $passwordHash);
            
            if ($stat->fetch()  && password_verify($password, $passwordHash)) {
                $user = new User($id, $userName);
            }

            $stat->close();
            $conn->close();

            return $user;
        }

        public function postUserWithUserNameAndPassword($userName, $password) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'INSERT INTO users (userName, passwordHash) VALUES (?,?)',
                function($s) use ($userName, $passwordHash) {
                    $s->bind_param('ss', $userName, $passwordHash);
                }
            );

            $stat->close();
            $conn->close();
        }

        public function getRatingForSubreddit($subredditId) {
            $value = null;

            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'SELECT AVG(value) FROM rating WHERE subredditid = ?',
                function($s) use ($subredditId) {
                    $s->bind_param('i', $subredditId);
                }
            );
            $stat->bind_result($val);
            
            if ($stat->fetch()) {
                $value = $val;
            }

            $stat->close();
            $conn->close();

            return $value;
        }

        public function getRatingForSubredditAndUser($subredditId, $userId) {
            $value = null;

            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'SELECT value FROM rating WHERE subredditid = ? AND userid = ?',
                function($s) use ($subredditId, $userId) {
                    $s->bind_param('ii', $subredditId, $userId);
                }
            );
            $stat->bind_result($val);
            
            if ($stat->fetch()) {
                $value = $val;
            }

            $stat->close();
            $conn->close();

            return (int)$value;
        }

        public function postRatingForSubredditAndUser($subredditId, $userId, $value) {
            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'INSERT INTO rating (subredditid, userid, value) VALUES (?,?,?)',
                function($s) use ($subredditId, $userId, $value) {
                    $s->bind_param('iii', $subredditId, $userId, $value);
                }
            );

            $stat->close();
            $conn->close();

            return true;
        }

        public function updateRatingForSubredditAndUser($subredditId, $userId, $value) {
            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'UPDATE rating SET value = ? WHERE subredditid = ? AND userid = ?',
                function($s) use ($subredditId, $userId, $value) {
                    $s->bind_param('iii', $value, $subredditId, $userId);
                }
            );

            $stat->close();
            $conn->close();

            return true;
        }

        public function getCommentsForSubreddit($subredditId) {
            $comments = array();

            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'SELECT c.id cid, content, u.id uid, username FROM comments c JOIN users u ON (c.userid = u.id) WHERE subredditid = ? ORDER BY creationdate DESC',
                function($s) use ($subredditId) {
                    $s->bind_param('i', $subredditId);
                }
            );
            
            $stat->bind_result($cid, $content, $uid, $username);
            while ($stat->fetch()) {
                $comments[] = new Comment($cid, $content, new User($uid, $username));
            }

            $stat->close();
            $conn->close();

            return $comments;
        }

        public function getCommentForSubredditAndUser($subredditId, $userId) {
            $comment = null;

            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'SELECT c.id cid, content, u.id uid, username FROM comments c JOIN users u ON (c.userid = u.id) WHERE subredditid = ? AND userid = ?',
                function($s) use ($subredditId, $userId) {
                    $s->bind_param('ii', $subredditId, $userId);
                }
            );
            $stat->bind_result($cid, $content, $uid, $username);
            
            if ($stat->fetch()) {
                $comment = new Comment($cid, $content, new User($uid, $username));
            }

            $stat->close();
            $conn->close();

            return $comment;
        }

        public function postCommentForSubredditAndUser($subredditId, $userId, $content) {
            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'INSERT INTO comments (subredditid, userid, content) VALUES (?,?,?)',
                function($s) use ($subredditId, $userId, $content) {
                    $s->bind_param('iis', $subredditId, $userId, $content);
                }
            );

            $stat->close();
            $conn->close();

            return true;
        }

        public function updateCommentForSubredditAndUser($subredditId, $userId, $content) {
            $conn = $this->getConnection();
            $stat = $this->executeStatement(
                $conn,
                'UPDATE comments SET content = ?, lastmodified = NOW() WHERE subredditid = ? AND userid = ?',
                function($s) use ($subredditId, $userId, $content) {
                    $s->bind_param('sii', $content, $subredditId, $userId);
                }
            );

            $stat->close();
            $conn->close();

            return true;
        }
    }