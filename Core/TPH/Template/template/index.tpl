<extend name="Common:base" />

<block name="title">[TITLE]</block>

<block name="cusStyle">
    [STYLE]
</block>

<block name="menu">
    <include file="[TABLENAME]:menu" />
</block>

<block name="main">
    <div class="pageheader">
        <h1 class="pagetitle">[TITLE]</h1>
    </div>
    <div class="contentwrapper">
[FORM]
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
            <tr>
                [TH]
                <th width="20%">操作</th>
            </tr>
            <empty name="list">
                <tr>
                    <td colspan="[COLSPAN]">目前没有数据~！</td>
                </tr>
            <else />
                <volist name="list" id="vo">
                    <tr>
                        [TD]
                        <td>
                            [TD_MENU]
                        </td>
                    </tr>
                </volist>
                <tr>
                    <td colspan="[COLSPAN]">
                        <div class="page-box">{$show}</div>
                    </td>
                </tr>
            </empty>
        </table>
    </div>
</block>
<block name="script">
[SCRIPT]
</block>
