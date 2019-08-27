<?php


use Phinx\Seed\AbstractSeed;

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
        $settings = require __DIR__ . '/../../settings.php';

        $data = [
            [
                'username' => 'ezstream',
                'password' => \password_hash('admin', $settings['settings']['app']['password_algo']),
                'email' => 'changeme@example.com',
                'is_admin' => true,
                'view_stream_list' => true,
                'is_active' => true,
                'created_at' => 'now'
            ]
        ];

        $users = $this->table('users');
        $users->insert($data)->save();
    }
}
