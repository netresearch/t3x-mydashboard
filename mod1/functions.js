function setUpdate(obj){
	if(!$(obj).hasClassName('ajax-update'))
		$(obj).toggleClassName('ajax-update');
}

function unsetUpdate(obj){
	if($(obj).hasClassName('ajax-update'))
		$(obj).toggleClassName('ajax-update');
}


/* refresh the Widget */
function ajaxWidgetParms(keyval, actionkey){
	setUpdate('widget_'+keyval);
	new Ajax.Updater('widget_'+keyval+'_content', 'index.php', {
		parameters: { ajax: 1, key: keyval, action: actionkey },
		onComplete: function(){ unsetUpdate('widget_'+keyval); }
	});
} 

function refreshWidget(keyval){
	ajaxWidgetParms(keyval, 'refresh');
}

function configWidget(keyval){
	ajaxWidgetParms(keyval, 'config');
}

function ajaxWidgetParmsValue(keyval, actionkey, valuekey){
	setUpdate('widget_'+keyval);
	new Ajax.Updater('widget_'+keyval+'_content', 'index.php', {
		parameters: { ajax: 1, key: keyval, action: actionkey , value: valuekey },
		onComplete: function(){ unsetUpdate('widget_'+keyval); }
	});
}

function sendConfForm(dashKey){
	var parms = $(dashKey+'_confform').serialize();
	ajaxWidgetParmsValue(dashKey, 'refresh_config', parms);
}

function deleteWidget(keyval){
	setUpdate('widget_'+keyval);
	new Ajax.Updater('widget_'+keyval, 'index.php', {
		parameters: { ajax: 1, key: keyval, action: 'delete' },
		onComplete: function(){ 
			$('widget_'+keyval).remove();
		}
	});
}

/* Show the Widget Options */
function showOptions(widgetKey){
	if($(widgetKey+'_config')) $(widgetKey+'_config').setStyle({display: 'block'});
	if($(widgetKey+'_refresh')) $(widgetKey+'_refresh').setStyle({display: 'block'});
	if($(widgetKey+'_delete')) $(widgetKey+'_delete').setStyle({display: 'block'});
}

/* Hide the Widget Options */
function hideOptions(widgetKey){
	if($(widgetKey+'_config')) $(widgetKey+'_config').setStyle({display: 'none'});
	if($(widgetKey+'_refresh')) $(widgetKey+'_refresh').setStyle({display: 'none'});
	if($(widgetKey+'_delete')) $(widgetKey+'_delete').setStyle({display: 'none'});
}