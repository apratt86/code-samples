<?php
/**
 * The file that instanciates custom tables
 *
 * @link       https://github.com/apratt86
 * @since      1.0.0
 * @author     Aaron Pratt <aaronprattdesign@gmail.com>
 */

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

class Custom_Table {

    public $table, $uses_prefix, $fields, $created_table;

    public function __construct( $table = '', $fields = [], $uses_prefix = true ) {
        $this->table = $table;
        $this->fields = $fields;
        $this->uses_prefix = $uses_prefix;

        $this->created_table = self::make_table();
    }

    private function make_table() {
        global $wpdb;

        $tablename = ( $this->uses_prefix ) ? $wpdb->prefix . $this->table : $this->table;

        $fields_sql = 'ID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY';

        foreach( $this->fields as $name => $format ) :
            $fields_sql .= ', ' . $name . ' ' . mb_strtoupper( $format );
        endforeach;

        $sql_create = "CREATE TABLE {$tablename} ( {$fields_sql} );";

        $created_table = maybe_create_table( $tablename, $sql_create );

        return $created_table;
    }

}