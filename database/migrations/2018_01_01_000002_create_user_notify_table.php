<?php

use Illuminate\Database\Schema\Blueprint;
use Vicoders\Database\Connect\NFDatabase;
use Illuminate\Database\Capsule\Manager as Capsule;

class CreateUserNotifyTable extends NFDatabase
{
    private $table = 'user_notify';

    public function up()
    {
        global $wpdb;
        $table_name_with_prefix = $wpdb->prefix . $this->table;
        if (!Capsule::Schema()->hasTable($table_name_with_prefix)) {
            Capsule::Schema()->create($table_name_with_prefix, function($table){
                $table->increments('id');
                $table->integer('user_id');
                $table->integer('notification_id')->unsigned()->index();
                $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
                $table->integer('status')->comment('0: unread; 1: read');
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
