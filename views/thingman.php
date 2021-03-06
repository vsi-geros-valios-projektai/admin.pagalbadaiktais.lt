<?php if($login->isUserLoggedIn() == false) return; if(!isGridManager()) return;

if(!subpage()) return;

if(isAction('atkurti') and getParam()) :
	
	$children = array();
	foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'needy.user_parent = '.$child['user_id'];
	$parent = '(('.implode(' OR ', $children).') OR needy.user_parent_ta = '.CUSER.')';
	if(countData(false, false, "SELECT * FROM needs INNER JOIN needy ON needs.need_needy = needy.user_id WHERE needs.need_id = ".getParam()." AND ".$parent) == 0) {err('Neturite teisės keisti šio poreikio duomenis', 'red'); return;}
	
	$row = getRow('needs', 'need_id = '.getParam());
	$act = $row['need_full'];
	$act = !$act;
	updateFieldWhere('needs', 'need_full', $act, 'need_id = '.getParam());

	redirect(0, getCurrentLink());
endif;

$tablefields = array(
		'need_id',
		'need_name',
		'need_full_desc',
		'need_regdate',
		array('converter' => array('need_id', 'getThingEditLink')),
		array('action' => array('need_id', 'atkurti', '<i title="Patenkintą poreikį padaryti nepatenkintu ir grąžinti į Poreikių lentelę" class="fa fa-undo"></i>', false)),
		/*array('action' => array('need_id', 'salinti', '<i title="Šalinti" class="fa fa-times"></i>', false)),*/
		'null',
	);
	$tablehead = array(
		'titles' => array(
		'ID',
		'Pavadinimas',
		'Aprašymas',
		'Modifikuotas',
		'',
		'',
		/*'',*/
		'',
		),
		'columns' => array(
		'right fc-60',
		'left fc-150',
		'left fc-300',
		'left fc-dates',
		'left fc-16',
		'left fc-16',
		/*'left fc-16',*/
		'',
		),
		
	);

	$children = array();
	foreach(listData('users', 'user_acctype = 1 AND user_active = 1 AND user_parent = '.CUSER) as $child) $children[] = 'needy.user_parent = '.$child['user_id'];
	$parent = 'AND (('.implode(' OR ', $children).') OR needy.user_parent_ta = '.CUSER.')';

	
	getSort('need_id');
	$where = 'SELECT need_full_desc, need_needy, need_id, need_name, cat_name, user_fname, user_lname, need_regdate, a.deleted AS deleted FROM (SELECT need_full_desc, need_id, need_name, cat_name, need_type, need_needy, need_regdate, needs.deleted AS deleted FROM needs INNER JOIN cats ON needs.need_cat = cats.cat_id WHERE needs.need_type='.subpage().' AND needs.need_full=1) a INNER JOIN needy ON a.need_needy = needy.user_id WHERE a.need_type='.subpage().' '.$parent.' ORDER BY '.$getsort.$getorder;

	
	
	$sortby = array(0 => 'need_id', 1 => 'need_name', 3 => 'need_regdate');
	
	formatTable( listData(false, false, pageNum(), $where), $tablefields, $tablehead, $sortby, true, 'nomargin');
	pagination(countData(false, false, $where));
?>
