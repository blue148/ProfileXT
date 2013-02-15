<?php
/*********/
/****This is a wrapper for the Login.UpdateProfile snippet
/****This calls the value from the pxt tables and sets them as placeholders for the form
/****Then it calls UpdateProfile, which will fill in the rest of the fields, 
/****including anything that is in the extended fields
/**************/
$path = MODX_CORE_PATH . 'components/profilext/';
$result = $modx->addPackage('profilext',$path . 'model/','pxt_');
if (! $result) return 'failed to add package';

/*********/
/***Most of the properties are the same as UpdateProfile
/***We grab these to pass to the snippet call later
/********/
$profileAtts['placeholderPrefix']=$modx->getOption('placeholderPrefix', $scriptProperties, '');
$profileAtts['submitVar'] = $modx->getOption('submitVar', $scriptProperties, 'login-updprof-btn');
$profileAtts['postHooks']='ProfileXT';

/****Kill this if the user is not logged into the web context.
/****You have to be authenticated to update your profile
/***********/
if(!($modx->user->hasSessionContext('web')))return;

$user = $modx->getObjectGraph('modUser', array('Profile'=>array()), array('id'=>$modx->user->get('id')));
$pxtv = $modx->getCollection('pxtValues', array('user_id'=>$user->get('id')));

foreach($pxtv as $key=>$val){
		//get the catalog item name
		$pxtName = $modx->getObject('pxtCatalog', array('id'=>$val->get('pxt_id')));
		//Some fields contain JSON strings for compactness. 
		//Attempt to convert the value to array form JSON
		$cleanval = $modx->fromJSON($val->get('value'));

		//Check if the new value is an array. If not, use the raw value
		if(count($cleanval)>0){
			 $pxtFields[$pxtName->get('name')]=$cleanval;
			} else {
					$pxtFields[$pxtName->get('name')]=$val->get('value');
			}

}
///set the placeholders for the form fields. 
$fields = $modx->toPlaceholders($pxtFields, $profileAtts['placeholderPrefix']);

///run UpdateProfile snippet. This will not only populate the rest of the form, but on page submit, will update the profile.
///This will also run the ProfileXT snippet, which will update the user's PXT tables

$modx->runSnippet('UpdateProfile', $profileAtts);
return;