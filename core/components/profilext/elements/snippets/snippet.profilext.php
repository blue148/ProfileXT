<?php
/***********
/***Created by jason@dvstudiosinc.com
/***This is a helper script (posthook) for Login.Register
/***The goal is to take any Extended Field info and put it in
/***a custom table to increase search performance
/***Its intention is for very large Extended Profile strings
/***After the Registration script runs, ProfileXT will clear the Extended field
/***Of the newly created profile, and put that data in pxt_values table, relating
/***it to the user id and the catalog id (field name from the form)
/***@package profilext
/******/

$path = MODX_CORE_PATH . 'components/profilext/';
$result = $modx->addPackage('profilext',$path . 'model/','pxt_');
if (! $result) return 'failed to add package';

/***Get fields from the submitted form***/
$form = $hook->getValues();
/***Detect if the Register call has actiavtion turned on or off. This will let us know if we need to block the new user or not***/
$actOn = $modx->getOption('activation', $scriptProperties,'');

/***Switch to detect if this is the Login.Register snippet or the Login.UpdateProfile snippet***/
if($hook->getValue('updateprofile.profile')){
    $user = $hook->getValue('updateprofile.profile');
    $newuser = false;
    $userfields = $user->toArray();
}else{
    $user = $hook->getValue('register.profile');

}
if(empty($user))die;

$userArr = $user->toArray();

/***Existing Catalog entries. Don't add new one if it already exists***/
$pxtCat = $modx->getCollection('pxtCatalog');
//convert child objects to array for diffing
foreach($pxtCat as $cat){
    $exist[$cat->get('name')]=$cat->get('name');
}
if(empty($exist) || count($exist)===0)$exist[]='';
if(empty($userArr['extended']))$userArr['extended']='';
//find the unique items by comparing the Extended fields array from register.user to pxtCat array
$uniqueCat= array_diff(array_keys($userArr['extended']), $exist);
//write unique items to Catalog
if(count($uniqueCat)>0){
    foreach($uniqueCat as $name=>$item){
        $uci = $modx->newObject('pxtCatalog', array('name'=>$item));
        $uci->save();
    }
}

//Login.Register saves all non-standard fields to the 'extended' array. 
//Loop thru this array and save each value to the ProfileXT Values table IF it has a reference in the Catalog table
foreach($userArr['extended'] as $ukey=>$uval){
    if(is_array($uval)){
        $uval = json_encode($uval);
    }
    if(empty($uval))continue;
    $pxcid = $modx->getObject('pxtCatalog', array('name'=>$ukey));
    $pval = $modx->newObject('pxtValues', array('pxt_id'=>$pxcid->get('id'), 'user_id'=>$userArr['internalKey'], 'value'=>$uval));
    $pval->save();
}
/***get the newly created user profile, clear extended fields, set to blocked if Register.activation is set to 0. Since this is a new user, there should be nothing in extended****/
$fn = $modx->getObject('modUserProfile', array('internalKey'=>$userArr['internalKey']));
if(empty($actOn)){
    $fn->set('blocked', 1);
}
$fn->set('extended', array());
$fn->save();
return;