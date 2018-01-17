<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Database manager main controller
 * @author Rance Aaron
 * @package Modules\Dbmanager
 */
class Dbmanager extends MX_Controller {
        
public $add = array();
    public $remove = array();
    public $modify = array();    
    public $dbupdates = array();
            
    public function __construct(){
            parent::__construct();
            $this->load->helper(array('url', 'html'));
            $this->load->library('session');
            $this->load->database();
            $this->load->model('dbmanager_model');
    }

    public function index(){       
        if(isset($_POST['process']) && $_POST['process'] == 'upgrade'){            
            $this->load->view('dbmanager_view2');            
        }else{            
            $this->load->view('dbmanager_view');
        }
    }
    
    public function getCurrentDB(){
        $this->load->model('dbmanager_model');
        return $this->dbmanager_model->get_current_db();
    }
            
    public function setUpgradeFlag($f){
        $this->upgrade_flag = $f;
    }
    
    public function getUpgradeFlag(){
        return $this->upgrade_flag;
    }

    public function getTableNames(){
        $this->load->model('dbmanager_model');        
        $names = $this->dbmanager_model->get_table_names($this->getCurrentDB());
        exit(json_encode($names));
    }
    
    public function getViewNames(){        
        $this->load->model('dbmanager_model');
        $names = $this->dbmanager_model->get_view_names($this->getCurrentDB());
        exit(json_encode($names));
    }
    
    public function getColumnNames(){
        $this->load->model('dbmanager_model');
        $names = $this->dbmanager_model->get_column_names();
        exit(json_encode($names));
    }
    
    public function getTableCount(){
        $this->load->model('dbmanager_model');
        $count = $this->dbmanager_model->get_table_count();
        exit(json_encode($count));
    }
    
    public function getViewCount(){
        $this->load->model('dbmanager_model');
        $count = $this->dbmanager_model->get_view_count();
        exit(json_encode($count));
    }
    
    public function getColumnKeys(){
        $this->load->model('dbmanager_model');
        $ck = $this->dbmanager_model->get_column_keys();
        exit(json_encode($ck));
    }
    
    public function getTableKeys(){
        $this->load->model('dbmanager_model');
        $tk = $this->dbmanager_model->get_table_keys();
        exit(json_encode($tk));
    }
    
    public function getViewDetails(){
        $this->load->model('dbmanager_model');
        $vdet = $this->dbmanager_model->get_view_details();
        exit(json_encode($vdet));
    }
    
    public function certifyDb(){
        $this->load->model('dbmanager_model');
        $cd = $this->dbmanager_model->certify_db($_POST['database']);
        exit(json_encode($cd));
    }
    
    public function getAllDbConfig(){        
        $this->load->model('dbmanager_model');
        $adbc = $this->dbmanager_model->get_all_db_config();
        exit(json_encode($adbc));
    }
            
    public function getBaseDbConfig(){
        $this->load->model('dbmanager_model');
        $bdbc = $this->dbmanager_model->get_base_db_config();
        $_SESSION['source_table_keys'] = $bdbc['table_names']['tables'];
        $_SESSION['sourcetablenames'] = array_keys($bdbc['table_names']['tables']);       
        exit(json_encode($bdbc));
    }
                    
    public function updateDbRecord(){
        $this->load->model('dbmanager_model');        
        $this->dbmanager_model->update_db_record();     
        $updated_record_status = $this->dbmanager_model->create_db_file(str_replace("'","^",serialize($this->dbmanager_model->get_base_db_config())));
        exit(json_encode('updated_record_status'));
    }
    
    public function getDbNames(){
        $this->load->model('dbmanager_model');
        $dbnames = $this->dbmanager_model->get_db_names();        
        exit(json_encode($dbnames));
    }
    
    public function compareDb(){
        $table_diff = array();
        $table_diff['add'] = array();        
        $t1 = array(); 
        $tta = array();        
        $tkeys = array('target'=>'');
        $this->load->model('dbmanager_model');
        $_SESSION['dbname'] = $_POST['dbname'];
        
        //Compare table names
        $target_table_names = $this->dbmanager_model->get_listof_tables($_POST['dbname']);      
        $table_diff_add = array_diff($_SESSION['sourcetablenames'], $target_table_names);
        $table_diff['remove'] = array_diff($target_table_names, $_SESSION['sourcetablenames']);
                        
        foreach ($table_diff_add as $value) {           
            if($this->dbmanager_model->check_excluded_tables($value)){                
                $table_diff['add'][] = $value;
            }
        }                               
        
        $this->remove = $table_diff['remove'];       
                                       
        //Compare column names per table
        $source_table_columns = $this->dbmanager_model->get_source_column_names($_SESSION['source_table_keys']);
        
        foreach ($target_table_names as $tn1) {
            if(!in_array($tn1, $table_diff['remove'])){
                $t1[] = $tn1;
            }          
        }                                       
        
        //Add or remove columns
        $table_diff['modifyAdd'][] = $this->compareColsToAdd($_POST['dbname'], $source_table_columns, $t1);
        $table_diff['modifyRemove'][] = $this->compareColsToRemove($_POST['dbname'], $source_table_columns, $t1); 
       
        //Compare column data types
        $table_diff['dataTypes'] = $this->dbmanager_model->compare_data_types($_POST['dbname'], $this->getSrcColDataTypes(), $t1);      
        
        //Compare indexes per table       
        foreach ($t1 as $target_table) {
            $tkeys['target'][$target_table] = $this->dbmanager_model->get_keys_per_table($_POST['dbname'].'.'.$target_table);
        }
        
        if( isset($tkeys['target']) && !empty($tkeys['target'])){            
            $table_diff['keystoupdate'] = $this->compareKeys($_SESSION['source_table_keys'], $tkeys['target']);
        }else{            
            $table_diff['keystoupdate'] = '';
        }
        
        //Compare collations
        
        //Store current database differences in session array
        $_SESSION['db_diff'] = $table_diff;
        $table_diff['dbname'] = $_POST['dbname'];  
        exit(json_encode($table_diff));
    }
    
    public function getSrcColDataTypes(){
        $z = array();
        $y = array();
        $y2 = array();
        $x = $_SESSION['source_table_keys'];
                       
        foreach ($x as $key => $value) {
            $z[$key] = $value['columns'];
        }
        
        foreach ($z as $tablename1 => $value1) {
            foreach ($value1 as $colname => $value2) {
                $w = array();
                $w['COLUMN_NAME'] = $value2['column_name'];
                $w['COLUMN_TYPE'] = $value2['column_type'];
                $w['IS_NULLABLE'] = $value2['is_nullable'];
                $w['DATA_TYPE'] = $value2['data_type'];
                $y[$tablename1][$colname] = $w;
            }
            $y2[$tablename1] = $y;
        }
                                                
        return $y2;
    }
    
    public function compareKeys($s, $t){ 
        $output = array();
        $keyDiff = array_keys($this->dbmanager_model->arrayRecursiveDiff($this->normalize_source($s), $t));
        
        foreach ($keyDiff as $value) {
            if($this->dbmanager_model->check_excluded_tables($value)){
                 $output[] = $value;
            }
        }
        
        return $output; 
    }
    
    public function normalize_source($s){
        $n = array();
        
        foreach ($s as $key => $value) {
            $n[$key]['key_list'] = array_shift($value['keys']);
        }
        
        return $n;
    }
    
    public function compareColsToAdd($database, $s, $t){
        $source = array();
        $target = array();                                 
       
        foreach ($t as $ttable){
            $target[$ttable] = $this->dbmanager_model->get_table_column_names($database, $ttable);
        }                       
        
        $column_diff = $this->dbmanager_model->arrayRecursiveDiff($s, $target);        
        $final_diff = $this->dbmanager_model->compare_columns($column_diff, $s, $target);
        
        return $final_diff;
    }
    
    public function compareColsToRemove($database, $s, $t){
        $source = array();
        $target = array();               
        
        foreach ($t as $ttable){
            $target[$ttable] = $this->dbmanager_model->get_table_column_names($database, $ttable);
        }                       
        
        $column_diff = $this->dbmanager_model->arrayRecursiveDiff($target, $s);        
        $final_diff = $this->dbmanager_model->compare_columns_to_remove($column_diff, $s, $target);
        
        return $final_diff;
    }
    
    public function addTables(){
        $_SESSION['update_results'] = array();
        $this->load->model('dbmanager_model');
        $base_db = $_SESSION['base_db_obj'];        
        $current_db_diff = $_SESSION['db_diff'];
        $tables_added = array();
        
        foreach ($current_db_diff['add'] as $tableToAdd) {
            if(isset($base_db['table_names']['tables'][$tableToAdd])){            
                $tables_added[] = $this->dbmanager_model->add_new_table($base_db['table_names']['tables'][$tableToAdd]);
            }            
        }
        
        $_SESSION['update_results']['tables_added'] = $tables_added;
        
        exit(json_encode($_SESSION['db_diff']));
    }
    
    public function removeTables(){
        $tables_renamed = array();
        
        if(isset($_POST['removelist']) && !empty($_POST['removelist'])){
            foreach ($_POST['removelist'] as $tableToRemove) {            
                $tables_renamed[] = $this->dbmanager_model->rename_table($tableToRemove);
            }
        }        
        
        $_SESSION['update_results']['tables_renamed'] = $tables_renamed;
        
        exit(json_encode($_SESSION['db_diff']));
    }
    
    public function removeColumns(){
        $columns_dropped = array();
        
        if(isset($_POST['removecols'])){
            $tc1 = $_POST['removecols'];
            $tc2 = array_shift($tc1);

            foreach ($tc2 as $tablename => $colarray) {
                foreach ($colarray as $colToRemove) {
                    $columns_dropped[] = $this->dbmanager_model->remove_columns($tablename, $colToRemove);
                }            
            }
        }
        
        $_SESSION['update_results']['columns_dropped'] = $columns_dropped;
        
        exit(json_encode($_SESSION['db_diff']));
    }
    
    public function modifyColumns(){        
        $this->load->model('dbmanager_model');
        $columns_modified = array();
        
        if(isset($_POST['modify']) && !empty($_POST['modify'])){                  
            $columns_to_modify = $_SESSION['db_diff']['dataTypes'];
                               
            foreach ($columns_to_modify as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    $i[$key2] = $value2;
                }
            }
                           
            foreach ($i as $tablename => $colsArray) {               
                if(isset($colsArray) && !empty($colsArray)){                    
                    foreach ($colsArray as $colname => $valueArray) {
                        if(isset($_SESSION['base_db_obj']['table_names']['tables'][$tablename]['columns'][$colname])){
                           print_r($_SESSION['base_db_obj']['table_names']['tables'][$tablename]['columns'][$colname]);
                            $columns_modified[] = $this->dbmanager_model->modify_column($tablename, $colname, $valueArray, $_SESSION['base_db_obj']['table_names']['tables'][$tablename]['columns'][$colname]);
                        }else{
                            $columns_modified[] = 'Skipping '.$tablename.' column: '.$colname;
                        }                        
                    }
                }                                         
            }
        }
        
        $_SESSION['update_results']['columns_modified'] = $columns_modified;
                        
        exit(json_encode($_SESSION['db_diff']));
    }
    
    public function addColumns(){
        $columns_added = array();
        
        if(isset($_POST['modifyAdd']) && !empty($_POST['modifyAdd'])){
            $this->load->model('dbmanager_model');       
            $columns_to_add = array_shift($_POST['modifyAdd']);
            
            foreach ($columns_to_add as $tablename => $columnList) {
                $columns_added[] = $this->dbmanager_model->add_column($tablename, $columnList, $_SESSION['base_db_obj']);
            }
        }
        
        $_SESSION['update_results']['columns_added'] = $columns_added;
                       
        exit(json_encode($_SESSION['db_diff']));
    }
    
    public function updateIndexes(){
        $indexes_updated = array();
        
        $this->load->model('dbmanager_model');
        if(isset($_POST['tablelist']) && !empty($_POST['tablelist'])){
            $tables_to_update = $_POST['tablelist'];
            
            foreach ($tables_to_update as $table_name) {
                if($table_name != 'piwik_visits'){                
                    //Drop indexes
                    $indexes_updated['dropped'][] = $this->dbmanager_model->drop_indexes($table_name, $_SESSION['base_db_obj']['table_names']['tables'][$table_name]['keys']);
                    //Add indexes
                    $indexes_updated['added'][] = $this->dbmanager_model->add_indexes($table_name, $_SESSION['base_db_obj']['table_names']['tables'][$table_name]['keys']);
                }            
            }
            
            $_SESSION['update_results']['updated_indexes'] = $indexes_updated;                        
            $_SESSION['update_results']['updated_auto_increments'] = $this->dbmanager_model->update_ai($_SESSION['primary_keys']);
        }else{
            $_SESSION['update_results']['updated_indexes'] = '';
            $_SESSION['update_results']['updated_auto_increments'] = '';
        }
                        
        exit(json_encode($_SESSION['update_results']));
    }
    
    public function updateViews(){
        $this->load->model('dbmanager_model');
        $source_view_list = array_keys($_SESSION['base_db_obj']['view_names']);
        $updateResults = array();
        $missing_view_list = array();
        
        foreach ($source_view_list as $view_name){
            if($view_name != 'view_count'){
                if($this->checkExcludedViews($view_name)){
                    $view_dep_obj = $this->dbmanager_model->lookup_view_dep($view_name, $_POST['dbname']);
                                                            
                    if($view_dep_obj['vcount'] > 0 && isset($view_dep_obj['dep']['views'])){                        
                        $this->dbmanager_model->lookup_view_dep($view_dep_obj['dep']['views'][0], $_POST['dbname']);
                    }
                }                
            }            
        }

        //compare final view totals
        $mvl = $this->dbmanager_model->compare_views($_SESSION['dbname'], $source_view_list);
        
        foreach ($mvl as $missing_view_name) {
            if($this->checkExcludedViews($missing_view_name)){
                $missing_view_list[] = $missing_view_name;
            }
        }
        
        $updateResults['count'] = count($missing_view_list);
        $updateResults['missing'] = $missing_view_list;
        
        exit(json_encode($updateResults));                              
    }
    
    public function checkExcludedViews($vn){
        if(!preg_match('/mobile_cause_/', $vn) && !preg_match('/mobile_response_/', $vn) && !preg_match('/mobile_responses_/', $vn)){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    public function getAuditLog(){
        exit(json_encode($this->dbmanager_model->get_audit()));
    }
    
    public function clearAuditSessionData(){
        $rootPath = dirname(getcwd());
        include $rootPath.'/application/modules/version/assets/version.php';        
        $this->dbmanager_model->reset_session_data($dmsa_version);
    }   
}
?>
