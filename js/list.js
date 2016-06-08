var prescNode

function _(id){
	return document.getElementById(id);
}

function highlight(id){
	alert(id);
}

function autoCheck(pid, did){
	//��������
	prescNode = _('='+pid);
	var prescNodes = prescNode.childNodes;
	var prescData = [];
	for (var i=0; i<prescNodes.length; ++i){
		var node = prescNodes[i];
		prescData[i] = node.id+'='+node.getAttribute('value');
	}
	
	//ҩƷ����
	drugNode = _('='+did);
	var drugNodes = drugNode.childNodes;
	var drugData = [];
	for (var i=0; i<drugNodes.length; ++i){
		var node = drugNodes[i];
		drugData[i] = node.id+'='+node.getAttribute('value');
	}
	
	//����ҩƷ����
	drugsNode = _(pid+'.names');
	
	
	//��ȡ���
	var data = prescData.join('&')+'&'+drugData.join('&')+'&'+drugsNode.getAttribute('value');
	$.post('p_assess.php', data, function(data){
		alert(data);
		var result = jQuery.parseJSON(data);

		if (result.EMatch){
			if (result.EMatch == 1){
				_(did+'~EMatch').checked = true;
				_(did+'.EMatch').value = '����ҩƷ����������';
			}else if(result.EMatch == 0){
				_(did+'~EMatch').checked = false;
				_(did+'.EMatch').value = '����ҩƷ����ϲ�����';
			}
		}
		
		if (result.ECombine){
			if (result.ECombine == 0){
				_(did+'~ECombine').checked = false;
				_(did+'.ECombine').value = '����ҩƷ������ҩƷ��ͻ����ͻ���У�';
				for (var j=0; j<result['ECombine:'].length; ++j){
					_(did+'.ECombine').value += result['ECombine:'][j]+'��';
				}
				_(did+'.ECombine').value += '��';
			}else if(result.ECombine == 1){
				_(did+'~ECombine').checked = true;
				_(did+'.ECombine').value = '����ҩƷ������ҩƷû�г�ͻ��';
				
			}
		}
		
		if (result.EDosage){
			if (result.EDosage == 0){
				_(did+'~EDosage').checked = false;
				_(did+'.EDosage').value = '����ҩƷ�ļ����ڷ�Χ֮�⣬ӦΪ��'+result['EDosage:'][0]+' ~ '+result['EDosage:'][1]+'��';
			}else if(result.EDosage == 1){
				_(did+'~EDosage').checked = true;
				_(did+'.EDosage').value = '����ҩƷ�ļ����ں��ʵķ�Χ�ڡ�';
				
			}
		}
		
		if (result.EFrequency){
			if (result.EFrequency == 0){
				_(did+'~EFrequency').checked = false;
				_(did+'.EFrequency').value = '����ҩƷ��Ƶ���ڷ�Χ֮�⣬ӦΪ��'+result['EFrequency:'][0]+' ~ '+result['EFrequency:'][1]+'��';
			}else if(result.EFrequency == 1){
				_(did+'~EFrequency').checked = true;
				_(did+'.EFrequency').value = '����ҩƷ��Ƶ���ں��ʵķ�Χ�ڡ�';
				
			}
		}
		
		if (result.EMethod){
			if (result.EDosage == 0){
				_(did+'~EDosage').checked = false;
				_(did+'.EDosage').value += '����ҩƷ�ĸ�ҩ��ʽ����ȷ��ӦΪ����֮һ��'+result['EMethod:'].join('��')+'��';
			}else if(result.EDosage == 1){
				//_(did+'~EDosage').checked = true;
				_(did+'.EDosage').value += '����ҩƷ�ĸ�ҩ��ʽ��ȷ��';
				
			}
		}
	});
}
