<!DOCTYPE html>
<html>
  <head>

    {$common_header}

    <script type="text/javascript">

      var casenames = {arr2str array=$casenames};
      var casetypes = {$types};

      var fieldnames = {arr2str array=$fieldnames};

      var casefields = new Array();
      casefields = {ldelim}
      {foreach from=$casenames item=mycase}
        {$mycase}:{$fields[$mycase]},
      {/foreach}
      {rdelim};

      var defdate = '{$defdate}';
      var defcase = '{$defcase}';
      var deffield = '{$deffield}';

      // Image animation requested
      function prodClicked(prod) {ldelim}
        //alert(prod+" for "+defcase+" on "+defdate+" clicked!");
        deffield = prod;
        var dstr = defdate.replace(/-/g,'');
        window.location.href = "mobile.php/"+dstr+"/"+defcase+"/"+deffield+"/";
      {rdelim}

      // Forecast case changed
      function caseClicked(inst) {ldelim}
        //alert($(this).attr("id")+" clicked!");
        defcase = $(this).attr("id");
        checkProdList();
      {rdelim}

      // Check product list for active
      function checkProdList() {ldelim}
        for (var i = 0, len= fieldnames.length; i< len; i++) {ldelim}
          var prod = fieldnames[i];
          if ( $.inArray(prod, casefields[defcase]) < 0) {ldelim}
            $( "li#"+prod ).attr('class','liVoid');
          {rdelim} else {ldelim}
            $( "li#"+prod ).attr('class','liActive');
          {rdelim}
        {rdelim}
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

      {rdelim}

      // onLoad function
      $(function() {ldelim}
        for ( var i = 0, len=casenames.length; i< len; i++ ) {ldelim}
          var fcase = casenames[i];
          if ( $.inArray(fcase,casetypes) < 0) {ldelim}
            $( "#"+fcase ).button({ldelim}disabled: true{rdelim});
          {rdelim}
        {rdelim};

        $( "input:radio[name=case]" ).click(caseClicked);
        $( "#casebutton" ).buttonset()

        checkProdList();
      {rdelim});

      // Toggle Back button image
      function toggleMenu() {
        $('#header .leftButton').toggleClass('pressed');
      }

      function goBack() {ldelim}
        //history.go(-1);
        var dstr = defdate.replace(/-/g,'');
        window.location.href = "mobile.php/"+dstr;
      {rdelim}

    </script>
  </head>

  <body>
     <div id="header">
       <a class="back" href="javascript:goBack()">Back</a>
       <h2> Products on {$defdate}</h2>
     </div>

     <div id="casebutton" align="center" style="font-size: 65%;">
       <input type="radio" id="gfs00Z" name="case" /><label for="gfs00Z">GFS  00Z</label>
       <input type="radio" id="gfs12Z" name="case" /><label for="gfs12Z">GFS  12Z</label>
       <input type="radio" id="ekf00Z" name="case" /><label for="ekf00Z">EnKF 00Z</label>
       <input type="radio" id="ekf12Z" name="case" /><label for="ekf12Z">EnKF 12Z</label>
     </div>

     <div id="products">
        <ul>

        {foreach from=$fieldnames item=field}
            <li class="liActive" id="{$field}">
              <a href="javascript:prodClicked('{$field}');">{$fielddes[$field].title}</a>
          {if $fielddes[$field].note ne ""} <br><span class="note">({$fielddes[$field].note})</span> {/if}
            </li>
        {/foreach}

        </ul>
     </div>

</div>

  </body>
</html>