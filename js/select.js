function _(id){
	return document.getElementById(id);
}

var task_ = _('task_');
task_.onchange = function(){
	if (task_.value > '0'){
		_('sections_filters').style.display = 'none';
	}else{
		_('sections_filters').style.display = 'block';
	}
	if (task_.value == '0' || task_.value == '-2'){
		_('subsection_taskNum').style.display = 'inline';
	}else{
		_('subsection_taskNum').style.display = 'none';
	}
	if (task_.value == '-2'){
		_('subsection_tasksId').style.display = 'inline';
		autofill();
	}else{
		_('subsection_tasksId').style.display = 'none';
	}
}

_('mode_normal').onclick = function(){
	_('task_assign').disabled = 'disabled';
	_('task_old').disabled = 'disabled';
}
_('mode_chief').onclick = function(){
	_('task_assign').disabled = '';
	_('task_old').disabled = '';
}

function autofill(){
	var tasksId_ = _('tasksId_');
	var index = tasksId_.selectedIndex;
	var option = document.getElementsByClassName('option_tasks')[index];
	var params = option.getAttribute('parameters').split(',');
	
	
	_('dateFrom_').value = params[0];
	_('dateTo_').value = params[1];
	
	_('limit_').value = params[2];
	_('limit2_').value = params[3];
	_('limit3_').value = params[4];
	
	_('taskNum_').value = params[5];
	
	switch (params[6]){
		case '' : _('type_all').checked = true; break;
		case 'normal' : _('type_normal').checked = true; break;
		case 'emergency' : _('type_emergency').checked = true; break;
		case 'hospitalized' : _('type_hospitalized').checked = true; break;
	}
	
	var drugBoxes = _('section_drugs').getElementsByTagName('input');
	for (var i=0; i<drugBoxes.length; ++i){
		drugBoxes[i].checked = false;
	}

	var drugs = option.getAttribute('drugs').split(',');
	for (var i=0; i<drugs.length; ++i){
		_('drug_'+drugs[i]).checked = true;
	}
}
