var Menu = {
	form : function(id,rid,level){		//id为要修改的菜单的id，rid为rootid即父id
		var form_title = '添加菜单';
		var url = '/admin/conf/add_menu_form';
		var ajaxUrl = '/admin/conf/save_menu_form';
		if(id){
			form_title = '修改菜单项';
			ajaxUrl = '/admin/conf/edit_menu';
			url = url + '/' + id;
		}
		art.dialog.open(url,{
			title 	: form_title,
			ok		: function(){
				var iframe = this.iframe.contentWindow;
				if(!iframe.document.body){
					$.dialog({content:'form 未加载完成'});
					return false;
				}
				var form = iframe.document.getElementById('menu-form');
				formData = {};
				var fields = [
					'name',
					'icon',
					// 'root',
					'sort',
					'path',
				];
				for(var x in fields){
					formData[fields[x]] = $.trim(form[fields[x]].value);
				}
				if(rid != '' && id == ''){		//rid 位 rootid
					formData['root'] = rid;
				}else{
					formData['root'] = 0;
				}

				if( id != ''){
					formData['id'] = id;
				}

				formData['level'] = level + 1;
				if(rid !='' && rid != 0)
				{
					formData['group'] = rid;
				}

				if(Check.menu_form(formData, form)){
					$.ajax({
						type 	: 'GET',
						url 	: ajaxUrl,
						dataType: 'json',
						data 	: formData,
						success : function(rp){
							location.reload();
						}
					});
				}
				return false;
			},
			cancel 	: true,
			lock 	: true,
			width 	: 320,
			drag 	: false,
			resize 	: false,
		});

	},
	add_son_menu : function(id,group,level){
		Menu.form('',id,level);
	},
	edit : function(id){
		Menu.form(id);
	},
	del : function(id){
		art.dialog({
			lock 	: true,
			content : '删除的目录包括其子目录会<br/>一并删除，且删除后无法恢复！',
			ok 		: function(){
				$.ajax({
					type 	: 'GET',
					url 	: '/admin/conf/del_menu_item/'+id,
					dataType: 'json',
					success : function(rp){
						art.dialog.tips(rp.message, 2);
						location.reload();
					}
				});
			},
			cancel 	: true
		});
	},
	// init : function(menuTree){	//初始化菜单列表
	// 	var item;
	// 	var key;
	// 	for(key in menuTree)
	// 	{
	// 		item = menuTree[key];
	// 		while(item.children != ''){
	// 			alert(item);
	// 			// if(typeof item.children != 'undefined'){
	// 			// 	alert(1111);
	// 			// 	item = item.children;
	// 			// }else{
	// 			// 	return;
	// 			// }
	// 		}
	// 	}
	// },
};

var Check = {
	menu_form : function(data, form){
		if(data.name == ''){
			art.dialog.tips('菜单名不可为空！', 2);
			form['name'].focus();
			return false;
		}
		if(data.path == ''){
			art.dialog.tips('路径不可为空！', 2);
			form['path'].focus();
			return false;
		}
		return true;
	},
};

$(document).ready(function(){
	$.ajax({
		type 	: 'GET',
		url 	: '/admin/conf/menu',
		dataType: 'json',
		success : function(data){
			Menu.init(data);
		}
	});
});