function drugAdd(id){
	var drug = document.getElementById('drug_'+id);
	drug.setAttribute('onclick','drugRemove("'+id+'")');
	var drugStr = drug.outerHTML;
	drug.outerHTML = '';
	
	var drugInGroup = document.getElementById('drug_in_group');
	drugInGroup.innerHTML += drugStr;
}

function drugRemove(id){
	var drug = document.getElementById('drug_'+id);
	drug.setAttribute('onclick','drugAdd("'+id+'")');
	var drugStr = drug.outerHTML;
	drug.outerHTML = '';
	
	var drugOutGroup = document.getElementById('drug_out_group');
	drugOutGroup.innerHTML += drugStr;
}

function submitDrugGroupMeta(formDrugs){
	formDrugs.innerHTML += '<input type="hidden" name="name" value="'+document.getElementById('group_name').value+'" />';
	formDrugs.innerHTML += '<input type="hidden" name="notation" value="'+document.getElementById('group_notation').value+'" />';
	formDrugs.innerHTML += '<input type="hidden" name="memo" value="'+document.getElementById('group_memo').value+'" />';
}

function submitDrugGroup0(){
	var drugInGroup = document.getElementById('drug_in_group');
	var drugs = drugInGroup.childNodes;
	var formDrugs = document.getElementById('form_drugs');
	formDrugs.innerHTML = '';
	
	for (var i=0; i<drugs.length; ++i){
		var drug = drugs[i];
		if (drug.getAttribute('value'))
			formDrugs.innerHTML += '<input type="hidden" name="drugs[]" value="'+drug.getAttribute('value')+'" />';
	}
	
	submitDrugGroupMeta(formDrugs);
	document.getElementById('my_form').submit();
}

function submitDrugGroup1(){
	var formDrugs = document.getElementById('form_drugs');
	formDrugs.innerHTML = '';
	
	formDrugs.innerHTML += '<input type="hidden" name="column" value="'+document.getElementById('group_column').value+'" />';
	var conditions = (document.getElementById('group_condition').value).split(' ');
	for (var i=0; i<conditions.length; ++i){
		formDrugs.innerHTML += '<input type="hidden" name="conditions[]" value="'+conditions[i]+'" />';
	}
	
	submitDrugGroupMeta(formDrugs);
	document.getElementById('my_form').submit();
}

submitDrugGroup2 = submitDrugGroup0;

function submitDrugGroupRemove(){
	if (confirm('ÄúÈ·¶¨ÒªÉ¾³ýÂð£¿'))
		document.getElementById('my_form_remove').submit();
}


function appendTo(tagid, value){
	$('#'+tagid)[0].value += ' ' + value;
}

function loadDrugGroups(){
	$.post('p_drugs.php?type=getGroups', '', function(meta){
		var result = jQuery.parseJSON(meta);
		if (result.status == 1){
			var t_list = $('#t_list')[0];
			t_list.innerHTML = '';
			
			var data = result.data;
			for (var i=0; i<data.length; ++i){
				var d = data[i];
				t_list.innerHTML += "<li onclick=\"appendTo('group_column','"+d.notation+"')\">"+d.name+'('+d.notation+')</li>';
			}
		}
	});
}

function loadDrugs(){
	if (!$('#config_drugs_show')[0].checked){
		$.post('p_drugs.php?type=getDrugs', '', function(meta){
			var result = jQuery.parseJSON(meta);
			var column = $('#group_column')[0].value;
			if (result.status == 1){
				var t_list = $('#t_list')[0];
				t_list.innerHTML = '';
				
				var data = result.data;
				for (var i=0; i<data.length; ++i){
					var d = data[i];
					var value = d[column];
					var label = (!d[column] || column=='name') ? '' : '('+d[column]+')';
					t_list.innerHTML += "<li onclick=\"appendTo('group_condition','"+value+"')\">"+d.name+' <x class="mark">'+label+'</x></li>';
				}
			}
		});
	}else{
		var column = $('#group_column')[0].value;
		$.post('p_drugs.php?type=getDrugProperties', 'column='+column, function(meta){
			var result = jQuery.parseJSON(meta);
			if (result.status == 1){
				var t_list = $('#t_list')[0];
				t_list.innerHTML = '';
				var data = result.data;
				for (var i=0; i<data.length; ++i){
					var d = data[i];
					t_list.innerHTML += '<li>'+d+'</li>';
				}
			}
		});
	}
}