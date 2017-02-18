<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* @author: Rance Aaron * Description: Database Manager model class */
class Dbmanager_model extends CI_Model{ 
    public $exclude = array('tmp_', 'test_');
    public $ExcludeTables = array('piwik_visits');
    
    function __construct(){
        parent::__construct();        
    }
    
    public function get_current_db(){
        return $this->db->database;
    }

    public function check_excluded_tables($x){        
        if(!preg_match('/^mobile_cause/', $x) &&
                !preg_match('/^mobile_response/', $x) &&
                !preg_match('/^mobile_responses/', $x) &&
                !preg_match('/^temp_/', $x) &&
                !preg_match('/^suivi/', $x) &&
                !preg_match('/^sp_/', $x)){                
                return TRUE;
            }else{                
                return FALSE;
        }    
    }
    
    public function get_table_names($x){        
        $sql = "SELECT * FROM information_schema.tables where table_schema='$x' and table_type = 'base table';";        
        $c = $this->db->query($sql);
        $a = array();
        $a1 = array();
        $index_total = array();
        
        foreach ($c->result_array() as $value) {
            $b = array();
            if(!preg_match('/^test_/', $value['TABLE_NAME']) && !preg_match('/^tmp_/', $value['TABLE_NAME'])){
                if(!in_array($value['TABLE_NAME'], $this->ExcludeTables)){
                    $b['name'] = $value['TABLE_NAME'];
                    $table_names[] = $value['TABLE_NAME'];
                    $b['collation'] = $value['TABLE_COLLATION'];                
                    $b['keys'] = $this->get_table_key_details($value['TABLE_NAME']);
                    $b['columns'] = $this->get_tablecolumn_names($value['TABLE_NAME']);
                    $index_total[] = $this->count_table_keys($value['TABLE_NAME']);
                    $a['tables'][$value['TABLE_NAME']] = $b;  
                }                                             
            }            
        }
        
        $_SESSION['table_names'] = json_encode($table_names);
        $a['counts']['index_total'] = array_sum($index_total);
        $a['counts']['table_count'] = count($table_names);
        
        return $a;
    }
    
    public function get_selected_table_names($d){
        $sql = "SELECT * FROM information_schema.tables where table_schema='$d' and table_type = 'base table';";
        $c = $this->db->query($sql);
        $a = array();
        $index_total = array();
        foreach ($c->result_array() as $value) {
            $b = array();
            if(!preg_match('/^test_/', $value['TABLE_NAME']) && !preg_match('/^tmp_/', $value['TABLE_NAME'])){
                $b['name'] = $value['TABLE_NAME'];
                $table_names[] = $value['TABLE_NAME'];
                $b['collation'] = $value['TABLE_COLLATION'];                
                $b['keys'] = $this->get_table_key_details($value['TABLE_NAME']);
                $b['columns'] = $this->get_tablecolumn_names($value['TABLE_NAME']);
                $index_total[] = $this->count_table_keys($value['TABLE_NAME']);
                $a[$value['TABLE_NAME']] = $b;
            }            
        }
        
        $_SESSION['table_names'] = json_encode($table_names);
        $a['index_total'] = array_sum($index_total);
        $a['table_count'] = count($table_names);
        return $a;
    }
    
    public function get_listof_tables($d){
        $a = array();
        $sql = "SELECT 
                    TABLE_NAME
                FROM
                    information_schema.tables
                WHERE
                    table_schema = '$d'
                        AND table_type = 'base table';";
        $c = $this->db->query($sql);
        foreach ($c->result_array() as $value) {
            if(!preg_match('/^test_/', $value['TABLE_NAME']) && !preg_match('/^tmp_/', $value['TABLE_NAME']) && !preg_match('/^temp_/', $value['TABLE_NAME']) && !preg_match('/^sp_/', $value['TABLE_NAME']) && !preg_match('/^mobile_/', $value['TABLE_NAME']) && !preg_match('/^sp_/', $value['TABLE_NAME']) && !preg_match('/^delete_/', $value['TABLE_NAME'])){
                if(!in_array($value['TABLE_NAME'], $this->ExcludeTables)){
                    $a[$value['TABLE_NAME']] = $value['TABLE_NAME'];
                }                
            }            
        }
        
        return $a;
    }
        
    public function get_db_names(){        
        $sql = "show databases";
        $c = $this->db->query($sql);
        
        $db_names = array();
        
        foreach ($c->result_array() as $value) {
            if($value['Database'] == 'information_schema'){
                $db_names[] = 'none selected';       
            }else{
                if(isset($_SESSION['updgrade'])){
                    if($value['Database'] == $this->db->database){
                        $db_names[] = $value['Database'];
                    }
                }else{
                    $db_names[] = $value['Database'];  
                }                     
            }                 
        }        
        
        return $db_names;
    }
    
    public function get_tablecolumn_names($t){        
        $sql = "SELECT 
            *
        FROM
            INFORMATION_SCHEMA.COLUMNS
        WHERE
            TABLE_SCHEMA = '".$this->db->database."'
                AND TABLE_NAME = '$t';";
        
        $c = $this->db->query($sql);
        $a = array();
        foreach ($c->result_array() as $value) {
            $b = array();
            $b['name'] = $value['TABLE_NAME'];
            $b['column_name'] = $value['COLUMN_NAME'];
            $b['column_default'] = $value['COLUMN_DEFAULT'];
            $b['is_nullable'] = $value['IS_NULLABLE'];
            $b['data_type'] = $value['DATA_TYPE'];
            $b['character_maximum_length'] = $value['CHARACTER_MAXIMUM_LENGTH'];
            $b['character_set_name'] = $value['CHARACTER_SET_NAME'];
            $b['collation_name'] = $value['COLLATION_NAME'];
            $b['column_type'] = $value['COLUMN_TYPE'];
            $b['column_key'] = $value['COLUMN_KEY'];
            $b['extra'] = $value['EXTRA'];
            $a[$value['COLUMN_NAME']] = $b;
        }
       
        return $a;
    }
    
    public function get_table_key_details($t){            
        $key_names = array();
        $final_key_list = array();
        
        $sql = "SHOW INDEX FROM $t";       
        $c = $this->db->query($sql);
        
        foreach ($c->result_array() as $value) {                      
            $key_names[] = $value['Key_name'];
        }
                
        $kn = array_unique($key_names);
        
        foreach ($kn as $key) {
            $a = array();
                        
            foreach ($c->result_array() as $value) {
                if($value['Key_name'] == $key){
                    $a[$key]['column_name'][] = $value['Column_name'];
                    $a[$key]['non_unique'] = $value['Non_unique'];
                    $a[$key]['null'] = $value['Null'];
                }
            }
            $final_key_list['key_list'][] = $a;
            $final_key_list['key_count'] = count($kn);
        }
                          
        return $final_key_list;
    }
    
    public function get_keys_per_table($t){         
        $key_names = array();
        $final_key_list = array();
        
        $sql = "SHOW INDEX FROM $t";       
        $c = $this->db->query($sql);
        
        foreach ($c->result_array() as $value) {                      
            $key_names[] = $value['Key_name'];
        }
        
        $kn = array_unique($key_names);
        
        foreach ($kn as $key) {
            $a = array();
            foreach ($c->result_array() as $value) {
                if($value['Key_name'] == $key){                    
                    $a[$key]['column_name'][] = $value['Column_name'];
                    $a[$key]['non_unique'] = $value['Non_unique'];
                    $a[$key]['null'] = $value['Null'];
                }
            }
            $final_key_list['key_list'][] = $a;
            $final_key_list['key_count'] = count($kn);
        }        
                                  
        return $final_key_list;
    }
    
    public function count_table_keys($t){
        $key_names = array();
        $sql = "SHOW INDEX FROM $t";       
        $c = $this->db->query($sql);        
        
        foreach ($c->result_array() as $value) {                      
            $key_names[] = $value['Key_name'];
        }
        
        $kn = array_unique($key_names);
        return count($kn);
    }
    
    public function get_view_names($current_database){
        if(isset($this->ExcludeViews) && !empty($this->ExcludeViews)){
            $sql = 'SELECT * FROM information_schema.tables where table_schema="'.$current_database.'" and table_type = "view" and TABLE_NAME NOT IN ("'. implode('","', $this->ExcludeViews).'");';
        }else{
            $sql = 'SELECT * FROM information_schema.tables where table_schema="'.$current_database.'" and table_type = "view";';
        }        
        
        $c = $this->db->query($sql);
        $a = array();
        $view_names = array();
        
        foreach ($c->result_array() as $value) {
            $d = array();
            if(!preg_match('/^bbbb_/', $value['TABLE_NAME']) && !preg_match('/^tmp_/', $value['TABLE_NAME'])){
                $view_names[] = $value['TABLE_NAME'];
                $d['definition'] = str_replace("ALGORITHM=UNDEFINED DEFINER=`inetstaging`@`%` SQL SECURITY DEFINER", " OR REPLACE ", $this->get_view_definition($value['TABLE_NAME']));
                $a[$value['TABLE_NAME']] = $d;
            }            
        }
        
        $_SESSION['view_names'] = json_encode($view_names);
        $a['view_count'] = count($view_names);
        return $a;
    }
    
    public function compare_views($v1, $source_views){             
        return array_diff($source_views, array_keys($this->get_view_names($v1)));
    }
    
    public function get_column_names(){
        $table_name = $_POST['tablename'];
        $sql = "SELECT 
            *
        FROM
            INFORMATION_SCHEMA.COLUMNS
        WHERE
            TABLE_SCHEMA = '".$this->db->database."'
                AND TABLE_NAME = '$table_name';";
        
        $c = $this->db->query($sql);
        $a = array();
        foreach ($c->result_array() as $value) {
            $b = array();
            $b['name'] = $value['TABLE_NAME'];
            $b['column_name'] = $value['COLUMN_NAME'];
            $b['data_type'] = $value['DATA_TYPE'];
            $b['column_type'] = $value['COLUMN_TYPE'];
            $b['column_key'] = $value['COLUMN_KEY'];
            $a[$value['TABLE_NAME']][$value['COLUMN_NAME']] = $b;
        }
        return $a;
    }
    
    public function get_table_column_names($d, $t){             
        $a = array();
        
        $sql = "SELECT 
                    COLUMN_NAME
                FROM
                    INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_SCHEMA = '$d'
                        AND TABLE_NAME = '$t';";
        
        $c = $this->db->query($sql);
        
        foreach ($c->result_array() as $value) {
            $a[] = $value['COLUMN_NAME'];
        }
                        
        return $a;
    }
    
    public function get_source_column_names($t){
        $src = array();
        
        foreach ($t as $tablename => $value) {
            if(isset($value['columns'])){                
                $src[$tablename] = array_keys($value['columns']);
            }else{
                $src[$tablename] = array();
            }            
        }
        
        return $src;
    }
    
    public function get_view_count(){
        $sql = "SELECT count(*) as count FROM information_schema.tables where table_schema='".$this->db->database."' and table_type = 'view';";
        $c = $this->db->query($sql);
        $a = $c->result_array();
        $a1 = array_shift($a);        
        return $a1['count'];
    }
            
    public function get_view_details(){
        $dep = array();
        $tlist = json_decode($_SESSION['table_names']);
        $vlist = json_decode($_SESSION['view_names']);        
        $v = $_POST['viewname'];
        $sql = "show create view $v";
        $c = $this->db->query($sql);
        $a = $c->result_array();
        $b = array_shift($a);
        
        foreach ($tlist as $tname) {
            if(stristr($b['Create View'],$tname)){
                $dep['tables'][] = $tname;
            }            
        }
        
        foreach ($vlist as $vname) {
            if(stristr($b['Create View'],$vname)){
                $dep['views'][] = $vname;
            }            
        }
        
        $d['def'] = str_replace("ALGORITHM=UNDEFINED DEFINER=`inetstaging`@`%` SQL SECURITY DEFINER", " OR REPLACE ", $b['Create View']);
        $d['dep'] = $dep;
        
        return $d;
    }
    
    public function lookup_view_dep($v, $selected_db){        
        $this->db->db_select($this->db->database);
        $base_view_def = $_SESSION['base_db_obj']['view_names'];
        $base_table_def = $_SESSION['base_db_obj']['table_names']['tables'];        
        $dep = array();
        $tlist = json_decode($_SESSION['table_names']);        
        $vlist = $this->list_views();
        $b = $base_view_def[$v]['definition'];
        $cnt = 0;
        $check;
        
        //Get view dependents that are tables
        foreach ($tlist as $tname) {
            if($this->check_excluded_tables($tname)){
                if(stristr($b,$tname)){
                    if(!$this->table_exists($selected_db,$tname)){
                        $this->add_new_table($base_table_def[$tname]);
                    }else{
                        $dep['tables'][] = $tname;
                    }                
                }
            }                        
        }
        
        //Get view dependents that are views by string searching the view
        //definition starting at Create View clause
        //Compare all strings in definition against the list of known view names
        //for matches, then check if the view exists                 
        foreach ($vlist as $vname) {                            
            if(stristr($b,$vname)){                
                if($v != $vname){                    
                    $dep['views'][] = $vname;
                    $cnt++;
                }                
            }            
        }
        
        //$d['def'] = str_replace("ALGORITHM=UNDEFINED DEFINER=`voluntee`@`99.62.19.56` SQL SECURITY DEFINER", " OR REPLACE ", $b['Create View']);
        $d['dep'] = $dep;
        $d['vcount'] = $cnt;             
                    
        if(!$this->view_exists($selected_db, $v)){
            if(!in_array($v, $this->ExcludeViews)){                
                if(isset($d['dep']['views'][0]) && !empty($d['dep']['views'][0])){                    
                    $check = $this->check_all_dep_views($d['dep']['views'], $selected_db);
                                     
                    if($check == 1){
                        $this->create_view( $b, $selected_db);
                    }else{
                        $b = $base_view_def[$check]['definition'];
                        $this->create_view( $b, $selected_db);
                    }                    
                }else{
                    $this->create_view( $b, $selected_db);
                }
            }                                
        }            
                                
        return $d;
    }
    
    public function list_views(){        
        $views = $_SESSION['base_db_obj']['view_names'];       
        return array_keys($views);       
    }
    
    public function check_all_dep_views($x, $d){                
        foreach ($x as $depv) {
            if(!$this->view_exists($d, $depv)){
                return $depv;
            }           
        }        
        return 1;        
    }
            
    public function view_exists($current_database, $view){
        $sql = "SELECT * 
                FROM information_schema.views
                WHERE table_schema = '$current_database' 
                    AND table_name = '$view'
                LIMIT 1;";
        
        $c = $this->db->query($sql);

        if(empty($c->result_array())){
            return false;
        }else{
            return true;
        }
    }
    
    public function table_exists($current_database, $table){
        $sql = "SELECT * 
                FROM information_schema.tables
                WHERE table_schema = '$current_database' 
                    AND table_name = '$table'
                LIMIT 1;";
        $c = $this->db->query($sql);

        if(empty($c->result_array())){
            return false;
        }else{
            return true;
        }
    }
    
    public function create_view($s, $cdb){        
        $this->db->db_select($cdb);       
        //Check query for definer clause
        if(!stristr($s,"ALGORITHM=UNDEFINED DEFINER=")){
            //Check for placeholders
            if(stristr($s,"^")){
                $sql = str_replace("^","'",$s); 
            }else{
                $sql = $s;
            }
            
            if($this->db->query($sql)){
                return true;
            }else{
                return false;
            }
        }
        
        return false;
    }
    
    public function get_view_definition($v){      
        $sql = "show create view $v";
        $c = $this->db->query($sql);
        $a = $c->result_array();
        $a1 = array_shift($a);
       
        return $a1['Create View'];
    }
    
    public function get_table_keys(){        
        $table_name = $_POST['t'];        
        $key_names = array();
        $final_key_list = array();
        
        $sql = "SHOW INDEX FROM $table_name";       
        $c = $this->db->query($sql);
        
        foreach ($c->result_array() as $value) {                      
            $key_names[] = $value['Key_name'];
        }
        
        $kn = array_unique($key_names);
        
        foreach ($kn as $key) {
            $a = array();
            foreach ($c->result_array() as $value) {
                if($value['Key_name'] == $key){
                    $a[$key][] = $value['Column_name'];
                }
            }
            $final_key_list[] = $a;
        }
                          
        return $final_key_list;
    }
    
    public function certify_db($current_database){        
        $certified_db = array();                
        //Get current table names with index count per table
        $certified_db['table_names'] = $this->get_table_names($current_database);        
        //Get current view names
        $certified_db['view_names'] = $this->get_view_names($current_database);        
        //Get current table count        
        $certified_db['table_count'] = $certified_db['table_names']['counts']['table_count']; 
        //Get current view count
        $certified_db['view_count'] = $certified_db['view_names']['view_count'];                       
        //Get current total index count
        $certified_db['total_index_count'] = $certified_db['table_names']['counts']['index_total']; 
        //Assign current db as source db
        $certified_db['source_db'] = $current_database;
        //Write database object to db table 
        $this->save_db_config(str_replace("'","^",serialize($certified_db)));
        
        return $certified_db;
    }
    
    public function save_db_config($data){
        $version = $_POST['version'];
        $sql = "INSERT INTO `db_config`
                   (`dbconfig`, `version`)
                   VALUES
                   ('".$data."', $version)"; 
        
        $this->db->query($sql);                
    }
    
    public function create_db_file($d){
        $this->load->helper('file');
        
        if ( ! write_file(APPPATH.'/modules/Dbmanager/db_config.txt', $d)){
                return 0;
        }else{
                return 1;
        }      
    }
    
    public function get_all_db_config(){        
        $sql = "SELECT 
                    id,
                    timestamp,
                    ifnull(version, '') as version,
                    status
                FROM db_config";
        $c = $this->db->query($sql);        
        $a = array();
        
        foreach ($c->result_array() as $value) {
            $b = array();
            $b['timestamp'] = $value['timestamp'];
            $b['version'] = $value['version'];
            $b['status'] = $value['status'];            
            $a[$value['id']] = $b;
        }
          
        return $a;      
    }
           
    public function check_if_dbconfig(){
        $r2 = array();
        $c = $this->db->query("show tables");
        $r = $c->result_array();
                
        foreach ($r as $key => $value) {
            $r2[] = array_shift($value);
        }
               
        if(in_array('db_config', $r2)){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    public function create_new_dbconfig(){
        $sql = "CREATE TABLE `db_config` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `dbconfig` longblob,
                `version` varchar(45) DEFAULT NULL,
                `status` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;;";
        $this->db->query($sql);
    }
    
    public function read_db_config(){
        $this->load->helper('file');        
        $repo_db = str_replace("^","'",unserialize(read_file(APPPATH.'/modules/Dbmanager/views/db_config.txt')));        
        return $repo_db;
    }
            
    public function insert_base_dbconfig($data){
        $version = 4.6;
        $sql = "INSERT INTO `db_config`
                   (`dbconfig`, `version`)
                   VALUES
                   ('".str_replace("'","^",serialize($data))."', $version)";            
        $this->db->query($sql);                 
    }
    
    public function get_base_db_config(){        
        if($this->check_if_dbconfig()){             
            $sql = "SELECT dbconfig FROM db_config where status = 1";
            
            $c = $this->db->query($sql);
            $c1 = $c->result_array();            
            $c2 = array_shift($c1);            
            $_SESSION['base_db_obj'] = str_replace("^","'",unserialize($c2['dbconfig']));
        }else{             
            $this->create_new_dbconfig();
            $new_dbconfig_record = $this->read_db_config();
            //Add blob data to new db_config table
            $this->insert_base_dbconfig($new_dbconfig_record);
            //Store in session
            $_SESSION['base_db_obj'] = $new_dbconfig_record;
        } 
        
        return $_SESSION['base_db_obj'];      
    }
    
    public function update_db_record(){
        $x = array();      
      
        foreach ($_POST as $key => $value) {
            if(isset($_POST['version'])){
                if($_POST['version'] != NULL){
                    $x['version'] = $_POST['version'];
                }
            }
            
            if(isset($_POST['status'])){
                $this->reset_all_db_records();
                
                if($_POST['status'] != NULL){
                    $x['status'] = $_POST['status'];
                }
            }           
        }
                
        $this->db->where('id', $_POST['id']);
        $this->db->update('db_config', $x);               
    }   
        
    public function reset_all_db_records(){       
        $this->db->update('db_config', array('status'=>0));
    }
    
    public function arrayRecursiveDiff($aArray1, $aArray2) {
        if(isset($aArray1) && isset($aArray2)){
            $aReturn = array();

            foreach ($aArray1 as $mKey => $mValue) {
              if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                  $aRecursiveDiff = $this->arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                  if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
                } else {
                  if ($mValue != $aArray2[$mKey]) {
                    $aReturn[$mKey] = $mValue;
                  }
                }
              } else {
                $aReturn[$mKey] = $mValue;
              }
            }
            return $aReturn;
        }        
    }
    
    public function compare_columns($diff, $s, $t){
        $differences = array_keys($diff);
        $source = array_keys($s);
        $target = array_keys($t);
        $results = array();
                        
        foreach ($differences as $tablename) {
            $x = array();
            if(!in_array($tablename, $this->ExcludeTables)){                
                if($this->check_excluded_tables($tablename)){
                    if(isset($s[$tablename]) && isset($t[$tablename])){
                        $x = array_diff($s[$tablename] + $t[$tablename], array_intersect($s[$tablename], $t[$tablename]));
                    }                    
                }                                
            }            
            
            if(!empty($x)){                
                $results[$tablename] = $x;
            }                 
        }
        
        return $results;
    }
    
    public function compare_columns_to_remove($diff, $s, $t){
        $differences = array_keys($diff);
        $source = array_keys($s);
        $target = array_keys($t);
        $results = array();
        $r = array();
        
        foreach ($differences as $tablename) {            
            if(!in_array($tablename, $this->ExcludeTables)){
                if($this->check_excluded_tables($tablename)){
                    $x = array_diff($s[$tablename] + $t[$tablename], array_intersect($s[$tablename], $t[$tablename]));
                }                
            }            
            
            if(!empty($x)){
                $results[$tablename] = $x;
            }else{
                $results = array();
            }                  
        }
        
        foreach ($results as $tblname => $columnArray) {
            foreach ($columnArray as $column_name) {
                if(in_array($column_name, $t[$tblname])){
                    $r[$tblname][] = $column_name;
                }                  
            }
        }
        
        return $r;
    }
    
    public function get_source_data_types($x){
        $x1 = array();
        
        foreach ($x as $sname) {
            $a = array();
            
            $sql = "SELECT                      
                        TABLE_NAME,
                        COLUMN_NAME,
                        COLUMN_TYPE,
                        IS_NULLABLE,
                        DATA_TYPE
                    FROM
                        INFORMATION_SCHEMA.COLUMNS
                    WHERE
                        TABLE_SCHEMA = '".$this->db->database."'
                            AND TABLE_NAME = '$sname'";
            $c = $this->db->query($sql);
            
            foreach ($c->result_array() as $value) {
                $b = array();
                
                if(!in_array($value['TABLE_NAME'], $this->ExcludeTables)){
                    $b['COLUMN_NAME'] = $value['COLUMN_NAME'];
                    $b['COLUMN_TYPE'] = $value['COLUMN_TYPE'];
                    $b['IS_NULLABLE'] = $value['IS_NULLABLE'];
                    $b['DATA_TYPE'] = $value['DATA_TYPE'];   
                    $a[$value['TABLE_NAME']][$value['COLUMN_NAME']] = $b;
                }                
            }
        
            $x1[$sname] = $a;
        }
        
        return $x1;
    }
           
    public function get_target_data_types($x, $y){
        $this->db->db_select($_POST['dbname']);
        $z = array();
        
        foreach ($y as $tname) {
            $e = array();
            
            $sql = "SELECT                      
                        TABLE_NAME,
                        COLUMN_NAME,
                        COLUMN_TYPE,
                        IS_NULLABLE,
                        DATA_TYPE
                    FROM
                        INFORMATION_SCHEMA.COLUMNS
                    WHERE
                        TABLE_SCHEMA = '$x'
                            AND TABLE_NAME = '$tname'";
            $c = $this->db->query($sql);
            
            foreach ($c->result_array() as $value) {
                $b = array();
                if($value['TABLE_NAME'] != 'piwik_visits'){
                    $b['COLUMN_NAME'] = $value['COLUMN_NAME'];
                    $b['COLUMN_TYPE'] = $value['COLUMN_TYPE'];
                    $b['IS_NULLABLE'] = $value['IS_NULLABLE'];
                    $b['DATA_TYPE'] = $value['DATA_TYPE'];   
                    $e[$value['TABLE_NAME']][$value['COLUMN_NAME']] = $b;
                }                
            }
            $z[$tname] = $e;
        }
        
        return $z;
    }
    
    public function compare_data_types($d, $s, $t){        
        $output = array();        
        $diff = $this->arrayRecursiveDiff($this->get_target_data_types($d, $t), $s);  
        
        foreach ($diff as $tablename => $value) {
            if($this->check_excluded_tables($tablename)){
                $output[$tablename] = $value;
            }
        }
                        
        return $output;   
    }
    
    /******************Update & Repair Db Routines*********************/
    
    public function add_new_table($t){
        $_SESSION['audit'] = array();
        $db_log = '';
        $et = array();
        $this->db->db_select($_POST['dbname']);
        $this->load->dbforge();
        $fields = $this->create_fields($t['columns']);        
        $this->dbforge->add_field($fields);
        if(!in_array($t['name'], $et)){
            $this->dbforge->create_table($t['name'], TRUE);
            $db_log = 'Add table: '.$t['name'];
        }
        $_SESSION['audit'][] = $db_log;
        return $db_log;
    }
            
    public function create_fields($f){
        $f1 = array();
        
        foreach ($f as $fieldname => $value) {            
            $f1[$fieldname] = array(
                'type'=>$value['column_type'],
                'default'=>$value['column_default'],
                'null'=>$value['is_nullable'],
                'extra'=>$value['extra']
            ); 
        }
                       
        return $f1;
    }

    public function rename_table($t){
        $db_log = '';
        $this->db->db_select($_POST['dbname']);
        $this->load->dbforge();    
        $date = new DateTime();
        $ti = $date->getTimestamp();           
        $this->dbforge->rename_table($t, 'delete_'.$t.'_'.$ti);
        $db_log = 'Renamed table: '.$t;
        $_SESSION['audit'][] = $db_log;
        return $db_log;
    }
    
    public function remove_columns($table_name, $column_to_drop){        
        $this->db->db_select($_POST['dbname']);
        $this->load->dbforge();
        $this->dbforge->drop_column($table_name, $column_to_drop);
        $_SESSION['audit'][] = 'Drop columns:'.$column_to_drop.' from table '.$table_name;
        return $table_name.':'.$column_to_drop;
    }
    
    public function modify_column($t, $c, $f, $s){        
        $this->db->db_select($_POST['dbname']);
        $tablename = $t;
        $column_name = $c;   
        $f1 = array_keys($f);
        $ftype = array_shift($f1);
        
        if($ftype == 'IS_NULLABLE'){            
            if($f['IS_NULLABLE'] == 'NO'){
                $column_definition = ' '.$s["column_type"].' NOT NULL ';
            }else{
                $column_definition = ' '.$s["column_type"].' NULL ';
            }
            $sql = "ALTER TABLE $tablename MODIFY `$column_name` $column_definition;"; 
        }elseIF($ftype == 'COLUMN_TYPE'){
                $column_definition = ' '.$s["column_type"].' ';
                $sql = "ALTER TABLE $tablename MODIFY `$column_name` $column_definition;"; 
        }else{
            $column_definition = '';
        }                   
                
        if(isset($column_definition) && $column_definition != ''){
            $_SESSION['audit'][] = $sql;
            $this->db->query($sql);            
            return $sql;
        }        
    }
    
    public function add_column($tablename, $column_name_array, $source_def){    
        $this->db->db_select($_POST['dbname']);
        $columns_added = array();
        
        foreach ($column_name_array as $column_name) {
            //echo $tablename.'-'.$column_name.'<br>';
            if(isset($source_def['table_names']['tables'][$tablename]['columns'][$column_name])){
                $s = $source_def['table_names']['tables'][$tablename]['columns'][$column_name];
                if(isset($s['is_nullable'])){
                    if($s['is_nullable'] == 'NO'){
                        $null_clause = ' NOT NULL ';
                    }else{
                        $null_clause = ' NULL ';
                    }
                }

                if(isset($s['column_default']) && !empty($s['column_default'])){
                    if($s['data_type'] == 'varchar'){
                        $default_clause = ' DEFAULT '.' "'.$s['column_default'].'" ';
                    }else{
                        $default_clause = ' DEFAULT '.$s['column_default'];
                    }                
                }else{
                    $default_clause = '';
                }

                $sql = "ALTER TABLE $tablename ADD `$column_name` ".$s['column_type'].$null_clause." ".$default_clause.' '.$s['extra'].";";
                $_SESSION['audit'][] = $sql;
                $this->db->query($sql);            
                $columns_added[] = $sql;
            }else{
                $columns_added[] = 'Skipped '.$tablename.':'.$column_name;
            }
            
        }
        
        return $columns_added;
    }
    
    public function add_indexes($t, $i){
        $this->db->db_select($_POST['dbname']);
        $k = array();
        if(isset($i['key_list'])){
            $keys = $i['key_list'];
            $indexes_added = array();

            foreach ($keys as $value) {
                foreach ($value as $keyType => $value2) {
                    foreach ($value2 as $key_name =>$value3) {
                        $k[$keyType][$key_name] = $value3;
                    }                
                }
            }        

            foreach ($k as $index_name => $col_array) {            
                $idx = '';
                $n = '';
                $c1 = array();

                foreach ($col_array as $key => $value) {
                    if($key == 'column_name'){
                        foreach ($value as $index_columns) {
                            $c1[] = $index_columns;
                        }                    
                    }               
                }

                $column_list = implode(',', $c1);

                if($index_name == 'PRIMARY'){
                    $idx = ' PRIMARY KEY ';
                    $index_name = '';
                    $sql = "Skipping Add on Primary Key";                    
                    $_SESSION['primary_keys'][$t] = $col_array['column_name'];
                }elseif($k[$index_name]['non_unique'] == 0){
                    $idx = ' UNIQUE ';
                    $sql = "ALTER TABLE $t ADD $idx $index_name ($column_list)";
                    $_SESSION['audit'][] = $sql;
                    $this->db->query($sql);  
                }elseif($index_name != 'PRIMARY' && $k[$index_name]['non_unique'] == 1){
                    $idx = ' INDEX ';
                    $sql = "ALTER TABLE $t ADD $idx $index_name ($column_list)";
                    $_SESSION['audit'][] = $sql;
                    $this->db->query($sql);  
                }                         

                $indexes_added[] = $sql;
            }
        }else{
                $indexes_added[] = 'No key found';
        }        
        
        return $indexes_added;
    }
    
    public function drop_indexes($t, $i){
        $this->db->db_select($_POST['dbname']);
        $k = array();
        if(isset($i['key_list'])){
            $keys = $i['key_list'];
            $indexes_dropped = array();

            foreach ($keys as $value) {
                foreach ($value as $keyType => $value2) {
                    $k[$keyType] = array_shift($value2);
                }
            }

            foreach ($k as $index_name => $value) {
                if($this->check_index_exists($t, $index_name)){
                    if($index_name == 'PRIMARY'){                    
                        $sql = "Skip Primary Key";
                    }else{
                        $sql = "ALTER TABLE $t DROP INDEX $index_name";
                        $_SESSION['audit'][] = $sql;
                        $this->db->query($sql); 
                    }

                    $indexes_dropped[] = $sql;
                }            
            }
        }else{
            $indexes_dropped[] = 'No keys found';
        }        
        
        return $indexes_dropped;
    }
    
    public function check_index_exists($t, $k){
        $this->db->db_select($_POST['dbname']);        
        $sql = "show index from $t";
        
        $c = $this->db->query($sql);
        $count = $c->num_rows();
        if(isset($count) && $count > 0){
            foreach ($c->result_array() as $row){
                if($k == $row['Key_name']){
                    return true;
                }
            }
            return false;
        }else{
            return false;
        }
    }
    
    public function update_ai($x){
        $this->db->db_select($_POST['dbname']);
        $ai = array();
        
        foreach ($x as $tn => $value) {
            if(!$this->check_index_exists($tn, 'PRIMARY')){
                $sql = "ALTER TABLE `$tn`                     
                    ADD PRIMARY KEY ($value[0]);";               
            }else{
                $sql = "ALTER TABLE `$tn`
                    MODIFY `$value[0]` INT, 
                    DROP PRIMARY KEY, 
                    ADD PRIMARY KEY ($value[0]);";
            }
            
            $ai[] = $sql;
            $this->db->query($sql);
            
            //add back the auto increment            
            $sql = "ALTER TABLE `$tn` MODIFY `$value[0]` INT AUTO_INCREMENT ";
            $ai[] = $sql;
            $this->db->query($sql);
            $n = $this->get_ai_count($tn);
            $sql = "ALTER TABLE `$tn` AUTO_INCREMENT = $n;";
            $ai[] = $sql;
            $this->db->query($sql);
        }
        
        $_SESSION['fixed_primary_keys'] = $ai;
        
        return $ai;
    }
    
    public function get_ai_count($t){
        $this->db->db_select($_POST['dbname']);
        $sql = "SELECT 
                    AUTO_INCREMENT as count
                 FROM
                     information_schema.tables
                 where
                     table_schema = '".$this->db->database."'
                         and table_type = 'base table'
                 and TABLE_NAME = '$t';";
        
        $c = $this->db->query($sql);
        $c1 = $c->result_array();
        $c2 = array_shift($c1);
        if(isset($c2['count']) && !empty($c2['count'])){
            return $c2['count'];
        }else{
            return 0;
        }        
    }
    
    public function get_audit(){
        if(!isset($_SESSION['audit'])){
            $_SESSION['audit'] = '';
        }
        
        if(!isset($_SESSION['primary_keys'])){
            $_SESSION['primary_keys'] = '';
        }
        
        if(!isset($_SESSION['fixed_primary_keys'])){
            $_SESSION['fixed_primary_keys'] = '';
        }
        
        return array($_SESSION['audit'], $_SESSION['primary_keys'], $_SESSION['fixed_primary_keys']);
    }
    
    public function reset_session_data($v){
                
        if(isset($_SESSION['audit']) && !empty($_SESSION['audit'])){
            foreach ($_SESSION['audit'] as $key => $value) {
                unset($_SESSION['audit'][$key]);
            }
        }

        if(isset($_SESSION['primary_keys']) && !empty($_SESSION['primary_keys'])){
            foreach ($_SESSION['primary_keys'] as $key => $value) {
                unset($_SESSION['primary_keys'][$key]);
            }
        }
        
        if(isset($_SESSION['fixed_primary_keys']) && !empty($_SESSION['fixed_primary_keys'])){
            foreach ($_SESSION['fixed_primary_keys'] as $key => $value) {
                unset($_SESSION['fixed_primary_keys'][$key]);
            }
        }
        
        exit(json_encode($v));
    }
}
?>