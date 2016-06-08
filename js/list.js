var prescNode

function _(id){
	return document.getElementById(id);
}

function highlight(id){
	alert(id);
}

function autoCheck(pid, did){
	//处方部分
	prescNode = _('='+pid);
	var prescNodes = prescNode.childNodes;
	var prescData = [];
	for (var i=0; i<prescNodes.length; ++i){
		var node = prescNodes[i];
		prescData[i] = node.id+'='+node.getAttribute('value');
	}
	
	//药品部分
	drugNode = _('='+did);
	var drugNodes = drugNode.childNodes;
	var drugData = [];
	for (var i=0; i<drugNodes.length; ++i){
		var node = drugNodes[i];
		drugData[i] = node.id+'='+node.getAttribute('value');
	}
	
	//关联药品部分
	drugsNode = _(pid+'.names');
	
	
	//获取结果
	var data = prescData.join('&')+'&'+drugData.join('&')+'&'+drugsNode.getAttribute('value');
	$.post('p_assess.php', data, function(data){
		alert(data);
		var result = jQuery.parseJSON(data);

		if (result.EMatch){
			if (result.EMatch == 1){
				_(did+'~EMatch').checked = true;
				_(did+'.EMatch').value = '【该药品与诊断相符】';
			}else if(result.EMatch == 0){
				_(did+'~EMatch').checked = false;
				_(did+'.EMatch').value = '【该药品与诊断不符】';
			}
		}
		
		if (result.ECombine){
			if (result.ECombine == 0){
				_(did+'~ECombine').checked = false;
				_(did+'.ECombine').value = '【该药品与其他药品冲突，冲突的有：';
				for (var j=0; j<result['ECombine:'].length; ++j){
					_(did+'.ECombine').value += result['ECombine:'][j]+'、';
				}
				_(did+'.ECombine').value += '】';
			}else if(result.ECombine == 1){
				_(did+'~ECombine').checked = true;
				_(did+'.ECombine').value = '【该药品与其他药品没有冲突】';
				
			}
		}
		
		if (result.EDosage){
			if (result.EDosage == 0){
				_(did+'~EDosage').checked = false;
				_(did+'.EDosage').value = '【该药品的剂量在范围之外，应为：'+result['EDosage:'][0]+' ~ '+result['EDosage:'][1]+'】';
			}else if(result.EDosage == 1){
				_(did+'~EDosage').checked = true;
				_(did+'.EDosage').value = '【该药品的剂量在合适的范围内】';
				
			}
		}
		
		if (result.EFrequency){
			if (result.EFrequency == 0){
				_(did+'~EFrequency').checked = false;
				_(did+'.EFrequency').value = '【该药品的频次在范围之外，应为：'+result['EFrequency:'][0]+' ~ '+result['EFrequency:'][1]+'】';
			}else if(result.EFrequency == 1){
				_(did+'~EFrequency').checked = true;
				_(did+'.EFrequency').value = '【该药品的频次在合适的范围内】';
				
			}
		}
		
		if (result.EMethod){
			if (result.EDosage == 0){
				_(did+'~EDosage').checked = false;
				_(did+'.EDosage').value += '【该药品的给药方式不正确，应为其中之一：'+result['EMethod:'].join('、')+'】';
			}else if(result.EDosage == 1){
				//_(did+'~EDosage').checked = true;
				_(did+'.EDosage').value += '【该药品的给药方式正确】';
				
			}
		}
	});
}
