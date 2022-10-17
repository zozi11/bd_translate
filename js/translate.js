//翻译所有指定的标签内文本，调用方式为 translate_tags('p,li,div,h3,h4,h5')
function translate_tags(tags_str,from,to){
    var tag_arr=tags_str.split(',');
	for(i=0;i<tag_arr.length;i++){
        translate_one_tag(tag_arr[i],from,to);
	}
}
//   translate_one_tag() 函数功能：获取指定标签内的所有文本，生成字符串，统一提交翻译，返回翻译的结果
function translate_one_tag(htmltag,from,to){
    var new_arr=new Array();
    $(htmltag).each(function(){
        var str=$(this).html();
        if(str.indexOf("<") == -1 && str){
        //保存原始文本，防止多次翻译后以讹传讹。
        if(!$(this).attr('old_str')){$(this).attr('old_str',str);}else{str=$(this).attr('old_str');}
        
        //如果内容里含有其它标签则不作处理，避免翻译后破坏样式。如果内容包含<br> 应该先进行替换，翻译后再替换回<br>
            str=str.replace('<br>','zzzzz');
            
            if(str.indexOf("<") == -1 && str && str.trim()!=''){
                new_arr.push(str);
            }
        }
    });
    //循环完毕，此处的数组就是待翻译的数组，翻译后返回一个新的数组，然后将这两个数组同时返回
    //调用后台API进行翻译,以ajax请求，异步模式返回
        if(!new_arr || new_arr.length==0){return;}
		$.ajax({ 
		    type:'post',
		    url:'ajax_translate.php',
		    data:{ 
				'funname':'do_translate',
                'to':to,
                'from':from,
				'htmltag':htmltag,
				'wait_arr':new_arr,
			}, 
		    cache:false, 
		    dataType:'json', 
		    success:function(r){
		    		
		    	if (r.status == true) {
		    		var result_arr=r.result_arr;
		    		var old_arr=r.old_arr;//因为是异步，避免全局变量内容改变，所以传入传出
		    		var tohtmltag=r.htmltag;
		    		
		    		//循环结果数组，根据索引进行替换
		    		    var tmp_i=0;
                        $(tohtmltag).each(function(){
                            if($(this).attr('old_str')){str=$(this).attr('old_str');}else{str=$(this).html();}
                            if(str.indexOf("<") == -1 && str && str.trim()!=''){
                                str=decodeURIComponent(result_arr[tmp_i].replace(/\+/g, '%20'));
                                str=str.replace('zzzzz','<br>');
                                if(str){$(this).html(str);}
                                tmp_i++;
                            }
                        });
		    	}else{
		    		//alert(r.msg);
		    		console.log(r.msg);
		    	}
		    }
		});
	}