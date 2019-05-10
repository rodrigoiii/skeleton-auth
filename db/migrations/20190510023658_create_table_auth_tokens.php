<?php

use Phinx\Migration\AbstractMigration;

class CreateTableAuthTokens extends AbstractMigration
{
    /**
     * [up description]
     *
     * @return void
     */
    public function up()
    {
        $table = $this->table('auth_tokens')
            ->addColumn('token', 'string', ['limit' => 13])
            ->addColumn('is_used', 'boolean', ['default' => 0])
            ->addColumn('type', 'enum', ['values' => ["register", "reset-password"]])
            ->addColumn('payload', 'json', ['null' => true])
            ->addTimestamps();

        $table->create();
    }

    /**
     * [down description]
     *
     * @return void
     */
    public function down()
    {
        $table_exist = $this->hasTable('auth_tokens');
        if ($table_exist)
        {
            $this->dropTable('auth_tokens');
        }
    }
}
