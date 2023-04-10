<?php

namespace Boodschappenservice\objects;

use Boodschappenservice\utilities\ArrayList;

/**
 * @property-read int $id
 */
class User implements \JsonSerializable {

    /**
     * @return Array<User>
     * @throws \Exception
     */
    public static function getAll() : array {
        global $conn;
        $stmt = $conn->prepare("SELECT userId FROM `users`");
        $res = $stmt->execute();
        if($res) {
            $users = new ArrayList();
            $stmt->bind_result($userId);
            while($stmt->fetch()) {
                $users->add($userId);
            }

            return $users->map(function($userId) {
                return User::get($userId);
            })->getArray();
        } else throw new \Exception($stmt->error, 500);
    }

    public static function create(string $username, string $password, string $email, string $role) : User {
        global $conn;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO `users` (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashedPassword, $email, $role);
        $res = $stmt->execute();
        if($res) {
            $userId = $conn->insert_id;
            return User::get($userId);
        } else throw new \Exception($stmt->error, 500);
    }

    public static function get(int $userId) : User {
        return new User($userId);
    }

    public int $userId;
    public string $username, $password, $email, $role;

    private function __construct(int $userId) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE userId = ?");
        $stmt->bind_param("i", $userId);
        $res = $stmt->execute();
        if($res) {
            $stmt->bind_result($userId, $username, $password, $email, $role);
            if($stmt->fetch() === null)
                throw new \Exception("User with userId $userId does not exist", 404);

            $this->userId = $userId;
            $this->username = $username;
            $this->password = $password;
            $this->email = $email;
            $this->role = $role;
        } else throw new \Exception($stmt->error, 500);
    }

    public function save() {
        global $conn;
        $stmt = $conn->prepare("UPDATE `users` SET username = ?, password = ?, email = ?, role = ? WHERE userId = ?");
        $stmt->bind_param("ssssi", $this->username, $this->password, $this->email, $this->role, $this->userId);
        if(!$stmt->execute()) throw new \Exception($stmt->error, 500);
    }

    public function delete() {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM `users` WHERE userId = ?");
        $stmt->bind_param("i", $this->userId);
        if(!$stmt->execute()) throw new \Exception($stmt->error, 500);
    }

    public function jsonSerialize(): array {
        return [
            "userId" => $this->userId,
            "username" => $this->username,
            "email" => $this->email,
            "role" => $this->role
        ];
    }

    public static function register(string $username, string $password, string $email, string $role = 'user'): User
    {
        try {
            // Create the new user
            return User::create($username, $password, $email, $role);
        } catch (\mysqli_sql_exception $e) {
            // Check for duplicate entry error code
            if ($e->getCode() == 1062) {
                throw new \Exception('Username or email already exists.', 409);
            } else {
                throw $e;
            }
        }
    }

    public static function login(string $username, string $password): User
    {
        global $conn;
        $stmt = $conn->prepare("SELECT userId, password FROM `users` WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $res = $stmt->execute();
        $stmt->store_result();

        if ($res) {
            $stmt->bind_result($userId, $hashedPassword);
            if ($stmt->fetch()) {
                if (password_verify($password, $hashedPassword)) {
                    return User::get($userId);
                } else {
                    throw new \Exception("Invalid password.", 401);
                }
            } else {
                throw new \Exception("User not found.", 404);
            }
        } else {
            throw new \Exception($stmt->error, 500);
        }
    }


    public static function searchUsers(string $searchQuery): array {
        global $conn;
        $searchQuery = "%{$searchQuery}%";
        $stmt = $conn->prepare("SELECT userId FROM `users` WHERE username LIKE ? OR email LIKE ?");
        $stmt->bind_param("ss", $searchQuery, $searchQuery);
        $res = $stmt->execute();

        if ($res) {
            $users = new ArrayList();
            $stmt->bind_result($userId);
            while ($stmt->fetch()) {
                $users->add($userId);
            }

            return $users->map(function ($userId) {
                return User::get($userId);
            })->getArray();
        } else {
            throw new \Exception($stmt->error, 500);
        }
    }
//$searchResults = User::searchUsers('john');
}
