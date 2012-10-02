<?php /* Smarty version 3.0rc1, created on 2012-10-02 10:06:27
         compiled from "/vol0/www/html/forecast/ywang/hurricane2/smarty/templates/mobile_prod.tpl" */ ?>
<?php /*%%SmartyHeaderCode:196811458506b02f30b24a2-30992166%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fb75cee661a396a8941146dafad190b12f67b55a' => 
    array (
      0 => '/vol0/www/html/forecast/ywang/hurricane2/smarty/templates/mobile_prod.tpl',
      1 => 1347897393,
    ),
  ),
  'nocache_hash' => '196811458506b02f30b24a2-30992166',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_arr2str')) include '/vol0/www/html/forecast/ywang/Smarty/plugins/function.arr2str.php';
?><!DOCTYPE html>
<html>
  <head>

    <?php echo $_smarty_tpl->getVariable('common_header')->value;?>


    <script type="text/javascript">

      var casenames = <?php echo smarty_function_arr2str(array('array'=>$_smarty_tpl->getVariable('casenames')->value),$_smarty_tpl->smarty,$_smarty_tpl);?>
;
      var casetypes = <?php echo $_smarty_tpl->getVariable('types')->value;?>
;

      var fieldnames = <?php echo smarty_function_arr2str(array('array'=>$_smarty_tpl->getVariable('fieldnames')->value),$_smarty_tpl->smarty,$_smarty_tpl);?>
;

      var casefields = new Array();
      casefields = {
      <?php  $_smarty_tpl->tpl_vars['mycase'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('casenames')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if (count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['mycase']->key => $_smarty_tpl->tpl_vars['mycase']->value){
?>
        <?php echo $_smarty_tpl->tpl_vars['mycase']->value;?>
:<?php echo $_smarty_tpl->getVariable('fields')->value[$_smarty_tpl->tpl_vars['mycase']->value];?>
,
      <?php }} ?>
      };

      var defdate = '<?php echo $_smarty_tpl->getVariable('defdate')->value;?>
';
      var defcase = '<?php echo $_smarty_tpl->getVariable('defcase')->value;?>
';
      var deffield = '<?php echo $_smarty_tpl->getVariable('deffield')->value;?>
';

      // Image animation requested
      function prodClicked(prod) {
        //alert(prod+" for "+defcase+" on "+defdate+" clicked!");
        deffield = prod;
        var dstr = defdate.replace(/-/g,'');
        window.location.href = "mobile.php/"+dstr+"/"+defcase+"/"+deffield+"/";
      }

      // Forecast case changed
      function caseClicked(inst) {
        //alert($(this).attr("id")+" clicked!");
        defcase = $(this).attr("id");
        checkProdList();
      }

      // Check product list for active
      function checkProdList() {
        for (var i = 0, len= fieldnames.length; i< len; i++) {
          var prod = fieldnames[i];
          if ( $.inArray(prod, casefields[defcase]) < 0) {
            $( "li#"+prod ).attr('class','liVoid');
          } else {
            $( "li#"+prod ).attr('class','liActive');
          }
        }
        $ ("li#"+deffield ).attr('class','liHlght');

        $('#'+defcase).attr('checked','checked').button("refresh");

        //i=$.inArray(defcase,casenames);
        //alert(i);
        //if ( i > -1) {
        //  $('input:radio[name=case]:nth(i)').attr('checked',true);
        //  $('#'+defcase).button("refresh");
        //  alert($('input:radio[name=case]:nth(i)').attr('checked'));
        //
        //}

      }

      // onLoad function
      $(function() {
        for ( var i = 0, len=casenames.length; i< len; i++ ) {
          var fcase = casenames[i];
          if ( $.inArray(fcase,casetypes) < 0) {
            $( "#"+fcase ).button({disabled: true});
          }
        };

        $( "input:radio[name=case]" ).click(caseClicked);
        $( "#casebutton" ).buttonset()

        checkProdList();
      });

      // Toggle Back button image
      function toggleMenu() {
        $('#header .leftButton').toggleClass('pressed');
      }

      function goBack() {
        //history.go(-1);
        var dstr = defdate.replace(/-/g,'');
        window.location.href = "mobile.php/"+dstr;
      }

    </script>
  </head>

  <body>
     <div id="header">
       <a class="back" href="javascript:goBack()">Back</a>
       <h2> Products on <?php echo $_smarty_tpl->getVariable('defdate')->value;?>
</h2>
     </div>

     <div id="casebutton" align="center" style="font-size: 65%;">
       <input type="radio" id="gfs00Z" name="case" /><label for="gfs00Z">GFS  00Z</label>
       <input type="radio" id="gfs12Z" name="case" /><label for="gfs12Z">GFS  12Z</label>
       <input type="radio" id="ekf00Z" name="case" /><label for="ekf00Z">EnKF 00Z</label>
       <input type="radio" id="ekf12Z" name="case" /><label for="ekf12Z">EnKF 12Z</label>
     </div>

     <div id="products">
        <ul>

        <?php  $_smarty_tpl->tpl_vars['field'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('fieldnames')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if (count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['field']->key => $_smarty_tpl->tpl_vars['field']->value){
?>
            <li class="liActive" id="<?php echo $_smarty_tpl->tpl_vars['field']->value;?>
">
              <a href="javascript:prodClicked('<?php echo $_smarty_tpl->tpl_vars['field']->value;?>
');"><?php echo $_smarty_tpl->getVariable('fielddes')->value[$_smarty_tpl->tpl_vars['field']->value]['title'];?>
</a>
          <?php if ($_smarty_tpl->getVariable('fielddes')->value[$_smarty_tpl->tpl_vars['field']->value]['note']!=''){?> <br><span class="note">(<?php echo $_smarty_tpl->getVariable('fielddes')->value[$_smarty_tpl->tpl_vars['field']->value]['note'];?>
)</span> <?php }?>
            </li>
        <?php }} ?>

        </ul>
     </div>

</div>

  </body>
</html>