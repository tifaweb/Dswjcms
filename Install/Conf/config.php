<?php

return array(
	//'配置项'=>'配置值'
	//'SHOW_PAGE_TRACE'=>true,//开启页面Trace
	'TMPL_L_DELIM'=>'<{', //修改左定界符
	'TMPL_R_DELIM'=>'}>', //修改右定界符
	'TMPL_PARSE_STRING'=>array(
        '__PUBLIC__'=>__ROOT__.'/Install/Tpl/Public',
    ),
);

?>