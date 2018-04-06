<extend name="Common:base" />

<block name="title">[TITLE]</block>

<block name="cusStyle">
    [STYLE]
</block>

<block name="menu">
    <include file="[TABLENAME]/menu" />
</block>

<block name="main">
    <div class="pageheader">
        <h1 class="pagetitle">[TITLE]</h1>
    </div>

    <div id="contentwrapper" class="contentwrapper">
        <form class="stdform stdform2" action="{:U('[TABLENAME]/edit[TABLENAME]')}" method="POST" id="JgoodsForm">
            <input type="hidden" name="id" value="{$vo.id}">
[ROWS]
            <div class="line-dete">
                <label></label>
                <span class="field">
                    <input type="submit" class="stdbtn" value="保存"/>
                </span>
            </div>
        </form>
    </div>
</block>
<block name="script">
[SCRIPT]
<script type="text/javascript">
    $(function(){
[JQUERY]
    })
</script>
</block>
