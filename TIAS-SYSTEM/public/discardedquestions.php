<?php require_once("../includes/sessions.php"); ?>
<?php require_once("../includes/db-connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php $page = "discardedquestions.php"; 
error_reporting(E_ALL);
ini_set("display_errors",1);
$_SESSION["get_query"] = "";
$_SESSION["get_query"] = $_SERVER["QUERY_STRING"];
?>
<style>

.correct{
 
  color:#0EAF0E;
  font-weight:bold;





}
</style>
<!-- Query database for all users -->
<?php 
//where script Start
$where = "";
$key = (isset($_GET['key']))? $_GET['key']:"";
if($key !="")
{
  if($where != "")
    $where .= " AND question_text like '%{$key}%'";
  else
    $where .= " question_text like '%{$key}%' AND is_discarded = 1";
}
$cat_id = (isset($_GET['category']))? $_GET['category']:"";
if($cat_id !="")
{
  if($where != "")
    $where .= " AND category = {$cat_id}";
  else
    $where .= " category = {$cat_id} AND is_discarded = 1";
}
$ans = (isset($_GET['ans']))? intval($_GET['ans']): 0;
if($ans > 0)
{
  
  $ans = ($ans == 2)? 0 : $ans;
  if($where != "")
    $where .= " AND is_answered = {$ans}";
  else
    $where .= " is_answered = {$ans} AND is_discarded = 1";
  $ans = ($ans === 0)? 2 : $ans;
}
//where script ends
$questions_list = get_all_questions_discarded($where); 
$total = $questions_list->num_rows;
parse_str($_SERVER["QUERY_STRING"], $output);
unset($output['page']);
$get_query = http_build_query($output, '', '&amp;');
$targetpage = "manage-questions.php?$get_query"; //your file name
$limit = 50; //how many items to show per page
$p = (isset($_GET['page']))? $_GET['page']:"";
if($p)
  $start = ($p - 1) * $limit; //first item to display on this page
else
  $start = 0;
$questions_list = get_all_questions_limit_discarded($start,$limit,$where);
$pagination_html = pagination($targetpage,$total,$limit,$p);
?>


<?php include('../includes/layouts/header.php'); ?>

<body >

<div id="wrapper">
            
  
  <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    
    <?php include('../includes/layouts/admin-head.php'); ?>

   
    <div class="collapse navbar-collapse navbar-ex1-collapse">
      <?php include('../includes/layouts/admin-side-nav.php'); ?>

      <?php include('../includes/layouts/admin-profile.php'); ?>
    </div>
  </nav>
  <div id="page-wrapper">
   <style type="text/css">
.legend { list-style: none; }
.legend li { float: left; margin-right: 12px; }
.legend span { border: 3px solid #ccc; float: left; width: 18px; height: 18px; margin: 0px; }
.legend spin { border: 3px solid #ccc; float: left; width: 18px; height: 18px; margin: 0px; }



.legend .superawesome { background-color: #0EAF0E ; }



</style>  
 <?php echo user_form_failure_msg(); ?>
 <?php echo user_form_success_msg(); ?>

  
   <div class="row">
      <div class="col-lg-24">
       <div class="panel panel-primary">
              <div class="panel-heading">
                
                <h3 class="panel-title">QUESTION BANK (DISCARDED QUESTIONS) </h3>
              </div>
              <div class="panel-body">
                <form name="search" id="search" action="" method="get">
                  <span class="pull-right"></span>
                     <input type="text" class="form-control" class="pull-right" placeholder="Search by Keyword" name="key" id="key" value="<?php echo $key;?>">
               <BR>

                  <button class="btn btn-SUCCESS pull-right :80%;"  type="button" onclick="document.getElementById('search').submit();"><i class="fa fa-search">FIND</i></button>
                  <button type ="reset" id="reset-search" name="reset-search" value="reset-search" class="btn btn-primary PULL-RIGHT" onclick="window.location.href = 'manage-questions.php';"><li class="fa fa-spinner">Reset</button>&nbsp;
              </div>

</form>
            </div>

            <form name="table_select" id="table_select" action="assign-question.php" method="post">

      
        <div class="clearfix"></div>

       <div class="clearfix"></div>
      
      <div class="col-lg-12">
        <div class="row">
        <div class="panel tbp-panel-inverse">

          <div class="panel-heading">


          <p>  <button type ="button"  id="show-answer" name="show-answer" value="show-answer" class="btn btn-danger pull-right tbp-flush-right ">Hide Choices</button>
       
           </p>
           <p>
           
          TOTAL NUMBER OF QUESTIONS <strong>[<?php echo $total;?>]</strong></strong>

          </div>
          <div class="panel-body">
            <div class="row">
   
              <div class="col-lg-3">

               
             
                  </div>

              </div>
               <div class="col-lg-4">

               <?php echo $pagination_html;  ?>
                </div>

 <div>
             <div style="margin-left:82%;">
                  <label class="control-label">&nbsp;</label>
                  <div class="input-group" style="margin-left:50%;">
                

                 

              </div>
              </div>

   
       
      
  
        
           
            
            <div class="table-responsive" style="clear:both;">
            <table class="table table-bordered table-hover table-striped tablesorter">
              <thead>

                <tr>
                  <th>No.</th>
                  <th>Question</th>
                    <th>Status</th>
                  
                    
                  
                 
                </tr>
              </thead>
              <tbody id="user-rows">     

              <?php
              
        $cnt = 0;
              while($row = mysqli_fetch_assoc($questions_list)) {
    $cnt++;

             $discardStyle = ($row['is_discarded'] == 1)?"color:#ff0000 !important;":"";

              ?>
                  
                  <tr>
                   
                    <td><?php echo $cnt ?></td>
                    <td  ><a style ="cursor:pointer;<?php echo $discardStyle;?>" <?php echo $discardStyle;?> onclick="$('#ans_<?php echo $row['question_id'];?>').toggle();"><?php echo checkAnsFormat($row['question_text']); ?></a></td>
                    <input type="hidden" value="<?php echo $status= $row['is_discarded']; ?>">
                     <td><?php echo question_status($status); ?></td>
                     
                   
                    
                     

                  </tr >
                    <tr id="ans_<?php echo $row['question_id'];?>" >
                    <td colspan="6"><?php echo get_question_answers_html($row['question_id']);?></td>
                    </tr>


                  
                  <div class="modal fade" id="confirm-delete-modal<?php echo htmlentities($row['question_id']); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                          <h4 class="modal-title" id="confirm-delate-label">Warning!</h4>
                        </div>
                        <div class="modal-body">
                          You are about to delete a question and ALL its associated answers. This action will be irreversible.<br />
                          Do you wish to proceed?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                          <a href="delete-questions.php?question_id=<?php echo $row["question_id"]; ?>" class="btn btn-primary">Yes</a>
                        </div>
                      </div>
                    </div>
                  </div><!-- /.modal fade -->
              
              <?php
                }
              ?>
              <?php
                // Release the returned data
                mysqli_free_result($questions_list);
              ?>
        
              </tbody>
            </table>
             <?php echo $pagination_html;  ?>
          </form>
       
          </div><!-- /.table-responsive -->
      </div>
    </div>
	 <script type="text/javascript">
<!--
 $('#assign').modal({ show: true });
 backdrop: 'static',
    keyboard: false 
//-->
</script>
 
  </div><!-- /#page-wrapper -->
     <div class="modal fade" id="assign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="confirm-delate-label">Warning!</h4>
              </div>
              <div class="modal-body">
                You are about to complete this Quiz. This action will be irreversible.<br />
                Do you wish to proceed?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <a onclick="getFormSubmit();" class="btn btn-primary">Yes</a>
              </div>
            </div>
          </div>
        </div><!-- /.modal fade -->

<?php include('../includes/layouts/footer.php'); ?>
   <script>
  $(document).ready(function() {
    $('#selecctall').click(function(event) {  //on click
        if(this.checked) { // check select status
            $('.checkbox1').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"              
            });
        }else{
            $('.checkbox1').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"                      
            });        
        }
    });
    
   
    
   $('#assign-question').click(function(event) {  //on click
       var selected = 0;
       alert("Discarded Question among your selection for assign will be ignored");
            $('.checkbox1').each(function() { //loop through each checkbox
               if(this.checked == true)
                   selected = selected + 1;
            });
        if(selected > 0)
            return true;
        alert("Please Select At least one question.");
        return false;
    });
   
   $('#show-answer').click(function(event) {  //on click
      if($(this).html() == "Hide Choices"){
          $(this).html("Show Choice");
          $( "tr[id^='ans_']" ).hide();
      }
      else {
          $(this).html("Hide Choices");
          $( "tr[id^='ans_']" ).show();
      }
      
    });
});
function assign_question(qs_id,is_discarded){
     if(is_discarded == 1){
       alert("Cant be assigned, this question is discarded");
       return false;
     }
     window.location.href = 'assign-question.php?question_id='+qs_id;
   } 
  </script>
<?php require_once("../includes/db-connection-close.php"); ?>
