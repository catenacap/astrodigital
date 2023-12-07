<?php


require_once('/home/xxx/public_html/m_db_conf.inc');

define('DB_SERVER1', 'xxx'); 
define('DB_SERVER4', DB_SERVER1); 

define('DB_USERNAME','xxx_db1'); 
define('DB_PASSWORD', M_DB_PASSWORD); 

define('DB_NAME','xxx_db1');
define('DB_NAME1_f','xxx_db1_f');
define('DB_NAME1_g','xxx_db1_g');
define('DB_NAME1_h','xxx_db1_h');
define('DB_NAME1_i','xxx_db1_i');
define('DB_NAME1_j','xxx_db1_j');
define('DB_NAME1_k','xxx_db1_k');
define('DB_NAME1_l','xxx_db1_l');

define('DB_NAME4','xxx_db4');
define('DB_NAME4_c','xxx_db4_c');

$db = new mysqli(DB_SERVER1, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($db->connect_errno) {
	echo "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error;
	exit;
}

$db1_f = new mysqli(DB_SERVER1, DB_USERNAME, DB_PASSWORD, DB_NAME1_f);

if($db1_f->connect_errno) {
	echo "DB1_f Failed to connect to MySQL: (" . $db1_f->connect_errno . ") " . $db1_f->connect_error;
	exit;
}

$db1_g = new mysqli(DB_SERVER1, DB_USERNAME, DB_PASSWORD, DB_NAME1_g);

if($db1_g->connect_errno) {
	echo "DB1_g Failed to connect to MySQL: (" . $db1_g->connect_errno . ") " . $db1_g->connect_error;
	exit;
}

$db1_h = new mysqli(DB_SERVER1, DB_USERNAME, DB_PASSWORD, DB_NAME1_h);

if($db1_h->connect_errno) {
	echo "DB1_h Failed to connect to MySQL: (" . $db1_h->connect_errno . ") " . $db1_h->connect_error;
	exit;
}

$db1_i = new mysqli(DB_SERVER1, DB_USERNAME, DB_PASSWORD, DB_NAME1_i);

if($db1_i->connect_errno) {
	echo "DB1_i Failed to connect to MySQL: (" . $db1_i->connect_errno . ") " . $db1_i->connect_error;
	exit;
}

$db1_j = new mysqli(DB_SERVER1, DB_USERNAME, DB_PASSWORD, DB_NAME1_j);

if($db1_j->connect_errno) {
	echo "DB1_j Failed to connect to MySQL: (" . $db1_j->connect_errno . ") " . $db1_j->connect_error;
	exit;
}

$db1_k = new mysqli(DB_SERVER1, DB_USERNAME, DB_PASSWORD, DB_NAME1_k);

if($db1_k->connect_errno) {
	echo "DB1_k Failed to connect to MySQL: (" . $db1_k->connect_errno . ") " . $db1_k->connect_error;
	exit;
}

$db1_l = new mysqli(DB_SERVER1, DB_USERNAME, DB_PASSWORD, DB_NAME1_l);

if($db1_l->connect_errno) {
	echo "DB1_j Failed to connect to MySQL: (" . $db1_l->connect_errno . ") " . $db1_l->connect_error;
	exit;
}

$db4 = new mysqli(DB_SERVER4, DB_USERNAME, DB_PASSWORD, DB_NAME4);

if($db4->connect_errno) {
	echo "Failed to connect to MySQL: (" . $db4->connect_errno . ") " . $db4->connect_error;
	exit;
}

$db4_c = new mysqli(DB_SERVER4, DB_USERNAME, DB_PASSWORD, DB_NAME4_c);

if($db4_c->connect_errno) {
	echo "Failed to connect to MySQL: (" . $db4_c->connect_errno . ") " . $db4_c->connect_error;
	exit;
}

?>