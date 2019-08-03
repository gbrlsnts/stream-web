<?php

use Phinx\Migration\AbstractMigration;

class BaseMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        // Users Table
        $this->table('users')
            ->addColumn('username',         'string', ['limit' => 20])
            ->addColumn('password',         'string', ['limit' => 60])
            ->addColumn('email',            'string', ['limit' => 100])
            ->addColumn('is_admin',         'boolean', ['default' => false])
            ->addColumn('view_stream_list', 'boolean', ['default' => false])
            ->addColumn('is_active',        'boolean', ['default' => true])
            ->addColumn('created_at',       'datetime')

            ->addIndex(['username'], ['unique' => true, 'name' => 'idx_username_unique'])
            ->addIndex(['email'], ['unique' => true, 'name' => 'idx_email_unique'])

            ->create();

        // Streams table
        $streams = $this->table('streams', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id',               'integer', ['signed' => false])
            ->addColumn('name',             'string', ['limit' => 150])
            ->addColumn('is_streaming',     'boolean', ['default' => false])
            ->addColumn('is_active',        'boolean', ['default' => true])
            ->addColumn('last_stream_at',   'datetime')
            ->addColumn('created_at',       'datetime')

            ->addForeignKey('id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])

            ->create();

        // Stream Tokens Table
        $this->table('tokens')
            ->addColumn('stream_id',        'integer', ['signed' => false])
            ->addColumn('token',            'string', ['limit' => 10])
            ->addColumn('num_usages',       'integer', ['default' => 0])
            ->addColumn('max_usages',       'integer', ['default' => 0])
            ->addColumn('expires_at',       'datetime')
            ->addColumn('created_at',       'datetime')

            ->addForeignKey('stream_id', 'streams', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])

            ->create();
    }
}
