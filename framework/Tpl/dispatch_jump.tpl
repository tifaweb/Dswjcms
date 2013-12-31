<if condition="$jump_url eq 1 ">
<include file="./Tpl/Home/Index/header.html" />
<elseif condition="$jump_url eq 2"/>
<include file="./Tpl/Win/Index/header.html" />
<else/>
<include file="./Tpl/Admin/Index/header.html" />
</if>
<!--头部 end -->
<!-- container start --> 
<div class="row-fluid index">
    <div class="jump_top span12">
    	<div class="span12 loan_search center">
          	<present name="message">
            <p class="green"><?php echo($message); ?></p>
            <else/>
            <p class="red"><?php echo($error); ?></p>
            </present>
            <p class="jump">
            页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b>
            </p>
            </div>
        </div>
    </div>
</div>
<!-- container end -->

<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
<!--底部  start-->
<if condition="$jump_url eq 1 ">
<include file="./Tpl/Home/Index/footer.html" />
<elseif condition="$jump_url eq 2"/>
<include file="./Tpl/Win/Index/footer.html" />
<else/>
<include file="./Tpl/Admin/Index/footer.html" />
</if>