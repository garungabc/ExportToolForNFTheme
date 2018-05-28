<?php

use Illuminate\Database\Schema\Blueprint;
use Vicoders\Database\Connect\NFDatabase;
use Illuminate\Database\Capsule\Manager as Capsule;

class CreateNotificationsTable extends NFDatabase
{
    private $table = 'notifications';

    public function up()
    {
        global $wpdb;
        $table_name_with_prefix = $wpdb->prefix . $this->table;
        if (!Capsule::Schema()->hasTable($table_name_with_prefix)) {
            Capsule::Schema()->create($table_name_with_prefix, function($table){
                $table->increments('id');
                $table->boolean('type');
                $table->integer('notifiable_id');
                $table->string('notifiable_type', 50);
                $table->text('content');
                $table->timestamps();
            });
        }
    }

    public function down() {
        global $wpdb;
        $table_name_with_prefix = $wpdb->prefix . $this->table;
        if (Capsule::Schema()->hasTable($table_name_with_prefix)) {
            Capsule::Schema()->drop($table_name_with_prefix);
        }
    }
}
