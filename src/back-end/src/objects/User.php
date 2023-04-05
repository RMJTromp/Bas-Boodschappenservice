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
}
