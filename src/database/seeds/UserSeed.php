<?php


use Phinx\Seed\AbstractSeed;
use App\Models\User;

class UserSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        if (!$this->shouldSeed()) {
            return;
        }

        $settings = require __DIR__ . '/../../settings.php';

        $userData = [
            [
                'username' => 'ezstream',
                'password' => \password_hash($settings['settings']['app']['default_password'], $settings['settings']['app']['password_algo']),
                'email' => 'changeme@example.com',
                'is_admin' => true,
                'view_stream_list' => true,
                'is_active' => true,
                'created_at' => 'now'
            ]
        ];

        $users = $this->table('users');
        $users->insert($userData)->save();

        // Insert admin stream
        $streamData = [
            [
                'id' => 1,
                'name' => $userData[0]['username'],
                'created_at' => 'now'
            ]
        ];

        $streams = $this->table('streams');
        $streams->insert($streamData)->save();
    }

    private function shouldSeed(): bool
    {
        $stmt = $this->query('SELECT COUNT(id) as `count` FROM users');
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return !$result || $result->count < 1;
    }
}
